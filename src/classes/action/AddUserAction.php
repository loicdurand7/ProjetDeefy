<?php
namespace iutnc\deefy\action;

class AddUserAction extends Action
{
    public function execute(): string
    {
        // GET : formulaire d'inscription (TD12 §6)
        if ($this->http_method === 'GET') {
            return <<<HTML
            <h2>Inscription</h2>
            <form method="post" action="?action=add-user">
              <label>Nom :
                <input type="text" name="name" required>
              </label><br>
              <label>Email :
                <input type="email" name="email" required>
              </label><br>
              <label>Âge :
                <input type="number" name="age" min="0" max="130" step="1" required>
              </label><br>
              <button type="submit">Connexion</button>
            </form>
            HTML;
        }

        // POST : filtrer et afficher les valeurs (XSS)
        $name  = filter_var($_POST['name']  ?? '', FILTER_SANITIZE_SPECIAL_CHARS);
        $email = filter_var($_POST['email'] ?? '', FILTER_SANITIZE_EMAIL);
        $age   = filter_var($_POST['age']   ?? '', FILTER_VALIDATE_INT);

        $ageTxt = ($age === false) ? 'âge invalide' : ($age . ' ans');

        return "<p>Nom: {$name}, Email: {$email}, Age: {$ageTxt}</p>";
    }
}
