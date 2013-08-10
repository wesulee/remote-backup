<?php
// view files in browser
require_once('includes/path_variables.php');
require_once('includes/helper_functions.php');
session_start();
validateLoggedIn();


function formatPath($path)
{
	if (empty($path))
		return ''; 		// default
	$path = str_replace('\\', '/', $path);
	$path = trim($path, '/');
	$path = preg_replace('/\/+/', '/', $path);
	return $path;
}

// returns NULL if path is acceptable
function validateFolderPath($path)
{
	if (!file_exists($path))
		return 'path does not exist';
	if (!is_dir($path))
		return 'path must be a folder';
	// make sure path accessing is inside of the user's storage folder
	if (mb_strpos($path, $_SESSION['user_path']) === FALSE)
		return 'invalid path';
	// do not allow ../ in path
	if (mb_strpos($path, '../') !== FALSE)
		return 'invalid path';
}


$reqPath = formatPath($_GET['path']);
$path = slashes(realpath(joinPath($_SESSION['user_path'], $reqPath)));


$pathError = validateFolderPath($path);
if ($pathError !== NULL) {
	echo $pathError;
	exit();
}

$folders = array();
$files = array();
$dh = opendir($path);

if ($dh === FALSE) {
	echo 'unable to open directory';
	exit();
}

while (($file = readdir($dh)) !== FALSE) {
	if (is_dir(joinPath($path, $file)))
		$folders[] = $file;
	else
		$files[] = $file;
}

// remove default folders
$excludeFolders = array('.', '..');
foreach ($excludeFolders as $remove) {
	if (($key = array_search($remove, $folders)) !== FALSE) {
    	unset($folders[$key]);
	}
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Browse</title>
</head>
<body>
<h3>File Browser</h3>
<p><?php
$divider = ' / ';

$pathsHTML = '';
$linkMap = genLinkPaths('browse.php', $_SESSION['user_path'], $path);
$linkMapLength = count($linkMap);
$linkMapIndex = 1;
foreach ($linkMap as $cLinkMap) {
	if ($linkMapIndex !== $linkMapLength)
		$pathsHTML .= '<a href="'.$cLinkMap['url'].'">'.$cLinkMap['folder'].'</a>'.$divider;
	else
		$pathsHTML .= '<a href="'.$cLinkMap['url'].'">'.$cLinkMap['folder'].'</a>';
	$linkMapIndex++;
}

echo $pathsHTML;
?></p>
<table border="1">
	<tr>
		<th>Type</th>
		<th>Name</th>
	</tr>
<?php
foreach ($folders as $folder) {
	echo '<tr>
	<td>folder</td>
	<td><a href="'.urlGET('browse.php', array('path' => joinPath($reqPath, $folder))).'">'.$folder.'</a></td>
</tr>';
}
foreach ($files as $file) {
	echo '<tr>
	<td>file</td>
	<td><a href="'.urlGET('download.php', array('path' => joinPath($reqPath, $file))).
	'" target="_blank">'.$file.'</a></td>
</tr>';
}
?>
</table>
</body>
</html>