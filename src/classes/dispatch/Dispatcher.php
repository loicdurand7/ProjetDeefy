<?php

declare(strict_types=1);

namespace iutnc\deefy\dispatch;

use \iutnc\deefy\action;

class Dispatcher
{
    private string $action;

    public function __construct(?string $a)
    {
        // vérifier si paramètre null → mettre chaîne vide
        $this->action = ($a !== null) ? $a : '';
    }

    // Fonction principale du dispatcher qui permet de lancer les différentes actions
    public function run(): void
    {
        switch ($this->action) {
            case 'display-playlist':
                $html = $this->runAction(new action\DisplayPlaylistAction());
                break;

            case 'mes-playlists':
                $html = $this->runAction(new action\MesPlaylistsAction());
                break;

            case 'add-playlist':
                $html = $this->runAction(new action\AddPlaylistAction());
                break;

            case 'current-playlist':
                $html = $this->runAction(new action\CurrentPlaylistAction());
                break;

            case 'add-track':
                $html = $this->runAction(new action\AddPodcastTrackAction());
                break;

            case 'signin':
                $html = $this->runAction(new action\SigninAction());
                break;

            case 'register':
                $html = $this->runAction(new action\RegisterAction());
                break;
            case 'logout':
                $html = $this->runAction(new action\LogoutAction());
                break;

            case 'delete-track':
                $html = $this->runAction(new action\DeleteTrackAction());
                break;
            case 'delete-playlist':
                $html = $this->runAction(new action\DeletePlaylistAction());
                break;

            default:
                $html = $this->runAction(new action\DefaultAction()); // page d’accueil par défaut
                break;
        }

        $this->renderPage($html);
    }

    // appelle execute() si dispo, sinon __invoke()
    private function runAction(object $act): string
    {
        // vérifier si la méthode execute existe
        if (method_exists($act, 'execute')) {
            return $act->execute();
        }
        // sinon, vérifier si l'objet est invocable (__invoke)
        if (is_callable($act)) {
            return $act();
        }
        // Si aucun des deux n'est dispo, message d'erreur
        return '<p>Action non exécutable</p>';
    }

    // Fonction pour rendre la page HTML complète avec header, footer, menus, etc
    public function renderPage(string $html): void
    {
        // Menu gauche si utilisateur connecté
        $leftMenu = '';
        if (isset($_SESSION['user_id'])) {
            $leftMenu = '
        <a href="?action=mes-playlists">Mes playlists</a>
        <a href="?action=add-playlist">Créer une playlist</a>
        <a href="?action=add-track">Ajouter une piste</a>
        <a href="?action=current-playlist">Playlist courante</a>
    ';
        }

        // Menu de droite selon état de connexion 
        $rightMenu = isset($_SESSION['user_id'])
            ? '<a href="?action=logout" style="color:red;margin-left:10px;">Se déconnecter</a>'
            : '<a href="?action=signin" style="margin-left:10px;">Connexion</a>
       <a href="?action=register" style="margin-left:10px;">Inscription</a>';

        echo <<<HTML
        <!doctype html>
        <html lang="fr">
        <head>
            <meta charset="utf-8">
            <title>Deefy</title>
            <style>
                body { font-family: Arial, sans-serif; margin:0; background:#fafafa; color:#111; }
                header { background:#111; color:#fff; padding:16px; }
                header h1 { margin:0; font-size:22px; }
                nav { margin-top:8px; }
                nav a { color:#fff; margin-right:15px; text-decoration:none; }
                main { max-width:900px; margin:24px auto; padding:0 16px; }
                footer { text-align:center; margin-top:40px; color:#666; font-size:14px; }
            </style>
        </head>
        <body>
        <header>
            <h1>Deefy – TD12</h1>
            <nav>
                <a href="?action=default">Accueil</a>
                {$leftMenu}
                <span style="float:right;">
                    {$rightMenu}
                </span>
            </nav>
        </header>
        <main>
            {$html}
        </main>
        </body>
        </html>
        HTML;
            }
}