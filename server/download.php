<?php
require_once('includes/helper_functions.php');
session_start();
validateLoggedIn();

// returns NULL if valid path
function validateDLPath($path)
{
	if (empty($path))
		return 'path cannot be empty';
	if (!file_exists($path))
		return 'path does not exist';
	if (is_dir($path))
		return 'path is not a file';
	if (mb_strpos($path, $_SESSION['user_path']) === FALSE)
		return 'invalid path';
	if (mb_strpos($path, '../') !== FALSE)
		return 'invalid path';
}

if (!isset($_GET['path'])) {
	echo 'DOwnload error: no path';
	exit();
}

$reqPath = $_GET['path'];
$filePath = slashes(realpath(joinPath($_SESSION['user_path'], $reqPath)));

$pathError = validateDLPath($filePath);
if ($pathError !== NULL) {
	echo 'Download error: '.$pathError;
	exit();
}


header('Content-Description: File Transfer');
header('Content-Type: application/octet-stream');
header('Content-Disposition: attachment; filename='.basename($filePath));
header('Content-Transfer-Encoding: binary');
header('Expires: 0');
header('Cache-Control: must-revalidate');
header('Pragma: public');
header('Content-Length: '.filesize($filePath));
ob_clean();
flush();
readfile($filePath);
exit();