<?php
declare(strict_types=1);

namespace iutnc\deefy\render;

use iutnc\deefy\audio\tracks\AlbumTrack;

class AlbumTrackRenderer extends AudioTrackRenderer
{
    public function __construct(AlbumTrack $track)
    {
        parent::__construct($track);
    }

    public function render(int $selector = self::SHORT): string
    {
        /** @var AlbumTrack $t */
        $t = $this->track;
        $title = htmlspecialchars($t->getTitre(), ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
        $album = htmlspecialchars($t->getAlbum(), ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
        $artist = $t->getArtiste() ? htmlspecialchars($t->getArtiste(), ENT_QUOTES, 'UTF-8') : '—';
        $num = $t->getTrackNumber();
        $numStr = $num !== null ? (string)$num : '—';

        $meta = "<div><strong>{$title}</strong><br>Album: {$album}<br>Artiste: {$artist}<br>Piste: {$numStr}</div>";

        if ($selector === Renderer::LONG) {
            return "<div class=\"album-track\">{$meta}<div>{$this->audioPlayer()}</div></div>";
        }
        return "<div class=\"album-track-short\"><strong>{$title}</strong> — {$artist}</div>";
    }
}
