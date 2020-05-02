<?php

/**
 * Utils class
 *
 * @author plamen
 */
class Utils {

	/**
	 * Formats the date into javascript friendly format (from "2016-01-01 15:00:00" into "2016-01-01T15:00:00")
	 * @param string $input
	 * @return string
	 */
	public static function formatDate($input){
		return preg_replace('/\s/', 'T', $input);
	}
}
