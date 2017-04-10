<?php
/**
 * Created by PhpStorm.
 * User: ecorrons
 * Date: 07/04/17
 * Time: 17:01
 */

class randomPassword
{
	static public function generate($length = 8)
	{
		$chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789!@#$*()_=+;:,.?";
		$password = substr(str_shuffle($chars), 0, $length);

		return $password;
	}

}