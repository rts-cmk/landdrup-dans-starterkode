<?php

	/**
	 * Validere bogstaver
	 *
	 * @param string $data
	 * @param int $min default 2
	 * @param int $max default 30
	 * @return boolean
	 */
	function validCharacter ($data, $min = 2, $max = 30){
		return (
			isset($data) && 
			strlen($data) >= $min && 
			strlen($data) <= $max && 
			preg_match("/[a-zæøåüöä]+$/i", $data)
		) ? true : false;
	}

	/**
	 * Validere dato uden tid
	 * 
	 * @param string $data
	 * @return boolean
	 */
	function validDate($data){
		return (isset($data) && (
					preg_match("~^\d{2}/\d{2}/\d{4}$~", $data) || 
					preg_match("~^\d{2}-\d{2}-\d{4}$~", $data) ||
					preg_match("~^\d{4}/\d{2}/\d{2}$~", $data) ||
					preg_match("~^\d{4}-\d{2}-\d{2}$~", $data)
					)
				) ? true : false;
	}
	/**
	 * Validere en string med minimum og maksimum.
	 *
	 * @param string $data
	 * @param int $min
	 * @param int $max
	 * @return boolean
	 */
	function validStringBetween($data, $min, $max){
		return (
			isset($data) && 
			preg_match("/[a-zæøåüöä 0-9,.]+$/i", $data) && 
			(strlen($data) >= $min) &&
			(strlen($data) <= $max)
		) ? true : false;
	}

	/**
	 * Validere en integer med minimum og maksimum.
	 *
	 * @param int $data
	 * @param int $min
	 * @param int $max
	 * @return boolean
	 */
	function validIntBetween($data, $min, $max){
		return(
			isset($data) && 
			is_numeric($data) && 
			(strlen($data) >= $min) && 
			(strlen($data) <= $max)
		) ? true : false;
	}

	/**
	 * Validerer en mixed variabel med et minimum og maksimum
	 *
	 * @param mixed $data
	 * @param int $min
	 * @param int $max
	 * @return boolean
	 */
	function validMixedBetween($data, $min, $max = 255) {
		return(
			isset($data) &&
			(strlen($data) >= $min) &&
			(strlen($data) <= $max)
		) ? true : false;
	}
	
	/**
	 * Validere telefon nummer
	 *
	 * @param string $number
	 * @return int
	 */
	function validTel($number){
		try{
			if(strlen($number) > 8){
				$number = str_replace(' ', '', $number);
				$number = preg_replace("/(^[+]\d{2} | ^00\d{2})/x", "", $number);
			}
			if(is_numeric($number) && strlen($number) == 8){
				return (int)$number;
			}
			return false;
		} catch (Expection $e){
			return $e->getMessage();
		}
	}

	/**
	 * Validere email
	 *
	 * @param string $mail
	 * @return boolean
	 */
	function validEmail($mail){
		return filter_var($mail, FILTER_VALIDATE_EMAIL);
	}

	/**
	 * Validere om 2 datatyper matcher hinanden
	 *
	 * @param mixed $x
	 * @param mixed $y
	 * @return boolean
	 */
	function validMatch($x, $y){
		return ($x === $y) ? true : false;
	}