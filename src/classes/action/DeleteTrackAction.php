<?php
declare(strict_types=1);

namespace iutnc\deefy\action;

use iutnc\deefy\repository\DeefyRepository;
use function iutnc\deefy\utils\requireAuth;
use function iutnc\deefy\utils\requirePlaylistAccess;

class DeleteTrackAction extends Action
{
    public function execute(): string
    {
        requireAuth();

        // On récupère les IDs envoyés depuis le formulaire
        $pid = (int)($_POST['pid'] ?? $_GET['pid'] ?? ($_SESSION['current_playlist_id'] ?? 0));
        $tid = (int)($_POST['tid'] ?? $_GET['tid'] ?? 0);

        if ($pid <= 0 || $tid <= 0) {
            return '<p>Suppression impossible : identifiant invalide.</p>';
        }

        // Vérifier les droits sur la playlist
        requirePlaylistAccess($pid);

        // Suppression via le repository
        $repo = DeefyRepository::getInstance();
        try {
            $repo->removeTrackFromPlaylist($pid, $tid);
        } catch (\Throwable $e) {
            return '<p style="color:red">Erreur lors de la suppression de la piste.</p>';
        }

        // Redirige vers la playlist après suppression
        header('Location: ?action=display-playlist&id=' . $pid);
        exit;
    }
}