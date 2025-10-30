<?php
declare(strict_types=1);
namespace iutnc\deefy\audio\lists;

class AudioList {
    protected string $nom;
    protected array $pistes;
    protected int $nbpistes, $duree;
    protected ?int $id = null;   

    public function __construct(string $nom, array $pistes = []) {
        $this->nom = $nom;
        $this->pistes = $pistes;
        $this->nbpistes = count($pistes);
        $this->duree = array_reduce($pistes, fn($acc, $p) => $acc + ($p->duree ?? 0), 0);
    }

    public function __get(string $prop): mixed {
        // Aliases pour compatibilité avec les renderers
        if ($prop === 'duration') return $this->duree;   // alias
        if ($prop === 'name')     return $this->nom;     // alias
        if ($prop === 'tracks')   return $this->pistes;  // alias

        if (!property_exists($this, $prop)) {
            throw new \Exception("invalid property : $prop");
        }
        return $this->$prop;
    }
     public function setID(int $id): void { $this->id = $id; }
    public function getID(): ?int { return $this->id; }
}
