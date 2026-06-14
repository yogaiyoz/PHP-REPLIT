<?php
$maxSize = 500 * 1024; // 500 KB
$uploadDir = "uploads/";

if (!is_dir($uploadDir)) {
    mkdir($uploadDir, 0777, true);
}

$message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_FILES["file"])) {

    if ($_FILES["file"]["error"] !== UPLOAD_ERR_OK) {
        $message = "Upload gagal!";
    } elseif ($_FILES["file"]["size"] > $maxSize) {
        $message = "Ukuran file melebihi 500 KB!";
    } else {

        $fileName = basename($_FILES["file"]["name"]);
        $targetFile = $uploadDir . time() . "_" . $fileName;

        if (move_uploaded_file($_FILES["file"]["tmp_name"], $targetFile)) {
            $url = (isset($_SERVER['HTTPS']) ? 'https' : 'http')
                . '://' . $_SERVER['HTTP_HOST']
                . dirname($_SERVER['PHP_SELF'])
                . '/' . $targetFile;

            $message = "Upload berhasil!<br><a href='$url' target='_blank'>$url</a>";
        } else {
            $message = "Gagal menyimpan file.";
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>PHP Uploader</title>
</head>
<body>

<h2>Upload File (Maksimal 500 KB)</h2>

<form method="post" enctype="multipart/form-data">
    <input type="file" name="file" required>
    <button type="submit">Upload</button>
</form>

<p><?php echo $message; ?></p>

</body>
</html>