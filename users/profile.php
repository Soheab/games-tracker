<?php

namespace App;

require_once __DIR__.'/../vendor/autoload.php';
include_once __DIR__."/../Runtime.php";

$runtime = getRuntime();

if (!$runtime->isLoggedIn()) {
    echo "Je moet ingelogd zijn om je profiel te bekijken.";
    header('Location: ../users.php');
    exit;
};


$user = $runtime->getCurrentUser();
$collections = $user->getCollections();

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Profiel van <?php echo htmlspecialchars($user->username); ?></title>
</head>
<body>

<h1>Profiel van <?php echo htmlspecialchars($user->username); ?></h1>
<p>Email: <?php echo htmlspecialchars($user->email); ?></p>
<p>Geregistreerd op: <?php echo htmlspecialchars($user->created_at); ?></
p>
<h2>Jouw Collecties</h2>
<?php if (empty($collections)) : ?>
    <p>Je hebt nog geen collecties. <a href="../collections/create.php">Maak er een aan</a>.</p>
<?php else : ?>
    <ul>
        <?php foreach ($collections as $collection) : ?>
            <li>
                <h3><?php echo htmlspecialchars($collection->name); ?></h3>
                <p><?php echo htmlspecialchars($collection->description); ?></p>
                <?php if (!empty($collection->image_file)) : ?>
                    <img src="../images/<?php echo htmlspecialchars($collection->image_file); ?>"
                         alt="<?php echo htmlspecialchars($collection->name); ?>" style="max-width:200px;">
                <?php endif; ?>
                <p>Aangemaakt op: <?php echo htmlspecialchars($collection->created_at); ?></p>
                <p><a href="../collections/view.php?id=<?php echo $collection->id; ?>">Bekijk collectie</a></p>
            </li>
        <?php endforeach; ?>
    </ul>
<?php endif; ?>

<p><a href="../collections/create.php">
        <button type="button">Nieuwe collectie aanmaken</button>
    </a></p>

<p><a href="logout.php">Uitloggen</a></p>
</body>
</html>
