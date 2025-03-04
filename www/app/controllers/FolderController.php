<?php

namespace app\controllers;

use app\models\DB;
use app\models\FolderModel;
use JetBrains\PhpStorm\NoReturn;

class FolderController
{
    public function openFolder(int $folderId): array
    {
        $folderModel = new FolderModel();

        return $folderModel->openFolder($folderId);

    } // open folder
    public function createFolder(): void
    {
        $conn = DB::getInstance();
        $folderModel = new FolderModel();

        $createFolderName = $_GET['createFolder'];
        $folderId = $_GET['folderId'];
        $creationDateForDb = date('Y-m-d H:i:s');

        if ($folderId === null) {
            $folderModel->createFolderHasNotParent($createFolderName, $creationDateForDb);
        } else {
            $folderModel->createFolderHasParent($folderId, $createFolderName, $creationDateForDb);
        }
    } // create folder
    public function deleteFolder($deleteItemId): void
    {
        $conn = DB::getInstance();
        $folderModel = new FolderModel();

        $folderModel->deleteFolder($deleteItemId);

    } // delete folder
    public function listRootFolder(): array
    {
        $folderModel = new FolderModel();

        return $folderModel->listRootFolder();

    } // list rooot

}