<?php
namespace App;

require_once __DIR__.'/../vendor/autoload.php';
include_once __DIR__."/../Runtime.php";

$runtime = getRuntime();

if ($runtime->isLoggedIn()) {
    echo "Je bent al ingelogd.";
    header('Location: profile.php');
    exit;
};

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['register'])) {
    $username = $_POST['username'] ?? '';
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';

    if (!User::userIsUnique($runtime, $username, $email)) {
        echo "Gebruikersnaam of email is al in gebruik.";
    };

    $user = new User(
        runtime: $runtime,
        id: null,
        username: $username,
        email: $email,
        password: password_hash($password, PASSWORD_DEFAULT)
    );
    $user->store();

    header('Location: /login.php');
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Registeren</title>
</head>
<body>
<h1>Registeren</h1>
<form  method="post">
    <label for="username">Gebruikersnaam:</label>
    <input type="text" id="username" name="username" required><br><br>

    <label for="email">Email:</label>
    <input type="email" id="email" name="email" required><br><br>

    <label for="password">Wachtwoord:</label>
    <input type="password" id="password" name="password" required><br><br>

    <button type="submit" name="register">Register</button>
</form>
</body>
</html>
