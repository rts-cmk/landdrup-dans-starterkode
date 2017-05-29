<?php
	/**
	 * Overfør et enkelt billece til serveren
	 *
	 * @param mixed $inputFieldName
	 * @param string $folder default '../media'
	 * @param array $mimeType default ['image/jpeg', 'image/jpg', 'image/png', 'image/gif', 'image/bmp']
	 * @return array
	 */
	function mediaImageUploader($inputFieldName, $mimeType = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif', 'image/bmp'], $folder = '../media'){
		$uploadError = array(
			1 => 'Filens størrelse overskrider \'upload_max_filesize\' directivet i php.ini.',
			2 => 'Filen størrelse overskride \'MAX_FILE_SIZE\' directivet i HTML formen.',
			3 => 'File blev kun delvis uploadet.',
			4 => 'Filen blev ikke uploaded.',
			6 => 'Kunne ikke finde \'tmp\' mappen.',
			7 => 'Kunne ikke gemme filen på disken.',
			8 => 'A PHP extension stopped the file upload.'
		); 

		if($_FILES[$inputFieldName]['error'] === 0){
			$image = $_FILES[$inputFieldName];
			if(!in_array($image['type'], $mimeType)){
				return [
					'code' => false,
					'msg' => 'Ikke tiladt filtype'
				];
			}
			if (!file_exists($folder)) {
				mkdir($folder, 0755, true);
			}
			$imageName = time() . '_' . $image['name'];
			if(move_uploaded_file($image['tmp_name'], $folder . '/' . $imageName)){
				return [
					'code' => true,
					'type' => $image['type'],
					'name' => $imageName
				];
			}
		} else {
			return [
				'code' => false,
				'msg' => $uploadError[$_FILES[$inputFieldName]['error']]
			];
		}
	}