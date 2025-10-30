<?php
declare(strict_types=1);

namespace iutnc\deefy\render;

use iutnc\deefy\audio\tracks\PodcastTrack;

class PodcastRenderer extends AudioTrackRenderer
{
    public function __construct(PodcastTrack $track)
    {
        parent::__construct($track);
    }

    public function render(int $selector = self::SHORT): string
    {
        /** @var PodcastTrack $t */
        $t = $this->track;
        $title = htmlspecialchars($t->getTitre(), ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
        $author = htmlspecialchars($t->getAuteur(), ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
        $date = $t->getDate()?->format('Y-m-d') ?? '—';

        $meta = "<div><strong>{$title}</strong><br>Auteur: {$author}<br>Date: {$date}</div>";

        if ($selector === Renderer::LONG) {
            return "<div class=\"podcast-track\">{$meta}<div>{$this->audioPlayer()}</div></div>";
        }
        return "<div class=\"podcast-track-short\"><strong>{$title}</strong> — {$author}</div>";
    }
}
