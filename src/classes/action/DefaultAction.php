<?php
declare(strict_types=1);

namespace iutnc\deefy\action;

class DefaultAction extends Action
{
    public function execute(): string
    {
        // Si l'utilisateur est connecté
        if (isset($_SESSION['user_id'])) {
            $user = unserialize($_SESSION['user']);
            $email = htmlspecialchars($user['email'], ENT_QUOTES, 'UTF-8');

            return <<<HTML
            <div style="text-align:center; margin-top:60px;">
                <h2>Bienvenue sur Deefy</h2>
                <p>Connecté en tant que <strong>{$email}</strong>.</p>
                <p>Utilise le menu ci-dessus pour gérer tes playlists :</p>
                <ul style="display:inline-block; text-align:left;">
                    <li><a href="?action=mes-playlists">Voir mes playlists</a></li>
                    <li><a href="?action=add-playlist">Créer une nouvelle playlist</a></li>
                    <li><a href="?action=add-track">Ajouter une piste</a></li>
                </ul>
            </div>
            HTML;
        }

        // Sinon, utilisateur non connecté
        return <<<HTML
        <div style="text-align:center; margin-top:60px;">
            <h2>Bienvenue sur Deefy</h2>
            <p>Connecte-toi pour créer et gérer tes playlists musicales.</p>
            <p>
                <a href="?action=signin" style="color:#4CAF50;">Se connecter</a> ou
                <a href="?action=register" style="color:#4CAF50;">Créer un compte</a>
            </p>
        </div>
        HTML;
    }
}