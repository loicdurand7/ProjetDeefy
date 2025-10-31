<?php
declare(strict_types=1);

namespace iutnc\deefy\action;

use function iutnc\deefy\utils\requireAuth;

class CurrentPlaylistAction extends Action
{
    public function execute(): string
    {
        requireAuth();

        // On utilise la playlist courante en session 
        $pid = (int)($_SESSION['current_playlist_id'] ?? 0);
        // Si il n'y en a pas on renvoi un message simple + des options 
        if ($pid <= 0) {
            return '<p>Aucune playlist courante.</p>
                    <p><a href="?action=add-playlist">Créer une playlist</a> ou <a href="?action=mes-playlists">Voir mes playlists</a></p>';
        }

        // On envoie l’affichage de la playlist courante 
        // à la classe DisplayPlaylistAction (réutilisation du code d’affichage pour éviter la duplication de code )
        $display = new DisplayPlaylistAction($this->http_method);
        return $display->execute();
    }
}