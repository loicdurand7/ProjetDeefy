<?php
declare(strict_types=1);

namespace iutnc\deefy\action;

use iutnc\deefy\auth\AuthnProvider;
use iutnc\deefy\repository\DeefyRepository;
use function iutnc\deefy\utils\e;
use function iutnc\deefy\utils\requireAuth; // bloquer l'accès aux non connectés

class MesPlaylistsAction extends Action {

    public function execute(): string {
        requireAuth(); // bloquer l'accès aux non connectés

        // user connecté
        $user = AuthnProvider::getSignedInUser();
        $uid = (int)$user['id'];

        // playlists du user
        $repo = DeefyRepository::getInstance();
        $playlists = $repo->listPlaylistsByUser($uid);

        // Si aucune playlist
        if (empty($playlists)) {
            return "<p>Aucune playlist trouvée. <a href='?action=add-playlist'>Créer une playlist</a></p>";
        }

        // Affichage des playlists 
        $html = "<h2>Mes Playlists</h2><ul>";
        foreach ($playlists as $pl) {
            $pid = (int)$pl['id'];
            $html .= "<li><a href='?action=display-playlist&id={$pid}'>".e($pl['nom'])."</a></li>";
        }
        $html .= "</ul>";

        return $html;
    }
}