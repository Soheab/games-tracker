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
$collections = $user->getCollections();
echo json_encode($collections);

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>My Collections</title>
    <style>
        table {
            border-collapse: collapse;
            width: 100%;
        }

        th, td {
            border: 1px solid #ccc;
            padding: 8px;
        }

        th {
            background: #f0f0f0;
        }

        img {
            max-width: 100px;
            max-height: 100px;
        }
    </style>
</head>
<body>
<h1>My Collections</h1>
<?php if (empty($collections)) : ?>
    <p>You have no collections.</p>
<?php else : ?>
    <table>
        <thead>
        <tr>
            <th>ID</th>
            <th>Name</th>
            <th>Description</th>
            <th>Image</th>
            <th>Created At</th>
            <th>Updated At</th>
            <th>Actions</th>
        </tr>
        </thead>
        <tbody>
        <?php foreach ($collections as $col) : ?>
            <?php $games = $col->getGames(); ?>
            <tr>
                <td><?= htmlspecialchars($col->id) ?></td>
                <td><?= htmlspecialchars($col->name) ?></td>
                <td><?= htmlspecialchars($col->description) ?></td>
                <td><?php if ($col->image_file) : ?>
                        <img src="../images/<?= htmlspecialchars($col->image_file) ?>" alt="Collection Image">
                    <?php endif; ?></td>
                <td><?= htmlspecialchars($col->created_at) ?></td>
                <td><?= htmlspecialchars($col->updated_at) ?></td>
                <td>
                    <a href="update.php?id=<?= urlencode($col->id) ?>">Edit</a>
                    |
                    <a href="delete.php?id=<?= urlencode($col->id) ?>" onclick="return confirm('Are you sure you want to delete this collection?');">Delete</a>
                    |
                    <a href="../games/create.php?collection_id=<?= urlencode($col->id) ?>">Add Game</a>
                </td>
            </tr>
            <tr>
                <td colspan="7">

                    <strong>Games:</strong>
                    <?php if (empty($games)) : ?>
                        <em>No games in this collection.</em>
                    <?php else : ?>
                        <ul>
                            <?php foreach ($games as $game) : ?>
                                <li>
                                    <?= htmlspecialchars($game->name ?? 'Unnamed Game') ?>
                                    <?php if (!empty($game->description)) : ?>
                                        - <?= htmlspecialchars($game->description) ?>
                                    <?php endif; ?>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    <?php endif; ?>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
<?php endif; ?>
</body>
</html>
