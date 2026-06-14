<?php
$baseDir = __DIR__ . '/uploads';

if (!is_dir($baseDir)) {
    mkdir($baseDir, 0755, true);
}

function listFiles($dir, $baseDir)
{
    $items = scandir($dir);

    foreach ($items as $item) {
        if ($item === '.' || $item === '..') {
            continue;
        }

        $fullPath = $dir . DIRECTORY_SEPARATOR . $item;
        $relativePath = str_replace($baseDir . DIRECTORY_SEPARATOR, '', $fullPath);

        echo '<div class="item">';

        if (is_dir($fullPath)) {
            echo '📁 <strong>' . htmlspecialchars($relativePath) . '</strong>';
            listFiles($fullPath, $baseDir);
        } else {
            $url = 'uploads/' . str_replace('\\', '/', $relativePath);

            echo '📄 ' . htmlspecialchars($relativePath);
            echo ' - <a href="' . htmlspecialchars($url) . '" target="_blank">Buka</a>';
        }

        echo '</div>';
    }
}
?>
<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<title>File Browser</title>
<style>
body{
    font-family:Arial,sans-serif;
    background:#f5f5f5;
    margin:20px;
}
.container{
    background:#fff;
    padding:20px;
    border-radius:10px;
    box-shadow:0 0 10px rgba(0,0,0,.1);
}
.item{
    margin-left:20px;
    padding:3px;
}
a{
    text-decoration:none;
}
</style>
</head>
<body>
<div class="container">
<h2>Daftar File Upload</h2>
<?php listFiles($baseDir, $baseDir); ?>
</div>
</body>
</html>