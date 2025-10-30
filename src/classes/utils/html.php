<?php
declare(strict_types=1);
namespace iutnc\deefy\utils;

/**
 * Fonction pour sécuriser une chaîne avant de l'afficher dans une page HTML.
 * Elle empêche les failles XSS en remplaçant les caractères spéciaux
 * (<, >, ", ', &) par leur équivalent HTML.
 *
 * @param string|null $s Chaîne à sécuriser (peut être nulle)
 * @return string Chaîne sécurisée, prête à être affichée
 */
function e(?string $s): string {
    return htmlspecialchars($s ?? '', ENT_QUOTES, 'UTF-8');
}
