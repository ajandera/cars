<?php
// sql  ALTER TABLE `core_zastavy` ADD `image_session_id` VARCHAR(20) NULL AFTER `source_hash`;

// check api aceess
if (isset($_GET['apiaccess']) && $_GET['apiaccess']==$C->APIACCESS)
{
	// check if post is awailable
	if (isset($_POST) && !empty($_POST))
	{
		// check send data
		if (!isset($_POST['session_id']))
		{
			echo 'missing data';
			exit;
		}

		if (!isset($_POST['images']))
		{
			echo 'missing images';
			exit;
		}


		// get object by session id
		if($findOrders = coreDBSel('SELECT id FROM '.$C->db_prefix.'core_zastavy WHERE image_session_id = ? ',array(clear_str($_POST['image_session_id'],false))))
		{
			if($row = $findBrooker->fetchRow())
			{
				$recordId = (int)$row['id'];
				// create folder if not existst
				$uploadDir = __DIR__ . "/../images/" . $recordId;
				if (!is_dir($uploadDir)) {
					mkdir($uploadDir, 0777, true);
				}

				// process uploaded files
				$uploadedFiles = [];
				foreach ($_FILES['images']['tmp_name'] as $key => $tmpName) {
					if ($_FILES['images']['error'][$key] === UPLOAD_ERR_OK) {
						$filename = basename($_FILES['images']['name'][$key]);
						$targetFile = $uploadDir . "/" . $filename;

						if (move_uploaded_file($tmpName, $targetFile)) {
							$uploadedFiles[] = $filename;
						}
					}
				}

				// send response
				echo json_encode([
					"status" => "success",
					"record_id" => $recordId,
					"session_id" => $sessionId,
					"files" => $uploadedFiles
				]);
			}
		}
	}
	else
	{
		echo 'empty data';
		exit;
	}
}
else
{
	echo 'unauthorized access';
	exit;
}

exit;