<?php
/**
 * @package     VikMailSMTP
 * @subpackage  helpers.cypher
 * @author      E4J s.r.l.
 * @copyright   Copyright (C) 2018 E4J s.r.l. All Rights Reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 * @link        https://vikwp.com
 */

// No direct access
defined('ABSPATH') or die('No script kiddies please!');

/**
* Cypher Class for Vik Mail SMTP
*/
final class VikMailSMTPCypher
{
	/**
	 * Returns the hash salt for encryption.
	 * 
	 * @return 	string 	the salt string
	 */
	public static function getSalt($md5 = true)
	{
		return $md5 ? md5(__CLASS__) : __CLASS__;
	}

	/**
	 * Encodes a value that contains sensitive data.
	 * 
	 * @param 	string 	$value 	the value to encode
	 * 
	 * @return 	string 	the encoded string
	 */
	public static function encode($value)
	{
		return base64_encode(self::getSalt().base64_encode((string)$value));
	}

	/**
	 * Decodes a value previously encoded
	 * 
	 * @param 	string 	$value 	the encoded value
	 * 
	 * @return 	string 	the decoded string
	 */
	public static function decode($value)
	{
		if (empty($value)) {
			return $value;
		}

		$value = str_replace(self::getSalt(), '', base64_decode((string)$value));
		
		return base64_decode($value);
	}
}
