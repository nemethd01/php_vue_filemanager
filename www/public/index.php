<?php

$build = false;
$manifestContent = json_decode(file_get_contents("build/manifest.json", true), true);
//var_dump($manifestContent["src/main.js"]);
$jsFile = $manifestContent["src/main.js"]["file"];
$cssFile = $manifestContent["src/CSS/main.css"]["file"];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <?php
    if ($build) {
        $href = "http://filemanager.local/build/".$cssFile;
        $src = "http://filemanager.local/build/".$jsFile;
    } else {
        $href = "http://filemanager.local:8000/src/CSS/style.css";
        $src = "http://filemanager.local:8000/src/main.js";
    }
    ?>
    <link rel="stylesheet" href="<?php print $href ?>">
    <meta charset="UTF-8">
    <title>File manager</title>
</head>
<body>
  <div id="app"></div>
  <script type="module" src="<?php print $src ?>"></script>
</body>
</html>