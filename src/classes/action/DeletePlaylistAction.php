<?php
declare(strict_types=1);

namespace iutnc\deefy\action;

use iutnc\deefy\repository\DeefyRepository;
use function iutnc\deefy\utils\requireAuth;
use function iutnc\deefy\utils\requirePlaylistAccess;

class DeletePlaylistAction extends Action
{
    public function execute(): string
    {
        requireAuth();

        // Récupération de l’ID de la playlist à supprimer
        $pid = (int)($_POST['id'] ?? $_GET['id'] ?? 0);
        if ($pid <= 0) {
            return '<p>Playlist introuvable.</p>';
        }

        // Vérifie que la playlist appartient bien à l’utilisateur
        requirePlaylistAccess($pid);

        // On recupère l'instance du repository pour effectuer la suppression 
        $repo = DeefyRepository::getInstance();

        try {
            $repo->removePlaylist($pid);
            // Si c’était la courante, on la retire de la session
            if ((int)($_SESSION['current_playlist_id'] ?? 0) === $pid) {
                unset($_SESSION['current_playlist_id']);
            }
        } catch (\Throwable $e) {
            return '<p style="color:red">Erreur lors de la suppression de la playlist.</p>';
        }

        // Redirection vers la liste des playlists
        header('Location: ?action=mes-playlists');
        exit;
    }
}