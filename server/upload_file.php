<?php
// Upload a file to the server

include_once('includes/variables.php');

echo '$_POST dump: ';
echo var_dump($_POST);
echo '
$_FILES dump: ';
echo var_dump($_FILES);
echo '
';

if (!isset($_FILES['file'])) {
	echo 'No file!';
} else {
	$uploadFilePath = $uploadDir.$_POST['full_path'];
	$uploadFileDir = dirname($uploadFilePath);


	if (!file_exists($uploadFilePath)) {
		if (!file_exists($uploadFileDir)) {
			mkdir($uploadFileDir, 0777, true);
		}
		move_uploaded_file($_FILES['file']['tmp_name'], $uploadFilePath);
	} else {
		echo 'File already exists';
	}
}




?>