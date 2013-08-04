<?php
require_once('../includes/variables.php');
class API
{
	protected $user;
	protected $userDir;
	protected $tmpUploadDir;

	public function __construct($user) {
		$this->user = $user;
		$this->userDir = $GLOBALS['userDir'];
		$this->userDir .= $user.'/';
		$this->tmpUploadDir = $GLOBALS['tmpUploadDir'];
		$this->tmpUploadDir .= $user.'/';
	}

	private function sendResponse($response)
	{
		return json_encode($response);
	}

	public function uploadfile($FILES, $POST)
	{


		$response = array('action' => 'uploadfile');

		if (!isset($FILES['file'])) {
			$response['error'] = 'Parameter error: no file';
			return self::sendResponse($respose);
		}

		$uploadPath = $this->userDir.$POST['full_path'];
		$uploadDir = dirname($uploadPath);

		echo $uploadPath, ' ', $uploadDir, "\n";

		if (!file_exists($uploadDir)) {
			echo 'uploadDir doesnt exist, creating it';
			if (!mkdir($uploadDir, 0777, true)) {
				$response['error'] = 'Unable to create directory';
				return self::sendResponse($response);
			}
		}

		if (!move_uploaded_file($FILES['file']['tmp_name'], $uploadPath))
			$response['error'] = 'Unable to move file';
		return self::sendResponse($response);
	}

	// multipart upload
	public function multiuploadfile($FILES, $POST)
	{
		$response = array('action' => 'multiuploadfile');

		if (!isset($FILES['file'])) {
			$response['error'] = 'Parameter error: no file';
			return self::sendResponse($response);
		}

		if (!isset($POST['part'])) {
			$response['error'] = 'Parameter error: no part';
			return self::sendResponse($response);
		}

		$tmpUploadPath = $this->tmpUploadDir.$FILES['file']['name'];
		$tmpUploadDir = dirname($tmpUploadPath);
		// if first part, move to temporary upload path, else append to temporary upload path
		if ($POST['part'] == '1') {
			if (!file_exists($tmpUploadDir)) {
				if (!mkdir($tmpUploadDir, 0777, true)) {
					$response['error'] = 'Unable to create directory';
					return self::sendResponse($response);
				}
			}

			if (!move_uploaded_file($FILES['file']['tmp_name'], $tmpUploadPath)) {
				$response['error'] = 'Unable to move file';
				return self::sendResponse($response);
			}
		}
		else {
			$contents = file_get_contents($FILES['file']['tmp_name']);
			if ($contents === false) {
				$response['error'] = 'Unable to read file contents';
				return self::sendResponse($response);
			}
			
			if (file_put_contents($tmpUploadPath, $contents, FILE_APPEND) === false) {
				$reponse['error'] = 'Unable to write contents of file';
				return self::sendResponse($response);
			}
		}

		if ($POST['last'] == '1') {
			$uploadPath = $this->userDir.$POST['full_path'];
			$uploadDir = dirname($uploadPath);

			// make sure folder exists
			if (!file_exists($uploadDir)) {
				if (!mkdir($uploadDir, 0777, true)) {
					$response['error'] = 'Unable to create directory';
					return self::sendResponse($response);
				}
			}

			// delete file if it exists
			if (file_exists($uploadPath)) {
				if (!unlink($uploadPath)) {
					$response['error'] = 'Unable to delete outdated file';
					return self::sendResponse($response);
				}
			}

			if (rename($tmpUploadPath, $uploadPath))
				return self::sendResponse($response);
			else {
				$response['error'] = 'Unable to move file';
				return self::sendResponse($response);
			}
		}
	}

	public function downloadfile($POST)
	{
		$response = array('action' => 'downloadfile');

		if (!isset($POST['path'])) {
			$response['error'] = 'Parameter error: no path';
			return self::sendResponse($response);
		}

		$filePath = $this->userDir.$POST['path'];

		if (!file_exists($filePath)) {
			$response['error'] = 'Value error: invalid path';
			return self::sendResponse($response);
		}

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
		exit();
	}


}