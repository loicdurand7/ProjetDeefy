<?php
declare(strict_types=1);

namespace iutnc\deefy\action;

use iutnc\deefy\repository\DeefyRepository;
use function iutnc\deefy\utils\requireAuth;
use function iutnc\deefy\utils\requirePlaylistAccess;
use function iutnc\deefy\utils\e;

class DisplayPlaylistAction extends Action
{
    public function execute(): string
    {
        requireAuth();

        // 1) Prendre l'id fourni sinon retomber sur la courante
        $idParam = $_GET['id'] ?? ($_GET['pid'] ?? null);
        if ($idParam === null) {
            $pid = (int)($_SESSION['current_playlist_id'] ?? 0);
            if ($pid <= 0) {
                return '<p>Aucune playlist courante.</p>
                        <p><a href="?action=add-playlist">Créer une playlist</a> ou <a href="?action=mes-playlists">Voir mes playlists</a></p>';
            }
        } else {
            $pid = (int)$idParam;
        }

        // 2) Sécurité + MAJ de la courante
        requirePlaylistAccess($pid);
        $_SESSION['current_playlist_id'] = $pid;

        // 3) Lecture BD d'UNE playlist + pistes
        $repo = DeefyRepository::getInstance();

        $pl = $repo->getPlaylist($pid);
        if (!$pl) {
            return '<p>Playlist introuvable.</p>';
        }

        $tracks = $repo->listTracksOfPlaylist($pid);

        // 4) Rendu
        $html  = '<h2>Playlist : ' . e($pl['nom'] ?? '???') . '</h2>';

        if (empty($tracks)) {
            $html .= '<p class="muted">Aucune piste pour le moment.</p>';
        } else {
            $html .= '<ol>';
            foreach ($tracks as $t) {
                $titre    = e($t['titre'] ?? '[sans titre]');
                $artiste  = e($t['artiste_album'] ?? '—');
                $duree    = format_mmss((int)($t['duree'] ?? 0));
                $filename = $t['filename'] ?? null;
                $tid      = isset($t['id']) ? (int)$t['id'] : 0; // <-- id de la track (nécessaire pour supprimer)

                $html .= '<li><strong>'.$titre.'</strong> — '.$artiste.' <em>('.$duree.')</em>';

                if (!empty($filename)) {
                    $src = 'audio/' . e($filename);
                    $html .= '<br><audio controls src="'.$src.'"></audio>';
                }

                // === Bouton supprimer la piste (POST) ===
                if ($tid > 0) {
                    $html .= '
                        <form method="post" action="?action=delete-track" style="display:inline;margin-left:8px;">
                          <input type="hidden" name="pid" value="'.$pid.'">
                          <input type="hidden" name="tid" value="'.$tid.'">
                          <button type="submit" onclick="return confirm(\'Supprimer cette piste ?\')">Supprimer</button>
                        </form>';
                }

                $html .= '</li>';
            }
            $html .= '</ol>';
        }

        $html .= '<p><a href="?action=add-track">Ajouter une piste</a></p>';
        $html .= '<p><a href="?action=mes-playlists">Voir mes playlists</a></p>';

        // === Bouton supprimer la playlist (POST) ===
        $html .= '
            <form method="post" action="?action=delete-playlist" onsubmit="return confirm(\'Supprimer définitivement cette playlist ?\')">
              <input type="hidden" name="id" value="'.$pid.'">
              <button type="submit" style="color:#b00020">Supprimer cette playlist</button>
            </form>';

        return $html;
    }
}

/** Formate des secondes en mm:ss */
function format_mmss(int $s): string {
    $m = intdiv($s, 60);
    $r = $s % 60;
    return sprintf('%d:%02d', $m, $r);
}