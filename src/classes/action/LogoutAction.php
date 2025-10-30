<?php
namespace iutnc\deefy\action;

class LogoutAction extends Action {

    public function execute(): string {
        // Détruire la session et réinitialiser les variables
        session_unset();     // supprime toutes les variables de session
        session_destroy();   // détruit complètement la session

        // Message de confirmation
        return <<<HTML
        <div style="display:flex;justify-content:center;align-items:center;height:55vh;">
          <div style="background:#fff;padding:24px;border-radius:12px;box-shadow:0 6px 18px rgba(0,0,0,.08);text-align:center;">
            <h2>Vous êtes maintenant déconnecté ✅</h2>
            <a href="?action=signin" style="color:#4CAF50;">Se reconnecter</a>
          </div>
        </div>
        HTML;
    }
}
