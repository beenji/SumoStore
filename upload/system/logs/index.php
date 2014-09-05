<?php
session_start();

if (!isset($_SESSION['user_id']) || !$_SESSION['user_id'] || !$_SESSION['token']) {
    header('Location: ../../');
    exit;
}

if (!isset($_GET['token']) || $_GET['token'] != $_SESSION['token']) {
    header('Location: ../../');
    exit;
}

if (isset($_GET['file'])) {
    $file = str_replace(array('..\\', '../', './'), '', $_GET['file']);
    header('Content-type: text/plain');
    echo file_get_contents($file);
}