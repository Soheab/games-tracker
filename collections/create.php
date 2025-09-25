<?php

namespace App;

require_once __DIR__.'/../vendor/autoload.php';
include_once __DIR__."/../Runtime.php";

$runtime = getRuntime();

if (!$runtime->isLoggedIn()) {
    echo "Je moet ingelogd zijn om de pagina te bekijken.";
    header('Location: ../users.php');
    exit;
};


$user = $runtime->getCurrentUser();


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'] ?? '';
    $description = $_POST['description'] ?? '';
    $image_file = $_FILES['image']['name'] ?? '';


    $user_id = 1; // Assuming user ID 1 for this example
    $collection = new Collection($runtime, null, $user_id, $name, $description, $image_file);
    $collection->store();

    header('Location: view.php');
    exit;
}


?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Collectie</title>
</head>
<body>
<h1>Maak een collectie</h1>
<form method="post" action="">
    <label for="name">Naam:</label><br>
    <input type="text" id="name" name="name" required>
    <br><br>
    <label for="description">Beschrijving:</label><br>
    <textarea id="description" name="description"></textarea>
    <br><br>
    <label for="image">Afbeelding:</label><br>
    <input type="file" id="image" name="image" accept="image/*">
    <br><br>
    <button type="submit">Maak Collectie</button>
</form>
</body>
</html>


