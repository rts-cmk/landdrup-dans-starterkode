<?php
	if(secCheckLevel() < 90){
		die();
	}
	if(secCheckMethod('POST')){
		$error   		= [];
		$post    		= secGetInputArray(INPUT_POST);
		$bruger 		= $post['bruger'] !== 0 ? $post['bruger'] 									: $error['bruger'] 		= 'fejl besked bruger!';
		$beskrivelse 	= validMixedBetween($post['beskrivelse'], 1, 511) ? $post['beskrivelse'] 	: $error['beskrivelse'] = 'fejl besked beskrivelse!';
		if(sizeof($error) === 0){
			$billede = mediaImageUploader('filUpload');
			if($billede['code']){
				sqlQueryPrepared(
					"
						INSERT INTO `media`(`sti`, `type`) VALUES (:sti, :type);
						SELECT LAST_INSERT_ID() INTO @lastId;
						INSERT INTO `instruktor`(`beskrivelse`, `fkMedia`, `fkProfil`) VALUES (:beskrivelse, @lastId, :fkProfil);
					",
					array(
						':sti' => $billede['name'],
						':type' => $billede['type'],
						':beskrivelse' => $beskrivelse,
						':fkProfil' => $bruger
					)
				);
			} else {
				$error['filUpload'] = $billede['msg'];
			}
		}
	} else {
			$stmt = $conn->prepare("SELECT brugere.id, profil.fornavn, profil.efternavn FROM `brugere`
									INNER JOIN `brugerroller` ON `brugere`.`fkBrugerrolle` = `brugerroller`.`id`
									INNER JOIN `profil` ON `profil`.`id` = `brugere`.`fkProfil`
									WHERE `brugerroller`.niveau >= 50");
			$stmt->execute();
			$result = $stmt->setFetchMode(PDO::FETCH_OBJ); 
	}
?>

<form action="" method="post" enctype="multipart/form-data">
	<fieldset>
		<legend>Instruktør</legend>
		<select name="bruger">
			<option value="0">Vælg en bruger</option>
			<?php foreach($stmt->fetchAll() as $value): ?>
				<option value="<?= $value->id ?>"><?= $value->fornavn . ' ' . $value->efternavn ?></option>
			<?php endforeach; ?>
		</select><br />
		<label for="beskrivelse">Beskrivelse</label><br />
		<textarea name="beskrivelse"></textarea><br />
		<label for="filUpload">Billed</label><br />
		<input name="filUpload" type="file"><br /><br />
		<button name="opretInstruktor" type="submit">Opret</button>
	</fieldset>
</form>
