<?php
namespace iutnc\deefy\action;

use iutnc\deefy\auth\AuthnProvider;
use iutnc\deefy\exception\AuthnException;
use iutnc\deefy\repository\DeefyRepository;

class RegisterAction extends Action {
    public function execute(): string {
        if ($this->http_method === 'GET') {
            return <<<HTML
            <div style="display:flex;justify-content:center;align-items:center;height:55vh;">
              <div style="background:#fff;padding:24px;border-radius:12px;box-shadow:0 6px 18px rgba(0,0,0,.08);width:360px;">
                <h2 style="text-align:center;margin:0 0 12px">Inscription</h2>
                <form method="POST" action="?action=register">
                  <label>Email</label>
                  <input type="email" name="email" required style="width:100%;padding:8px;border:1px solid #ccc;border-radius:6px;">
                  <label style="margin-top:10px;">Mot de passe</label>
                  <input type="password" name="passwd" required style="width:100%;padding:8px;border:1px solid #ccc;border-radius:6px;">
                  <label style="margin-top:10px;">Confirmer</label>
                  <input type="password" name="passwd2" required style="width:100%;padding:8px;border:1px solid #ccc;border-radius:6px;">
                  <button type="submit" style="margin-top:14px;background:#4CAF50;color:#fff;border:0;padding:10px;border-radius:6px;cursor:pointer;width:100%;">Créer le compte</button>
                </form>
              </div>
            </div>
            HTML;
        }

        // POST
        $email = filter_var($_POST['email'] ?? '', FILTER_SANITIZE_EMAIL);
        $p1 = $_POST['passwd']  ?? '';
        $p2 = $_POST['passwd2'] ?? '';
        if ($p1 !== $p2) {
            return $this->msg("Les mots de passe ne correspondent pas.", false);
        }

        // Vérifier doublon
        $repo = DeefyRepository::getInstance();
        $exists = $repo->findUserByEmail($email);
        if ($exists) {
            return $this->msg("Cet email est déjà utilisé.", false);
        }

        try {
            AuthnProvider::register($email, $p1);
            return $this->msg("Compte créé. Vous pouvez vous connecter.", true);
        } catch (AuthnException $e) {
            return $this->msg("Erreur : " . htmlspecialchars($e->getMessage(), ENT_QUOTES, 'UTF-8'), false);
        } catch (\Throwable $e) {
            return $this->msg("Erreur inattendue.", false);
        }
    }

    private function msg(string $txt, bool $ok): string {
        $color = $ok ? '#2e7d32' : '#c62828';
        $link  = $ok ? '<a href="?action=signin">Se connecter</a>' : '<a href="?action=register">Réessayer</a>';
        return <<<HTML
        <div style="display:flex;justify-content:center;align-items:center;height:55vh;">
          <div style="background:#fff;padding:24px;border-radius:12px;box-shadow:0 6px 18px rgba(0,0,0,.08);text-align:center;max-width:520px;">
            <p style="color:$color;font-weight:600;margin:0 0 12px">$txt</p>
            $link
          </div>
        </div>
        HTML;
    }
}
