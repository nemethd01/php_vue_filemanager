<?php

namespace app\models;

use app\models\DB;
use PDO;
use PDOException;

class FolderModel
{
    public function openFolder(int $folderId): array
    {
        $conn = DB::getInstance();

        try {
            $sql = "SELECT dir_name FROM directories WHERE dir_id=:folderId";
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(":folderId", $folderId);
            $stmt->execute();
            $result = $stmt->fetch();
            $actualDirName = $result['dir_name'];

            $tomb = $this->orderBy();

            $sql = "SELECT * FROM files WHERE dir_id=:folderId ORDER BY " . $tomb['filterBy'] . ' ' . $tomb['sort'];
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(":folderId", $folderId);
            $stmt->execute();
            $files = $stmt->fetchAll();

            $filterBy = $this->translateFilterBy($tomb['filterBy']);
            $sql = "SELECT * FROM directories WHERE parent_id=:folderId ORDER BY " . $filterBy . ' ' .$tomb['sort'];
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(":folderId", $folderId);
            $stmt->execute();
            $directories = $stmt->fetchAll();

            $isRootDir = false;
        } catch (PDOException $exception) {
            echo "Error: ".$exception->getMessage();
        }

        return [
            'actualDirName' => $actualDirName,
            'files' => $files,
            'directories' => $directories,
            'isRootDir' => $isRootDir,
        ];
    }

    public function listRootFolder(): array
    {
        $conn = DB::getInstance();

        try {
            $tomb = $this->orderBy();

            $sql = "SELECT * FROM files WHERE dir_id is null ORDER BY " . $tomb['filterBy'] . ' ' . $tomb['sort'];
            $stmt = $conn->prepare($sql);
            $stmt->execute();
            $files = $stmt->fetchAll();

            $filterBy = $this->translateFilterBy($tomb['filterBy']);
            $sql = "SELECT * FROM directories WHERE parent_id is null ORDER BY " . $filterBy . ' ' .$tomb['sort'];
            $stmt = $conn->prepare($sql);
            $stmt->execute();
            $directories = $stmt->fetchAll();

            $isRootDir = true;

        } catch (PDOException $exception) {
            echo "<br>"."Error: ".$exception->getMessage();
        }
        $conn = null;

        return array(
            "files" => $files,
            "directories" => $directories,
            "isRootDir" => $isRootDir,
        );
    }

    public function createFolderHasNotParent($newFolderName, $creationDate): void
    {
        $conn = DB::getInstance();

        try {
            $sql = "INSERT INTO directories (dir_name, creation_date)
            VALUES ('$newFolderName', '$creationDate')";
            $conn->exec($sql);
        } catch (PDOException $exception) {
            print "<br>".$exception->getMessage();
        }
        $conn = null;

    }

    public function createFolderHasParent($id, $newFolderName, $creationDate): void
    {
        $conn = DB::getInstance();

        try {
            $sql = "INSERT INTO directories (parent_id, dir_name, creation_date)
                VALUES ('$id', '$newFolderName', '$creationDate')";
            $conn->exec($sql);
        } catch (PDOException $exception) {
            echo "<br>"."Error: ".$exception->getMessage();
        }
        $conn = null;

    }

    public function deleteFolder($id): void
    {
        $conn = DB::getInstance();

        try {
            $sql = "SELECT * FROM directories WHERE parent_id=:deleteItemId";
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(':deleteItemId', $id);
            $stmt->execute();
            $deleteFoldersInFolder = $stmt->fetchAll();

            $sql = "SELECT * FROM files WHERE dir_id = :deleteItemId";
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(':deleteItemId', $id);
            $stmt->execute();
            $deleteFilesInFolder = $stmt->fetchAll();

            foreach ($deleteFilesInFolder as $item) {
                try {
                    $dirPath = $_SERVER["DOCUMENT_ROOT"];
                    $deleteFileId = $item['file_id'];
                    // get delete from storage rel path
                    $sql = "SELECT generated_name FROM files WHERE file_id=:deleteFileId";
                    $stmt = $conn->prepare($sql);
                    $stmt->bindParam(':deleteFileId', $deleteFileId);
                    $stmt->execute();
                    $result = $stmt->fetch(PDO::FETCH_ASSOC);
                    $deleteStorageRelPath = $result['generated_name'];

                    // delete from db
                    $sql = "DELETE FROM files WHERE file_id = '$deleteFileId'";
                    $conn->exec($sql);
                } catch (PDOException $exception) {
                    echo "<br>"."Error: ".$exception->getMessage();
                }

                try {
                    $absPathForDeleteStorage = $dirPath . "/uploads/" . $deleteStorageRelPath;
                    unlink($absPathForDeleteStorage);
                } catch (Exception $exception) {
                    echo "<br>"."Error: ".$exception->getMessage();
                }

            }

            if ($deleteFoldersInFolder) {
                foreach ($deleteFoldersInFolder as $item) {
                    $dirId = $item['dir_id'];
                    $this->deleteFolder($dirId);
                }

            }

        } catch (PDOException $exception) {
            echo "<br>"."Error: ".$exception->getMessage();
        }

        try {
            $sql = "DELETE FROM directories WHERE dir_id='$id'";
            $conn->exec($sql);
        } catch (PDOException $exception){
            print "<br>".$exception->getMessage();
        }
        $conn = null;

    }


    private function orderBy(): array
    {
        if (isset($_GET['sort'])) {
            $sort = $_GET['sort'];
        } else {
            $sort = 'ASC';
        }
        if (isset($_GET['filterBy'])) {
            $filterBy = $_GET['filterBy'];
        } else {
            $filterBy = 'file_name';
        }

        return [
            'sort' => $sort,
            'filterBy' => $filterBy
        ];
    } // helper

    private function translateFilterBy($filterBy): string
    {
        switch ($filterBy) {
            case 'file_name':
                $filterBy = 'dir_name';
                break;
            default:
                break;
        }

        return $filterBy;
    } // translate filterBy (helper)

}