<?php
// =============================================
// FONCTIONS UTILITAIRES
// =============================================

/**
 * Lire le fichier CSV des utilisateurs
 */
function getUsers(): array
{
    if (!file_exists(FILE_USERS))
        return [];
    $lines = file(FILE_USERS, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    $users = [];
    foreach ($lines as $line) {
        $data = str_getcsv($line);
        // email, password_hash, validated, token_validation, subscriptions, created_at
        if (count($data) >= 6) {
            $users[] = [
                'email' => $data[0],
                'password' => $data[1],
                'validated' => $data[2],
                'token_validation' => $data[3],
                'subscriptions' => $data[4] ? explode('|', $data[4]) : [],
                'created_at' => $data[5],
            ];
        }
    }
    return $users;
}

/**
 * Sauvegarder tous les utilisateurs dans le CSV
 */
function saveUsers(array $users): void
{
    $lines = [];
    foreach ($users as $u) {
        $subs = implode('|', $u['subscriptions']);
        $lines[] = implode(',', [
            csvEscape($u['email']),
            csvEscape($u['password']),
            csvEscape($u['validated']),
            csvEscape($u['token_validation']),
            csvEscape($subs),
            csvEscape($u['created_at']),
        ]);
    }
    file_put_contents(FILE_USERS, implode("\n", $lines) . "\n");
}

function csvEscape(string $val): string
{
    if (strpos($val, ',') !== false || strpos($val, '"') !== false || strpos($val, "\n") !== false) {
        return '"' . str_replace('"', '""', $val) . '"';
    }
    return $val;
}

/**
 * Trouver un utilisateur par email
 */
function findUser(string $email): ?array
{
    foreach (getUsers() as $u) {
        if (strtolower($u['email']) === strtolower($email))
            return $u;
    }
    return null;
}

/**
 * Mettre à jour un utilisateur
 */
function updateUser(string $email, array $newData): bool
{
    $users = getUsers();
    foreach ($users as &$u) {
        if (strtolower($u['email']) === strtolower($email)) {
            $u = array_merge($u, $newData);
            saveUsers($users);
            return true;
        }
    }
    return false;
}

/**
 * Créer un nouvel utilisateur
 */
function createUser(string $email, string $password, array $subscriptions): string
{
    $token = bin2hex(random_bytes(32));
    $users = getUsers();
    $users[] = [
        'email' => $email,
        'password' => password_hash($password, PASSWORD_DEFAULT),
        'validated' => '0',
        'token_validation' => $token,
        'subscriptions' => $subscriptions,
        'created_at' => date('Y-m-d H:i:s'),
    ];
    saveUsers($users);
    return $token;
}

/**
 * Générer et stocker un token de réinitialisation
 */
function generateResetToken(string $email): string
{
    $token = bin2hex(random_bytes(32));
    $expiry = date('Y-m-d H:i:s', strtotime('+1 hour'));
    $lines = [];
    if (file_exists(FILE_TOKENS)) {
        foreach (file(FILE_TOKENS, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) as $line) {
            $d = str_getcsv($line);
            if ($d[0] !== $email)
                $lines[] = $line; // on écrase l'ancien
        }
    }
    $lines[] = implode(',', [csvEscape($email), csvEscape($token), csvEscape($expiry)]);
    file_put_contents(FILE_TOKENS, implode("\n", $lines) . "\n");
    return $token;
}

/**
 * Valider un token de réinitialisation
 */
function validateResetToken(string $token): ?string
{
    if (!file_exists(FILE_TOKENS))
        return null;
    foreach (file(FILE_TOKENS, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) as $line) {
        $d = str_getcsv($line);
        if (count($d) >= 3 && $d[1] === $token && strtotime($d[2]) > time()) {
            return $d[0]; // retourne l'email
        }
    }
    return null;
}

/**
 * Supprimer un token de réinitialisation
 */
function deleteResetToken(string $token): void
{
    if (!file_exists(FILE_TOKENS))
        return;
    $lines = [];
    foreach (file(FILE_TOKENS, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) as $line) {
        $d = str_getcsv($line);
        if ($d[1] !== $token)
            $lines[] = $line;
    }
    file_put_contents(FILE_TOKENS, implode("\n", $lines) . "\n");
}

/**
 * Envoyer un email (avec php mail())
 */
function sendMail(string $to, string $subject, string $htmlBody): bool
{
    $headers = "MIME-Version: 1.0\r\n";
    $headers .= "Content-Type: text/html; charset=UTF-8\r\n";
    $headers .= "From: " . MAIL_NAME . " <" . MAIL_FROM . ">\r\n";
    return mail($to, $subject, $htmlBody, $headers);
}

/**
 * Envoyer email de validation d'inscription
 */
function sendValidationEmail(string $email, string $token): void
{
    $link = BASE_URL . "validate.php?token=" . urlencode($token) . "&email=" . urlencode($email);
    $subject = "[Le Monde RSS] Validez votre inscription";
    $body = "
    <h2>Bienvenue sur Le Monde – Aggrégateur RSS</h2>
    <p>Merci pour votre inscription ! Cliquez sur le lien ci-dessous pour valider votre compte :</p>
    <p><a href='$link' style='background:#e20025;color:#fff;padding:10px 20px;text-decoration:none;border-radius:4px;'>Valider mon compte</a></p>
    <p>Ou copiez ce lien : <code>$link</code></p>
    <p>Ce lien est valable 24h.</p>
    ";
    sendMail($email, $subject, $body);
}

/**
 * Envoyer email de réinitialisation de mot de passe
 */
function sendResetEmail(string $email, string $token): void
{
    $link = BASE_URL . "reset_password.php?token=" . urlencode($token);
    $subject = "[Le Monde RSS] Réinitialisation de votre mot de passe";
    $body = "
    <h2>Réinitialisation de mot de passe</h2>
    <p>Vous avez demandé à réinitialiser votre mot de passe. Cliquez sur le lien ci-dessous :</p>
    <p><a href='$link' style='background:#e20025;color:#fff;padding:10px 20px;text-decoration:none;border-radius:4px;'>Réinitialiser mon mot de passe</a></p>
    <p>Ce lien expire dans <strong>1 heure</strong>. Si vous n'avez pas fait cette demande, ignorez cet email.</p>
    ";
    sendMail($email, $subject, $body);
}

/**
 * Récupérer et parser un flux RSS
 */
function fetchRSS(string $url): array
{
    $items = [];
    try {
        $ctx = stream_context_create(['http' => ['timeout' => 5, 'user_agent' => 'Mozilla/5.0']]);
        $content = @file_get_contents($url, false, $ctx);  // télécharge avec le contexte
        $xml = @simplexml_load_string($content, 'SimpleXMLElement', LIBXML_NOCDATA);  // parse le XML
        if (!$xml)
            return [];
        foreach ($xml->channel->item as $item) {
            $items[] = [
                'title' => (string) $item->title,
                'link' => (string) $item->link,
                'description' => strip_tags((string) $item->description),
                'pubDate' => (string) $item->pubDate,
                'image' => extractRSSImage($item),
            ];
            if (count($items) >= 5)
                break; // 5 articles par catégorie
        }
    } catch (Exception $e) {
    }
    return $items;
}

function extractRSSImage($item): string
{
    // Cherche image dans enclosure, media:content, ou description
    if (isset($item->enclosure) && (string) $item->enclosure['type'] === 'image/jpeg') {
        return (string) $item->enclosure['url'];
    }
    $ns = $item->getNamespaces(true);
    if (isset($ns['media'])) {
        $media = $item->children($ns['media']);
        if (isset($media->content))
            return (string) $media->content['url'];
    }
    // Parse description pour img src
    preg_match('/<img[^>]+src=["\']([^"\']+)["\']/', (string) $item->description, $m);
    return $m[1] ?? '';
}

/**
 * Vérifier si l'utilisateur est connecté (redirige sinon)
 */
function requireLogin(): array
{
    if (!isset($_SESSION['user'])) {
        header('Location: index.php');
        exit;
    }
    return $_SESSION['user'];
}

/**
 * Nettoyer une entrée utilisateur
 */
function clean(string $str): string
{
    return htmlspecialchars(trim($str), ENT_QUOTES, 'UTF-8');
}

/**
 * Afficher un message flash
 */
function flash(string $type, string $msg): void
{
    $_SESSION['flash'] = ['type' => $type, 'msg' => $msg];
}

function getFlash(): ?array
{
    if (isset($_SESSION['flash'])) {
        $f = $_SESSION['flash'];
        unset($_SESSION['flash']);
        return $f;
    }
    return null;
}
