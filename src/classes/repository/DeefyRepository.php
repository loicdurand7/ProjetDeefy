<?php

declare(strict_types=1);

namespace iutnc\deefy\repository;

use iutnc\deefy\audio\lists\Playlist;
use PDO;

class DeefyRepository
{
    private PDO $pdo;
    private static ?DeefyRepository $instance = null;
    private static array $config = [];

    // Constructeur Singleton privé pour empêcher l'instanciation directe et configurer la connexion PDO
    private function __construct(array $conf)
    {
        $this->pdo = new PDO(
            $conf['dsn'],
            $conf['username'],
            $conf['password'] ?? '',
            [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_EMULATE_PREPARES => false,
                PDO::ATTR_STRINGIFY_FETCHES => false,
            ]
        );
    }

    // Load la configuration depuis un fichier INI (config.ini)
    public static function setConfig(string $file): void
    {
        $conf = parse_ini_file($file);
        if ($conf === false) {
            throw new \Exception("Erreur de lecture du fichier de config");
        }
        // On construit un DSN propre
        $dsn = "{$conf['driver']}:host={$conf['host']};dbname={$conf['database']};charset=utf8mb4";
        self::$config = [
            'dsn'      => $dsn,
            'username' => $conf['username'] ?? '',
            'password' => $conf['password'] ?? '',
        ];
    }

    // Pour recupérer l'instance unique du repository qui permet d'accéder aux données de la BD 
    public static function getInstance(): DeefyRepository
    {
        if (self::$instance === null) {
            self::$instance = new DeefyRepository(self::$config);
        }
        return self::$instance;
    }

    /* PLAYLISTS */
    // Fonction pour retrouver une playlist par son ID
    public function findPlaylistById(int $id): Playlist
    {
        $stmt = $this->pdo->prepare("SELECT id, nom FROM playlist WHERE id = :id");
        $stmt->execute(['id' => $id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$row) throw new \Exception("Playlist introuvable (id=$id)");
        $pl = new Playlist($row['nom']);
        $pl->setID((int)$row['id']);
        return $pl;
    }

    // Fonction qui sauvegarde une playlist vide et l'associe à l'utilisateur connecté
    public function saveEmptyPlaylist(Playlist $pl): Playlist
    {
        // Insérer la playlist
        $query = "INSERT INTO playlist (nom) VALUES (:nom)";
        $stmt = $this->pdo->prepare($query);
        $stmt->execute(['nom' => $pl->nom]);
        $pl->setID((int)$this->pdo->lastInsertId());

        // Associer à l'utilisateur 
        $userId = $_SESSION['user_id'] ?? null;
        if ($userId) {
            $st2 = $this->pdo->prepare("INSERT INTO user2playlist (id_user, id_pl) VALUES (:uid, :pid)");
            $st2->execute(['uid' => $userId, 'pid' => $pl->getID()]);
        }
    }

    // Fonction pour récupérer toutes les playlists (pas utile ici comme on veut gerer par user)
    public function findAllPlaylists(): array
    {
        $res = [];
        $stmt = $this->pdo->query("SELECT id, nom FROM playlist");
        foreach ($stmt as $row) {
            $pl = new Playlist($row['nom']);
            $pl->setID((int)$row['id']);
            $res[] = $pl;
        }
        return $res;
    }

    /* USERS */
    /** Retourne la ligne user par email ou null si inconnu */
    public function findUserByEmail(string $email): ?array
    {
        $st = $this->pdo->prepare("SELECT * FROM `User` WHERE email = ?");
        $st->execute([$email]);
        $u = $st->fetch(PDO::FETCH_ASSOC);
        return $u ?: null;
    }

    /** Insère un utilisateur (email + hash) */
    public function insertUser(string $email, string $hash): void
    {
        $st = $this->pdo->prepare("INSERT INTO `User` (email, passwd) VALUES (?, ?)");
        $st->execute([$email, $hash]);
    }

    /* PLAYLISTS / USERS */
    /** Retourne toutes les playlists d’un utilisateur spécifié */
    public function listPlaylistsByUser(int $userId): array
    {
        $sql = "SELECT p.id, p.nom 
                FROM playlist p 
                JOIN user2playlist up ON p.id = up.id_pl
                WHERE up.id_user = :uid";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['uid' => $userId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /** Crée une playlist et la lie à l'utilisateur */
    public function createPlaylist(string $nom, int $userId): int
    {
        $this->pdo->beginTransaction();
        try {
            $st = $this->pdo->prepare("INSERT INTO playlist (nom) VALUES (:nom)");
            $st->execute(['nom' => $nom]);
            $pid = (int)$this->pdo->lastInsertId();

            $link = $this->pdo->prepare(
                "INSERT INTO user2playlist (id_user, id_pl) VALUES (:uid, :pid)"
            );
            $link->execute(['uid' => $userId, 'pid' => $pid]);

            $this->pdo->commit();
            return $pid;
        } catch (\Throwable $e) {
            $this->pdo->rollBack();
            throw $e;
        }
    }

    /** Vérifie si un utilisateur a accès à une playlist */
    public function userHasAccessToPlaylist(int $userId, int $playlistId): bool
    {
        $sql = "SELECT COUNT(*) FROM user2playlist WHERE id_user = :uid AND id_pl = :pid";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['uid' => $userId, 'pid' => $playlistId]);
        return $stmt->fetchColumn() > 0;
    }

    /** Retourne une playlist sous forme de tableau */
    public function getPlaylist(int $playlistId): ?array
    {
        $sql = "SELECT id, nom FROM playlist WHERE id = :pid";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['pid' => $playlistId]);
        $pl = $stmt->fetch(PDO::FETCH_ASSOC);
        return $pl ?: null;
    }

    /* TRACKS */
    /** Retourne toutes les pistes d'une playlist */
    public function listTracksOfPlaylist(int $playlistId): array
    {
        $sql = "SELECT t.* 
                FROM track t 
                JOIN playlist2track pt ON t.id = pt.id_track
                WHERE pt.id_pl = :pid
                ORDER BY pt.no_piste_dans_liste";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['pid' => $playlistId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /** Crée une nouvelle piste puis la lie à une playlist */
    public function addNewTrackToPlaylist(int $playlistId, array $trackData): bool
    {
        // 1. Insérer la track
        $insertTrack = "INSERT INTO track (titre, genre, duree, filename, type) 
                        VALUES (:titre, :genre, :duree, :filename, 'A')";
        $st1 = $this->pdo->prepare($insertTrack);
        $st1->execute([
            'titre' => $trackData['titre'],
            'genre' => $trackData['genre'] ?? null,
            'duree' => $trackData['duree'] ?? null,
            'filename' => $trackData['filename'] ?? null
        ]);
        $trackId = (int)$this->pdo->lastInsertId();

        // 2. Déterminer le numéro de piste suivant
        $st2 = $this->pdo->prepare("SELECT COALESCE(MAX(no_piste_dans_liste), 0) + 1 AS next 
                                    FROM playlist2track WHERE id_pl = :pid");
        $st2->execute(['pid' => $playlistId]);
        $next = (int)$st2->fetchColumn();

        // 3. Lier la track à la playlist
        $st3 = $this->pdo->prepare("INSERT INTO playlist2track (id_pl, id_track, no_piste_dans_liste)
                                    VALUES (:pid, :tid, :n)");
        return $st3->execute(['pid' => $playlistId, 'tid' => $trackId, 'n' => $next]);
    }

    /** Supprime une piste d'une playlist */
    public function removeTrackFromPlaylist(int $playlistId, int $trackId): void
    {
        $sql = "DELETE FROM playlist2track WHERE id_pl = :pid AND id_track = :tid";
        $st = $this->pdo->prepare($sql);
        $st->execute(['pid' => $playlistId, 'tid' => $trackId]);
    }

    /** Supprime une playlist (et toutes ses pistes associées) */
    public function removePlaylist(int $playlistId): void
    {
        $this->pdo->beginTransaction();
        try {
            // Supprimer les liens entre cette playlist et ses pistes
            $st = $this->pdo->prepare("DELETE FROM playlist2track WHERE id_pl = :pid");
            $st->execute(['pid' => $playlistId]);

            // Supprimer le lien entre l’utilisateur et cette playlist
            $st = $this->pdo->prepare("DELETE FROM user2playlist WHERE id_pl = :pid");
            $st->execute(['pid' => $playlistId]);

            // Supprimer la playlist elle-même
            $st = $this->pdo->prepare("DELETE FROM playlist WHERE id = :pid");
            $st->execute(['pid' => $playlistId]);

            $this->pdo->commit();
        } catch (\Throwable $e) {
            $this->pdo->rollBack();
            throw $e;
        }
    }
}
