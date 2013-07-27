<?php

include_once('includes/variables.php');

$reqFilePath = $_GET['path'];
$filePath = $uploadDir.$reqFilePath;

if (file_exists($filePath)) {
	header('Content-Description: File Transfer');
    header('Content-Type: application/octet-stream');
    header('Content-Disposition: attachment; filename='.basename($filePath));
    header('Content-Transfer-Encoding: binary');
    header('Expires: 0');
    header('Cache-Control: must-revalidate');
    header('Pragma: public');
    header('Content-Length: ' . filesize($filePath));
    ob_clean();
    flush();
    readfile($filePath);
    exit;
} else {
	echo 'File does not exist!';
}

?>