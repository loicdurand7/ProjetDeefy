<?php
declare(strict_types=1);

namespace iutnc\deefy\audio\tracks;

class PodcastTrack extends AudioTrack
{
    protected string $auteur;
    protected ?\DateTimeImmutable $date;

    public function __construct(string $titre, string $filename, string $auteur, ?string $date = null, ?int $duration = null)
    {
        parent::__construct($titre, $filename, $duration);
        $this->auteur = $auteur;
        $this->date = $date ? new \DateTimeImmutable($date) : null;
    }

    public function getAuteur(): string { return $this->auteur; }
    public function getDate(): ?\DateTimeImmutable { return $this->date; }
}
