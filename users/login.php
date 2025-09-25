<?php
namespace App;

require_once __DIR__ . '/../vendor/autoload.php';
include_once __DIR__ . "/../Runtime.php";

use Exception;

$runtime = getRuntime();

if ($runtime->isLoggedIn()) {
    echo "Je bent al ingelogd als {$runtime->getCurrentUser()->username}";
    header('Location: profile.php');
    exit;
};

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['login'])) {
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';

    try {
        $runtime->login($email, $password);
    } catch (Exception $e) {
        echo "Fout bij inloggen: ".$e->getMessage();
        exit;
    }

    header('Location: profile.php');
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Profiel</title>
</head>
<body>
<h1>Inloggen</h1>
<form method="post">
    <label for="email">Email:</label><br>
    <input type="email" id="email" name="email" required>
    <br><br>
    <label for="password">Wachtwoord:</label><br>
    <input type="password" id="password" name="password" required>
    <br><br>
    <button type="submit" name="login">Inloggen</button>
</form>
</body>
</html>
