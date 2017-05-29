<?php

	if (secCheckMethod('POST')) {
		$post = secGetInputArray(INPUT_POST);
		$error = [];
		if (secValidateToken($post['_once'], 300)) {
			$username = validEmail($post['username'])           ? $post['username'] : $error['brugernavn']  = 'Fejl i brugernavn';
			$password = validMixedBetween($post['password'], 4) ? $post['password'] : $error['adgangskode'] = 'Fejl i adgangskode';

			if (sizeof($error) === 0) {
				$stmt = $conn->prepare("SELECT id, adgangskode, email FROM brugere WHERE email = :email");
				$stmt->bindParam(':email', $username, PDO::PARAM_STR);
				if ($stmt->execute() && ($stmt->rowCount() === 1)) {
					$resultat = $stmt->fetch(PDO::FETCH_OBJ);
					if (!password_verify($password, $resultat->adgangskode)) {
						$error['adgangskode'] = 'Forkert adgangskode';
					}
					else {
						$_SESSION['userid'] = $resultat->id;
						$_SESSION['username'] = $resultat->email;
						header('Location: ?side=profil');
					}
				}
			}
		}
	}

?>
<h2>Log ind</h2>
<form action="" method="post">
	<?=secCreateTokenInput()?>
	<input type="text" name="username">
	<input type="password" name="password">
	<button type="submit">Log ind</button>
</form>