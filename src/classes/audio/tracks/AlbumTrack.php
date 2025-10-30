<?php
declare(strict_types=1);

namespace iutnc\deefy\audio\tracks;

use iutnc\deefy\exception\InvalidPropertyValueException;

class AlbumTrack extends AudioTrack
{
    protected string $album;
    protected ?int $trackNumber;

    public function __construct(string $titre, string $filename, string $album, ?int $trackNumber = null, ?int $duration = null)
    {
        parent::__construct($titre, $filename, $duration);
        $this->album = $album;
        if ($trackNumber !== null && $trackNumber <= 0) {
            throw new InvalidPropertyValueException('trackNumber must be > 0');
        }
        $this->trackNumber = $trackNumber;
    }

    public function getAlbum(): string { return $this->album; }
    public function getTrackNumber(): ?int { return $this->trackNumber; }
}
