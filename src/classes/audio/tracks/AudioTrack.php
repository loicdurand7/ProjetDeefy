<?php
declare(strict_types=1);

namespace iutnc\deefy\audio\tracks;

use iutnc\deefy\exception\InvalidPropertyNameException;
use iutnc\deefy\exception\InvalidPropertyValueException;

class AudioTrack
{
    protected string $titre;
    protected string $filename;
    protected ?int $duration;
    protected ?string $artiste = null;

    public function __construct(string $titre, string $filename, ?int $duration = null)
    {
        $this->titre = $titre;
        $this->filename = $filename;
        if ($duration !== null && $duration < 0) {
            throw new InvalidPropertyValueException('duration must be >= 0');
        }
        $this->duration = $duration;
    }

    public function getTitre(): string { return $this->titre; }
    public function getFilename(): string { return $this->filename; }
    public function getDuration(): ?int { return $this->duration; }
    public function setArtiste(string $artiste): void { $this->artiste = $artiste; }
    public function getArtiste(): ?string { return $this->artiste; }

    public function __get(string $name)
    {
        switch ($name) {
            case 'titre': return $this->titre;
            case 'filename': return $this->filename;
            case 'duration':
            case 'duree':
                 return $this->duration;
            case 'artiste': return $this->artiste;
            default: throw new InvalidPropertyNameException("Unknown property: $name");
        }
    }

    public function __set(string $name, $value): void
    {
        switch ($name) {
            case 'artiste':
                if (!is_string($value) || $value === '') {
                    throw new InvalidPropertyValueException('artiste must be non-empty string');
                }
                $this->artiste = $value;
                break;
            case 'duration':
                if (!is_int($value) || $value < 0) {
                    throw new InvalidPropertyValueException('duration must be int >= 0');
                }
                $this->duration = $value;
                break;
            default:
                throw new InvalidPropertyNameException("Unknown or read-only property: $name");
        }
    }
}
