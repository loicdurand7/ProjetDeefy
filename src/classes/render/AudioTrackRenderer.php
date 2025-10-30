<?php
declare(strict_types=1);

namespace iutnc\deefy\render;

use iutnc\deefy\audio\tracks\AudioTrack;

abstract class AudioTrackRenderer implements Renderer
{
    protected AudioTrack $track;

    public function __construct(AudioTrack $track)
    {
        $this->track = $track;
    }

    protected function audioPlayer(): string
    {
        $src = htmlspecialchars($this->track->getFilename(), ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
        return "<audio controls src=\"{$src}\">Your browser does not support the audio element.</audio>";
    }
}
