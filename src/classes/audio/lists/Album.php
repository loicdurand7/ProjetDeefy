<?php
declare(strict_types=1);
namespace iutnc\deefy\audio\lists;

class Album extends AudioList {
    protected string $artiste, $date;

    public function __construct(string $nom, array $pistes) {
        parent::__construct($nom, $pistes);
        $this->artiste = "???";
        $this->date = "???";
    }

    public function setArtiste(string $a): void { $this->artiste = $a; }
    public function setDate(string $d): void { $this->date = $d; }
}
