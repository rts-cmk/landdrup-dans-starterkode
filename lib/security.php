<?php

/**
 * Tjekker request method
 *
 * @param string $method
 * @return boolean
 */
function secCheckMethod($method) {
	return (filter_input(INPUT_SERVER, 'REQUEST_METHOD', FILTER_SANITIZE_SPECIAL_CHARS) === strtoupper($method)) ? true : false;
}

/**
 * Returnér filtreret superglobal
 *
 * @param string $input
 * @return string
 */
function secGetInputArray($input) {
	return filter_input_array(strtoupper($input), FILTER_SANITIZE_SPECIAL_CHARS);
}

/**
 * Genererer et token, hvis der ikke i forvejen findes et
 *
 * @return void
 */
function secGenerateToken() {
	if (function_exists('random_bytes')) {
		$_SESSION['Token'] = bin2hex(random_bytes(32));
	} elseif (function_exists('mcrypt_create_iv')) {
		$_SESSION['Token'] = bin2hex(mcrypt_create_iv(32, MCRYPT_DEV_URANDOM));
	} else {
		$_SESSION['Token'] = bin2hex(openssl_random_pseudo_bytes(32));
	}
	$_SESSION['TokenAge'] = time();
}

/**
 * Opretter et skjult input-felt med navn _once og value token
 *
 * @return string html entity
 */
function secCreateTokenInput() {
	secGenerateToken();
	return '<input name="_once" type="hidden" value="'.$_SESSION['Token'].'">';
}

/**
 * Valider token og alder
 *
 * @param string $token
 * @param int $maxAge sekunder, default 300
 * @return boolean
 */
function secValidateToken($token, $maxAge = 300) {
	if ($token != $_SESSION['Token'] || (time() - $_SESSION['TokenAge']) > $maxAge) {
		return false;
	}
	else {
		unset($_SESSION['Token'], $_SESSION['TokenAge']);
		return true;
	}
}

/**
 * Tjekker om en bruger er logget ind
 *
 * @return boolean
 */
function secIsLoggedIn() {
	if (isset($_SESSION['userid']) && isset($_SESSION['username']) &&
	    !empty($_SESSION['userid']) && !empty($_SESSION['username'])) {
			global $conn;
			$stmt = $conn->prepare("SELECT id FROM brugere WHERE email = :email AND id = :id");
			$stmt->bindParam(':email', $_SESSION['username'], PDO::PARAM_STR);
			$stmt->bindParam(':id', $_SESSION['userid'], PDO::PARAM_INT);
			return ($stmt->execute() && $stmt->rowCount() === 1) ? true : false;
		}
		else {
			return false;
		}
}

/**
 * Find brugerens niveau fra db
 *
 * @return integer
 */
function secCheckLevel(){
		global $conn;
		$stmt = $conn->prepare("SELECT brugerroller.niveau FROM `brugere`
								INNER JOIN `brugerroller` ON `brugerroller`.`id` = `brugere`.`fkBrugerrolle`
								WHERE `brugere`.`email` = :mail");
    	$stmt->bindParam(':mail', $_SESSION['username'], PDO::PARAM_STR);
		$stmt->execute();
    	if($stmt->rowCount() === 1){
			$result = $stmt->fetch(PDO::FETCH_OBJ);
			return $result->niveau;
		} else {
			return 0;
		}
}