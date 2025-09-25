<?php
namespace App;

require_once __DIR__ . '/../vendor/autoload.php';
include_once __DIR__ . "/../Runtime.php";


$runtime = getRuntime();

if ($runtime->isLoggedIn()) {
    $runtime->logout();
}

header('Location: login.php');
exit;
