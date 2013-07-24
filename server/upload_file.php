<?php
// Upload a file to the server

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
	$uploadPath = 'upload/'.$_FILES['file']['name'];

	if (!file_exists($uploadPath)) {
		move_uploaded_file($_FILES['file']['tmp_name'], $uploadPath);
	} else {
		echo 'File already exists';
	}
}




?>