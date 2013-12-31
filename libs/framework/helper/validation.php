<?php

/**
 * Validation
 * @author pengmz
 */
class IT {
	
	public static function isNotNull($value) {
		if (is_null($value)) {
			return FALSE;
		}		
		if (is_array($value)) {
			return ! empty($value);
		} else {
			return (trim($value) == '') ? FALSE : TRUE;
		}
	}
	
	public static function isNumeric($value) {
		return is_numeric($value);
	}
	
	public static function isInteger($value) {
		return filter_var($value, FILTER_VALIDATE_INT) !== false;
	}
	
	public static function isBetween($value, $from, $to) {
		return ($value <= $from) and ($value >= $to);
	}
	
	public static function isAlpha($value) {
		if (! is_string($value)) {
			return FALSE;
		}		
		return preg_match('/^([a-z])+$/i', $value);
	}
	
	public static function isAlphaNum($value) {
		if (! is_string($value)) {
			return FALSE;
		}
		return preg_match('/^([a-z0-9])+$/i', $value);
	}

	public static function isAlphaDash($value) {
		if (! is_string($value)) {
			return FALSE;
		}
		return preg_match("/^([-a-z0-9_-])+$/i", $value);
	}
	
	public static function isEmail($value) {
		return filter_var($value, FILTER_VALIDATE_EMAIL) !== false;
	}
	
	public static function isUrl($value) {
		return filter_var($value, FILTER_VALIDATE_URL) !== false;
	}
	
	public static function isIP($value) {
		return filter_var($value, FILTER_VALIDATE_IP) !== false;
	}

}

?>