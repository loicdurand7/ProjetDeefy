<?php
declare(strict_types=1);

// Render d'une AudioList (Playlist)

namespace iutnc\deefy\render;

use iutnc\deefy\audio\lists\AudioList;

class AudioListRenderer implements Renderer
{
    private AudioList $list;

    public function __construct(AudioList $list) {
        $this->list = $list;
    }

    public function render(int $selector = self::SHORT): string {
        // récupérer et sécuriser les propriétés de la liste
        $nom  = htmlspecialchars($this->list->__get('nom'));
        $nb   = (int) $this->list->__get('nbpistes');
        $duree= (int) $this->list->__get('duration');

        $html = "<h2>Playlist : {$nom}</h2>";
        $html .= "<p>{$nb} piste(s) – {$duree} secondes</p>";

        return $html;
    }
}
