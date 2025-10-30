<?php
declare(strict_types=1);

namespace iutnc\deefy\auth;

use iutnc\deefy\exception\AuthnException;
use iutnc\deefy\repository\DeefyRepository;

class AuthnProvider
{
    /** Cours: signin(email, passwd) -> vérifie et met en session */
    public static function signin(string $email, string $passwd2check): void {
        $repo = DeefyRepository::getInstance();
        $user = $repo->findUserByEmail($email);

        // même message pour email inconnu ou mauvais mdp
        if (!$user || !password_verify($passwd2check, $user['passwd'])) {
            throw new AuthnException("Auth error : invalid credentials");
        }

        // stocker l’utilisateur en session 
        $_SESSION['user'] = serialize($user);
        return;
    }

    /** Cours: register(email, pass) -> hash puis insert */
    public static function register(string $email, string $pass): bool {
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new AuthnException("error : invalid user email");
        }

        $hash = password_hash($pass, PASSWORD_DEFAULT, ['cost' => 12]);

        $repo = DeefyRepository::getInstance();
        $repo->insertUser($email, $hash);

        return true;
    }

    /** Cours: getSignedInUser() -> renvoie l’utilisateur de la session */
    public static function getSignedInUser(): array {
        if (!isset($_SESSION['user'])) {
            throw new AuthnException("Auth error : not signed in");
        }
        return unserialize($_SESSION['user']);
    }
}
