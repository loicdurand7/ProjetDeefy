<?php

declare(strict_types=1);

namespace iutnc\deefy\action;

use iutnc\deefy\repository\DeefyRepository;
use function iutnc\deefy\utils\requireAuth;
use function iutnc\deefy\utils\e;

class AddPlaylistAction extends Action {
     /**
     * GET  : affiche un petit formulaire "nom"
     * POST : crée la playlist en BD, la lie à l'utilisateur, définit la playlist courante
     */
    public function execute(): string
    {
        if ($this->http_method === 'GET') {
            return <<<HTML
<h2>Créer une playlist</h2>
<form method="post" action="?action=add-playlist">
  <label>Nom de la playlist</label><br>
  <input type="text" name="nomP" required>
  <br><button type="submit">Créer</button>
</form>
HTML;
        }

        // POST
        $raw = $_POST['nom'] ?? ($_POST['nomP'] ?? '');
        $nom = trim((string)$raw);

        if ($nom === '') {
            return '<p style="color:red">Nom invalide</p>' . $this->execute();
        }

 // Récupère l'id utilisateur (compat: user_id direct OU depuis $_SESSION['user'])
        $uid = (int)($_SESSION['user_id'] ?? 0);
        if ($uid <= 0 && isset($_SESSION['user'])) {
            $u = @unserialize($_SESSION['user']);
            if (is_array($u) && isset($u['id'])) $uid = (int)$u['id'];
        }
        if ($uid <= 0) {
            // Par sécurité si jamais la session n'a pas été initialisée comme prévu
            http_response_code(401);
            return "<p>Veuillez vous reconnecter.</p>";
        }

        // Création en BD + liaison user2playlist
        $repo = DeefyRepository::getInstance();
        // création playist en fonction de l'utilisateur
        $pid  = $repo->createPlaylist($nom, $uid);

        // Définit la playlist courante (une seule)
        $_SESSION['current_playlist_id'] = $pid;
        header('Location: ?action=display-playlist&id=' . $pid);
        exit;

        // Retour simple + liens utiles
        $nomAff = e($nom);
        $pidAff = (int)$pid;

        // Affichage HTML avec les liens utiles
        return <<<HTML
<p>Playlist <strong>{$nomAff}</strong> créée avec succès.</p>
<p>
  <a href="?action=display-playlist&id={$pidAff}">Ouvrir la playlist</a><br>
  <a href="?action=add-track">Ajouter une piste</a><br>
  <a href="?action=mes-playlists">Mes playlists</a>
</p>
HTML;
    }
}
