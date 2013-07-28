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
	exit;
}

if (isset($_POST['multi']) && $_POST['multi'] == '1') {
	$tmpPath = $tmpDir.$_FILES['file']['name'];
	
	if ($_POST['part'] == '1') {
		move_uploaded_file($_FILES['file']['tmp_name'], $tmpPath);
	} else {
		file_put_contents($tmpPath, file_get_contents($_FILES['file']['tmp_name']), FILE_APPEND);
	}

	if (isset($_POST['last']) && $_POST['last'] == '1') {
		$uploadFilePath = $uploadDir.$_POST['full_path'];
		$uploadFileDir = dirname($uploadFilePath);

		if (!file_exists($uploadFilePath)) {
			if (!file_exists($uploadFileDir)) {
				mkdir($uploadFileDir, 0777, true);
			}
			rename($tmpPath, $uploadFilePath);
		} else {
			echo 'File already exists';
		}
	}
	exit;
}

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




?>