<?php
header('Content-Type: application/json');

//path
$docRoot = '/uploads';
if (isset($_GET['folderName']) != '') {
    $folderName = $_GET['folderName'];
    $d = dir(getcwd().$docRoot.$folderName);
} else {
    $d = dir(getcwd().$docRoot);
}

$dirPath = $d->path;

$localPath = str_replace(getcwd().$docRoot, '', $dirPath);
