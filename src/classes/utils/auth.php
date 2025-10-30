<?php
declare(strict_types=1);

namespace iutnc\deefy\utils;

use iutnc\deefy\repository\DeefyRepository;

/** Vérifie qu’un utilisateur est connecté */
function requireAuth(): void {
    if (empty($_SESSION['user'])) {
        http_response_code(401);
        echo "<p>Veuillez vous connecter.</p>";
        exit;
    }
}

/** Vérifie qu’un utilisateur a accès à une playlist donnée */
function requirePlaylistAccess(int $playlistId): void {
    requireAuth();
    $repo = DeefyRepository::getInstance();
    $user = unserialize($_SESSION['user']);
    $uid = (int)$user['id'];

    $hasAccess = $repo->userHasAccessToPlaylist($uid, $playlistId);
    if (!$hasAccess) {
        http_response_code(403);
        echo "<p>Accès refusé à cette playlist.</p>";
        exit;
    }
}
