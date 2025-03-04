<?php
namespace app\controllers;

use app\models\FileModel;


class FileController
{
    public function uploadFileChunks(): void
    {
        $dirPath = $_SERVER["DOCUMENT_ROOT"];
        $chunkNumber = $_POST['chunkNumber'];

        //upload chunks
        if (isset($_FILES['file'])) {
            $fileName = $_POST['origFileName'];
            $fileTmpName = $_FILES['file']['tmp_name'];
            $targetFilePath = $dirPath . "/uploads/". $fileName . '.part' . $chunkNumber;
            if (move_uploaded_file($fileTmpName, $targetFilePath)) {
//            echo "Chunk $chunkNumber uploaded successfully";
            } else {
                http_response_code(500);
                echo "Error uploading chunk $chunkNumber";
                exit();
            }
        }
    } // upload 1
    public function finishUploadFile(): void
    {
        $fileModel = new FileModel();
        $dirPath = $_SERVER["DOCUMENT_ROOT"];

        $fileName = $_POST['origFileName'];
        $totalChunks = $_POST['totalChunks'];
        $openFolderId = $_POST['openFolderId'] ?: NULL;

        // create folder on storage
        $uploadFolderName = date("Ymd", time());
        $path = $dirPath."/uploads/" . $uploadFolderName;

        if (!file_exists($path)) {
            mkdir($path);
        }

        // finish upload
        $outputFilePath = $path . "/" . $fileName;
        $outputFile = fopen($outputFilePath, 'wb');
        if ($outputFile === false) {
            print "Hiba";
            print $outputFilePath;
            exit();
        }

        for ($i = 1; $i <= $totalChunks; $i++) {
            $chunkFilePath = $dirPath . "/uploads/" . $fileName . '.part' . $i;
            $chunk = file_get_contents($chunkFilePath);
            fwrite($outputFile, $chunk);
            unlink($chunkFilePath);
        }

        fclose($outputFile);

        //file uploaad to database
        $filePathForDb = $path . "/" . $fileName;
        $fileSizeForDb = filesize($filePathForDb);
        $creationDateForDb = date('Y-m-d H:i:s', filectime($filePathForDb));
        // create generated name for db and storage
        $file_name = pathinfo($fileName, PATHINFO_FILENAME);
        $extension = pathinfo($fileName, PATHINFO_EXTENSION);
        $slugName = $this->slugify($file_name);
        $generatedName = $uploadFolderName . "/" .rand(1, 9999) . "-" . $slugName . "." . $extension;
        try {
            $fileModel->uploadFile($openFolderId, $fileName, $creationDateForDb, $fileSizeForDb, $generatedName);
        } catch (PDOException $exception) {
            echo "<br>"."Error: ".$exception->getMessage();
        }

        // rename file on storage to slug (különben a fizikai tárhelyre csak egy file-t írogatna felül folyamatosan)
        $slugFilePath = $dirPath . "/uploads/" . $generatedName;
        rename($outputFilePath, $slugFilePath);

        print json_encode([
            "uploadFolderName" => $uploadFolderName,
        ]);

    } // upload 2
    public function downloadFile(int $dlFileId): void
    {
        $dirPath = $_SERVER["DOCUMENT_ROOT"];
        $fileModel = new FileModel();

        $dlFileData = $fileModel->downloadFile($dlFileId);

        $dlFilePath = $dirPath . "/uploads/" . $dlFileData['generatedName'];

        if (file_exists($dlFilePath)) {
            header('Content-Description: File Transfer');
            header('Content-Type: application/octet-stream');
            header('Content-Disposition: attachment; filename="'.basename($dlFileData['fileName']).'"');
            header('Expires: 0');
            header('fileName: '.basename($dlFileData['fileName']));
            header('Cache-Control: must-revalidate');
            header('Pragma: public');
            header('Content-Length: ' . filesize($dlFilePath));
            readfile($dlFilePath);
            exit;
        }

    } // download file
    public function deleteFile(int $deleteFileId): void
    {
        $fileModel = new FileModel();
        $dirPath = $_SERVER["DOCUMENT_ROOT"];

        try {
            // delete from db
            $result = $fileModel->deleteFile($deleteFileId);

            // delete from storage
            $deleteFilePath = $dirPath . "/uploads/" . $result['generated_name'];
            unlink($deleteFilePath);
        } catch (PDOException $exception) {
            print "<br>".$exception->getMessage();
        } catch (Exception $exception) {
            echo "Error: ".$exception->getMessage();
        }

    } // delete file

    private function slugify($text, $ampersand = 'and'): string
    {
        $text = iconv('UTF-8', 'ASCII//TRANSLIT', $text);

        $text = preg_replace('/[^\w\s-]/', '', $text);
        $text = preg_replace('/[\s]+/', '_', $text);
        $text = str_replace('&', '_' . $ampersand . '_', $text);
        $text = preg_replace('/[-_]{2,}/', '_', $text);
        $text = trim($text, '_');
        $text = strtolower($text);

        return $text;
    } // helper

}