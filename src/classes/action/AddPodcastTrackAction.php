<?php
declare(strict_types=1);

namespace iutnc\deefy\action;

use iutnc\deefy\repository\DeefyRepository;
use function iutnc\deefy\utils\requireAuth;
use function iutnc\deefy\utils\requirePlaylistAccess;
use function iutnc\deefy\utils\e;

class AddPodcastTrackAction extends Action
{
    public function execute(): string
    {
        requireAuth();

        // On va modifier la playlist courante (car chaque fois que l'utilisateur clique sur une playlist elle devient la courante)
        $pid = (int)($_SESSION['current_playlist_id'] ?? 0);
        if ($pid <= 0) {
            return '<p>Aucune playlist courante.</p>
                    <p><a href="?action=mes-playlists">Voir mes playlists</a> ou <a href="?action=add-playlist">Créer une playlist</a></p>';
        }
        requirePlaylistAccess($pid);

        // GET : Formulaire d’ajout
        if ($this->http_method === 'GET') {
            return <<<HTML
            <h2>Ajouter une piste (podcast)</h2>
            <form method="post" action="?action=add-track" enctype="multipart/form-data">
              <label>Titre : <input type="text" name="title" required></label><br>
              <label>Auteur : <input type="text" name="author"></label><br>
              <label>Durée (secondes) : <input type="number" name="duration" min="1" required></label><br>
              <label>Fichier audio (mp3) : <input type="file" name="userfile" accept=".mp3"></label><br>
              <button type="submit">Ajouter</button>
            </form>
            <p><a href="?action=display-playlist&id={$pid}">Retour à la playlist</a></p>
            HTML;
        }

        // POST : Traitement
        // Récup inputs
        $title    = trim((string)($_POST['title'] ?? ''));
        $author   = trim((string)($_POST['author'] ?? ''));
        $duration = (int)($_POST['duration'] ?? 0);

        // Verifications simple pour le titre et la durée
        if ($title === '' || $duration <= 0) {
            return '<p style="color:red">Titre et durée sont obligatoires.</p>'
                . '<p><a href="?action=add-track">Réessayer</a></p>';
        }

        // Upload MP3 
        $filename = null;
        if (!empty($_FILES['userfile']) && $_FILES['userfile']['error'] === UPLOAD_ERR_OK) {
            $file = $_FILES['userfile'];
            $ext  = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
            $type = $file['type'] ?? '';

            // Vérification type et extension
            if ($ext !== 'mp3' || $type !== 'audio/mpeg') {
                return '<p style="color:red">Seuls les fichiers MP3 (audio/mpeg) sont acceptés.</p>'
                    . '<p><a href="?action=add-track">Réessayer</a></p>';
            }

            // Dossier de destination (ici audio)
            $targetDir = dirname(__DIR__, 3) . '/audio';
            if (!is_dir($targetDir)) {
                @mkdir($targetDir, 0777, true);
            }

            $newName    = uniqid('track_', true) . '.mp3';
            $targetPath = $targetDir . '/' . $newName;

            // Si échec
            if (!@move_uploaded_file($file['tmp_name'], $targetPath)) {
                return '<p style="color:red">Impossible de sauvegarder le fichier.</p>'
                    . '<p><a href="?action=add-track">Réessayer</a></p>';
            }

            $filename = $newName; // ce nom sera stocké en BD (colonne track.filename)
        }

        // Insertion BD via le repository
        try {
            $repo = DeefyRepository::getInstance();

            // Nom EXACT des colonnes de la BD
            $repo->addNewTrackToPlaylist($pid, [
                'titre'          => $title,
                'genre'          => null,
                'duree'          => $duration,
                'filename'       => $filename,        // peut être null si pas d’upload
                'type'           => 'P',               // podcast
                'artiste_album'  => null,
                'titre_album'    => null,
                'annee_album'    => null,
                'numero_album'   => null,
                'auteur_podcast' => ($author !== '' ? $author : null),
                'date_posdcast'  => null,              // respecter l’orthographe foireuse du dump
            ]);

            // Retour vers l’affichage de la playlist courante
            header('Location: ?action=display-playlist&id=' . $pid);
            exit;

        } catch (\Throwable $e) {
            // On renvoie une erreur simple, pas besoin de details à l'écran juste ce qu'il faut 
            return '<p style="color:red">Erreur lors de l’ajout de la piste.</p>'
                . '<p><a href="?action=add-track">Réessayer</a></p>';
        }
    }
}
