<?php
	if (secCheckMethod('POST')) {
		$post = secGetInputArray(INPUT_POST);
		
		$error = [];

		if (!secValidateToken($post['_once'], 600)) {
			$error['session'] = 'Din session er udløbet. Prøv igen.';
		}

		if(isset($post['opretBruger'])){
			$fornavn     = validCharacter($post['fornavn']) ? $post['fornavn']           : $error['fornavn']     = 'Fejl i fornavn';
			$efternavn   = validCharacter($post['efternavn']) ? $post['efternavn']       : $error['efternavn']   = 'Fejl i efternavn';
			$fodselsdato = validDate($post['fodselsdato']) ? $post['fodselsdato']        : $error['fodselsdato'] = 'Fejl i fødselsdato';
			$adresse     = validStringBetween($post['adresse'], 2, 65) ? $post['adresse']: $error['adresse']     = 'Fejl i adresse';
			$postnr      = validIntBetween($post['postnr'], 4, 5) ? $post['postnr']      : $error['postnr']      = 'Fejl i postnr';
			$by          = validCharacter($post['city'], 2, 31) ? $post['city']          : $error['city']        = 'Fejl i by';
			$tel         = validTel($post['tlf']) ? $post['tlf']                         : $error['tlf']         = 'Fejl i tlf';
			$mail        = validEmail($post['email']) ? $post['email']                   : $error['email']       = 'Fejl i email';
			$adgangskode = validMatch($post['gentagKode'], $post['kode']) ? $post['kode']: $error['kodematch']   = 'Fejl i kode';
			$adgangskode = validMixedBetween($post['kode'], 4) ? $post['kode']           : $error['kodeformat']  = 'Ey, bro! du har laver fejl i den kode, der.';
			
			if(sizeof($error) === 0){
				if ($stmt = $conn->prepare("SELECT id FROM brugere WHERE email = :mail")) {
					$stmt->bindParam(':mail', $mail, PDO::PARAM_STR);
					if ($stmt->execute()) {
						if ($stmt->rowCount() > 0) {
							$error['brugerfindes'] = 'Den bruger, du prøver at oprette, ekstisterer allerede.';
						}
						else {
							$adgangskode = password_hash($adgangskode, PASSWORD_BCRYPT, ['cost' => 12]);
							if (!sqlQueryPrepared('
								INSERT INTO `profil`(`fornavn`, `efternavn`, `fodselsdato`, `adresse`, `postnr`, `city`, `tlf`) 
								VALUES (:fornavn,:efternavn,:fodselsdato,:adresse,:postnr,:city,:tlf);
								SELECT LAST_INSERT_ID() INTO @lastId;
								INSERT INTO `brugere`(`email`, `adgangskode`, `fkProfil`, `fkBrugerrolle`) 
								VALUES (:omg,:nice,@lastId,:wtf)
								',array(
									':fornavn' => $fornavn,
									':efternavn' => $efternavn,
									':fodselsdato' => $fodselsdato,
									':adresse' => $adresse,
									':postnr' => $postnr,
									':city' => $by,
									':tlf' => $tel,
									':omg' => $mail,
									':nice' => $adgangskode,
									':wtf' => 4
								))) {
									$error['brugeropret'] = 'Der skete en fejl ved oprettelse. SCRUB!';
								}
								else {
									header('Location: ?side=logind');
								}
						}
					}
					else {
						$error['generel'] = 1801; // execute fejl
					}
				}
				else {
					$error['generel'] = 1802; // prepare fejl
				}

			}
		}
		
	}
?>

<form action="?side=opretBruger" method="post">
	<?=secCreateTokenInput()?>
	<?=@$msg?>
	<fieldset>
		<legend>Profil:</legend>
		<label for="fornavn">Fornavn</label><br />
		<input type="text" min="2" max="30" name="fornavn" id="fornavn"><br />
		<?php
			if (isset($error['fornavn'])) echo '<div class="danger">'.$error['fornavn'].'</div>'.PHP_EOL;
		?>
		<label for="efternavn">Efternavn</label><br />
		<input type="text" min="2" max="30" name="efternavn" id="efternavn"><br />
		<?php
			if (isset($error['efternavn'])) echo '<div class="danger">'.$error['efternavn'].'</div>'.PHP_EOL;
		?>
		<label for="fodselsdato">Fødselsdato</label><br />
		<input type="date" name="fodselsdato" id="fodselsdato"><br />
		<?php
			if (isset($error['fodselsdato'])) echo '<div class="danger">'.$error['fodselsdato'].'</div>'.PHP_EOL;
		?>
		<label for="adresse">Adresse</label><br />
		<input type="text" min="2" max="65" name="adresse" id="adresse"><br />
		<?php
			if (isset($error['adresse'])) echo '<div class="danger">'.$error['adresse'].'</div>'.PHP_EOL;
		?>
		<label for="postnr">Post nr.</label><br />
		<input type="number" min="0" max="99999" name="postnr" id="postnr"><br />
		<?php
			if (isset($error['postnr'])) echo '<div class="danger">'.$error['postnr'].'</div>'.PHP_EOL;
		?>
		<label for="city">By</label><br />
		<input type="text" min="2" max="31" name="city" id="city"><br />
		<?php
			if (isset($error['city'])) echo '<div class="danger">'.$error['city'].'</div>'.PHP_EOL;
		?>
		<label for="tlf">Tlf.</label><br />
		<input type="tel" name="tlf" min="8" max="8" id="tlf"><br>
		<?php
			if (isset($error['tlf'])) echo '<div class="danger">'.$error['tlf'].'</div>'.PHP_EOL;
		?>
	</fieldset>
	<fieldset>
		<legend>Login oplysninger:</legend>
		<label for="email">E-mail</label><br />
		<input type="email" id="email" name="email"><br />
		<?php
			if (isset($error['email'])) echo '<div class="danger">'.$error['email'].'</div>'.PHP_EOL;
		?>
		<label for="kode">Adgangskode</label><br />
		<input type="password" name="kode" id="kode"><br />
		<?php
			if (isset($error['kodeformat'])) echo '<div class="danger">'.$error['kodeformat'].'</div>'.PHP_EOL;
		?>
		<label for="gentagKode">Gentag adgangskode</label><br />
		<input type="password" name="gentagKode" id="gentagKode"><br />
		<?php
			if (isset($error['kodematch'])) echo '<div class="danger">'.$error['kodematch'].'</div>'.PHP_EOL;
		?>
		<button type="submit" name="opretBruger">Opret</button>
	</fieldset>
</form>