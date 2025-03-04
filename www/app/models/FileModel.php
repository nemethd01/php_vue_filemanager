<?php

namespace app\models;

use app\models\DB;
use PDO;

class FileModel
{
    public function uploadFile($parentId, $name, $creationDate, $size, $generatedName): void
    {
        $conn = DB::getInstance();

        $sql = "INSERT INTO files (dir_id, file_name, creation_date, file_size, generated_name)
        VALUES (:parentId, :name, :creationDate, :size, :generatedName)";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(":parentId", $parentId);
        $stmt->bindParam(":name", $name);
        $stmt->bindParam(":creationDate", $creationDate);
        $stmt->bindParam(":size", $size);
        $stmt->bindParam(":generatedName", $generatedName);
        $stmt->execute();

    }

    public function downloadFile($id): array
    {
        $conn = DB::getInstance();

        $sql= "SELECT * FROM files WHERE file_id=:downloadFileId";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(":downloadFileId", $id);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        $generatedName = $result['generated_name'];
        $fileName = $result['file_name'];

        return array(
            "generatedName" => $generatedName,
            "fileName" => $fileName,
        );

    }

    public function deleteFile($id)
    {
        $conn = DB::getInstance();

        $sql = "SELECT generated_name FROM files WHERE file_id=:id";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(":id", $id);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        $sql = "DELETE FROM files WHERE file_id='$id'";
        $conn->exec($sql);

        return $result;

    }

}