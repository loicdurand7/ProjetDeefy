<?php
declare(strict_types=1);

namespace iutnc\deefy\action;

use function iutnc\deefy\utils\requireAuth;

class CurrentPlaylistAction extends Action
{
    public function execute(): string
    {
        requireAuth();

        $pid = (int)($_SESSION['current_playlist_id'] ?? 0);
        if ($pid <= 0) {
            return '<p>Aucune playlist courante.</p>
                    <p><a href="?action=add-playlist">Créer une playlist</a> ou <a href="?action=mes-playlists">Voir mes playlists</a></p>';
        }

        // Déléguer à l’afficheur sans passer d’id → il utilisera la “courante”
        $display = new DisplayPlaylistAction($this->http_method);
        return $display->execute();
    }
}