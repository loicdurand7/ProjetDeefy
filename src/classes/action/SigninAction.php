<?php
namespace iutnc\deefy\action;

use iutnc\deefy\auth\AuthnProvider;
use iutnc\deefy\exception\AuthnException;

class SigninAction extends Action {

    public function execute(): string {
        $html = "";

        // Formulaire de connexion GET → afficher le formulaire
        if ($this->http_method === 'GET') {
            $html .= <<<HTML
            <div style="font-family: Arial, sans-serif; background-color: #f5f5f5; height: 100vh; display: flex; justify-content: center; align-items: center;">
                <div style="background: white; padding: 2em; border-radius: 15px; box-shadow: 0 0 15px rgba(0,0,0,0.1); width: 350px; text-align: center;">
                    <h2 style="color:#333;">Connexion</h2>
                    <form method="POST" action="?action=signin">
                        <label style="display:block; text-align:left; margin-top:10px;">Email</label>
                        <input type="email" name="email" required style="width:100%; padding:8px; border:1px solid #ccc; border-radius:5px;">
                        
                        <label style="display:block; text-align:left; margin-top:10px;">Mot de passe</label>
                        <input type="password" name="passwd" required style="width:100%; padding:8px; border:1px solid #ccc; border-radius:5px;">
                        
                        <button type="submit" style="margin-top:15px; background-color:#4CAF50; color:white; border:none; padding:10px 15px; border-radius:5px; cursor:pointer;">Se connecter</button>
                    </form>
                </div>
            </div>
            HTML;
        }

  // Si méthode POST → vérifier les identifiants
        else {
            $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
            $passwd = $_POST['passwd'];

            try {
                // Authentifie l'utilisateur
                AuthnProvider::signin($email, $passwd);

                // Récupère les infos du user (stockées dans $_SESSION['user'])
                $user = unserialize($_SESSION['user']);

                // On garde aussi un accès direct à l'id 
                $_SESSION['user_id'] = (int)$user['id'];  

                $html .= <<<HTML
                <div style="text-align:center; font-family: Arial, sans-serif; margin-top: 100px;">
                    <h2>Bienvenue, {$user['email']} !</h2>
                    <p>Authentification réussie 🎉</p>
                    <a href="?action=mes-playlists" style="color:#4CAF50;">Voir mes playlists</a>
                </div>
                HTML;
            } catch (AuthnException $e) {
                // Si une erreur d'authentification arrive, on renvoie le message d'erreur + un bouton pour reesayer
                $html .= <<<HTML
                <div style="text-align:center; font-family: Arial, sans-serif; margin-top: 100px;">
                    <h3 style="color:red;">Erreur : {$e->getMessage()}</h3>
                    <a href="?action=signin" style="color:#4CAF50;">Réessayer</a>
                </div>
                HTML;
            }
        }

        return $html;
    }
}
