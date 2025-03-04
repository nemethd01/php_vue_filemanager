<?php

include_once "../vendor/autoload.php";
include_once "../app/autoload.php";

use app\controllers\FileController;
use app\controllers\FolderController;
use Bramus\Router\Router;

$router = new Router();

// custom 404
$router->set404('/(.*)?', function () {
    header($_SERVER['SERVER_PROTOCOL'] . ' 404 Not Found');
//    http_response_code(404);
    echo '<h1><mark>404, route not found!</mark></h1>';
});
//404 json encode
/*$router->set404('/api(/.*)?', function() {
    header('HTTP/1.1 404 Not Found');
    header('Content-Type: application/json');

    $jsonArray = array();
    $jsonArray['status'] = "404";
    $jsonArray['status_text'] = "route not defined";

    echo json_encode($jsonArray);
});*/


// filemanager


$fileController = new FileController();
$folderController = new FolderController();

// folders

// list root folder
$router->get('/folder', function () use ($folderController) {
    print json_encode($folderController->listRootFolder());
});

// open folder
$router->get('/folder/(\d+)', function ($folderId) use ($folderController) {
    print json_encode($folderController->openFolder($folderId));
});

// Create folder
$router->get('/create-folder', function () use ($folderController) {
    return $folderController->createFolder($_GET);
});

// delete folder
$router->delete('/delete-folder/(\d+)', function ($deleteItemId) use ($folderController) {
    return $folderController->deleteFolder($deleteItemId);
});


// files

// file upload
// upload chunk
$router->post('/upload-file-chunks', function () use ($fileController) {
    return $fileController->uploadFileChunks($_POST);
});
// finish upload
$router->post('/finish-upload', function () use ($fileController) {
    return $fileController->finishUploadFile($_POST);
});

// download file
$router->get("/download/(\d+)", function ($downloadFileId) use ($fileController) {
    return $fileController->downloadFile($downloadFileId);
});

// delete file
$router->delete('/delete-file/(\d+)', function ($deleteFileId) use ($fileController) {
    return $fileController->deleteFile($deleteFileId);
});


$router->run();
