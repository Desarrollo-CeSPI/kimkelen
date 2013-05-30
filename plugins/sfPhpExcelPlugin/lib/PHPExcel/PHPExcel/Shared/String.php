<?php
/**
 * PHPExcel
 *
 * Copyright (c) 2006 - 2010 PHPExcel
 *
 * This library is free software; you can redistribute it and/or
 * modify it under the terms of the GNU Lesser General Public
 * License as published by the Free Software Foundation; either
 * version 2.1 of the License, or (at your option) any later version.
 *
 * This library is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU
 * Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public
 * License along with this library; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301  USA
 *
 * @category   PHPExcel
 * @package    PHPExcel_Shared
 * @copyright  Copyright (c) 2006 - 2010 PHPExcel (http://www.codeplex.com/PHPExcel)
 * @license    http://www.gnu.org/licenses/old-licenses/lgpl-2.1.txt	LGPL
 * @version    ##VERSION##, ##DATE##
 */


/**
 * PHPExcel_Shared_String
 *
 * @category   PHPExcel
 * @package    PHPExcel_Shared
 * @copyright  Copyright (c) 2006 - 2010 PHPExcel (http://www.codeplex.com/PHPExcel)
 */
class PHPExcel_Shared_String
{
	/**	Constants				*/
	/**	Regular Expressions		*/
	//	Fraction
	const STRING_REGEXP_FRACTION	= '(-?)(\d+)\s+(\d+\/\d+)';


	/**
	 * Control characters array
	 *
	 * @var string[]
	 */
	private static $_controlCharacters = array();

	/**
	 * SYLK Characters array
	 *
	 * $var array
	 */
	private static $_SYLKCharacters = array();

	/**
	 * Decimal separator
	 *
	 * @var string
	 */
	private static $_decimalSeparator;

	/**
	 * Thousands separator
	 *
	 * @var string
	 */
	private static $_thousandsSeparator;

	/**
	 * Is mbstring extension avalable?
	 *
	 * @var boolean
	 */
	private static $_isMbstringEnabled;

	/**
	 * Is iconv extension avalable?
	 *
	 * @var boolean
	 */
	private static $_isIconvEnabled;

	/**
	 * Build control characters array
	 */
	private static function _buildControlCharacters() {
		for ($i = 0; $i <= 31; ++$i) {
			if ($i != 9 && $i != 10 && $i != 13) {
				$find = '_x' . sprintf('%04s' , strtoupper(dechex($i))) . '_';
				$replace = chr($i);
				self::$_controlCharacters[$find] = $replace;
			}
		}
	}

	/**
	 * Build SYLK characters array
	 */
	private static function _buildSYLKCharacters()
	{
		self::$_SYLKCharacters = array(
			' 0'  => chr(0),
			' 1'  => chr(1),
			' 2'  => chr(2),
			' 3'  => chr(3),
			' 4'  => chr(4),
			' 5'  => chr(5),
			' 6'  => chr(6),
			' 7'  => chr(7),
			' 8'  => chr(8),
			' 9'  => chr(9),
			' :'  => chr(10),
			' ;'  => chr(11),
			' <'  => chr(12),
			' :'  => chr(13),
			' >'  => chr(14),
			' ?'  => chr(15),
			'!0'  => chr(16),
			'!1'  => chr(17),
			'!2'  => chr(18),
			'!3'  => chr(19),
			'!4'  => chr(20),
			'!5'  => chr(21),
			'!6'  => chr(22),
			'!7'  => chr(23),
			'!8'  => chr(24),
			'!9'  => chr(25),
			'!:'  => chr(26),
			'!;'  => chr(27),
			'!<'  => chr(28),
			'!='  => chr(29),
			'!>'  => chr(30),
			'!?'  => chr(31),
			"'?"  => chr(127),
			'(0'  => '€', // 128 in CP1252
			'(2'  => '‚', // 130 in CP1252
			'(3'  => 'ƒ', // 131 in CP1252
			'(4'  => '„', // 132 in CP1252
			'(5'  => '…', // 133 in CP1252
			'(6'  => '†', // 134 in CP1252
			'(7'  => '‡', // 135 in CP1252
			'(8'  => 'ˆ', // 136 in CP1252
			'(9'  => '‰', // 137 in CP1252
			'(:'  => 'Š', // 138 in CP1252
			'(;'  => '‹', // 139 in CP1252
			'Nj'  => 'Œ', // 140 in CP1252
			'(>'  => 'Ž', // 142 in CP1252
			')1'  => '‘', // 145 in CP1252
			')2'  => '’', // 146 in CP1252
			')3'  => '“', // 147 in CP1252
			')4'  => '”', // 148 in CP1252
			')5'  => '•', // 149 in CP1252
			')6'  => '–', // 150 in CP1252
			')7'  => '—', // 151 in CP1252
			')8'  => '˜', // 152 in CP1252
			')9'  => '™', // 153 in CP1252
			'):'  => 'š', // 154 in CP1252
			');'  => '›', // 155 in CP1252
			'Nz'  => 'œ', // 156 in CP1252
			')>'  => 'ž', // 158 in CP1252
			')?'  => 'Ÿ', // 159 in CP1252
			'*0'  => ' ', // 160 in CP1252
			'N!'  => '¡', // 161 in CP1252
			'N"'  => '¢', // 162 in CP1252
			'N#'  => '£', // 163 in CP1252
			'N('  => '¤', // 164 in CP1252
			'N%'  => '¥', // 165 in CP1252
			'*6'  => '¦', // 166 in CP1252
			"N'"  => '§', // 167 in CP1252
			'NH ' => '¨', // 168 in CP1252
			'NS'  => '©', // 169 in CP1252
			'Nc'  => 'ª', // 170 in CP1252
			'N+'  => '«', // 171 in CP1252
			'*<'  => '¬', // 172 in CP1252
			'*='  => '­', // 173 in CP1252
			'NR'  => '®', // 174 in CP1252
			'*?'  => '¯', // 175 in CP1252
			'N0'  => '°', // 176 in CP1252
			'N1'  => '±', // 177 in CP1252
			'N2'  => '²', // 178 in CP1252
			'N3'  => '³', // 179 in CP1252
			'NB ' => '´', // 180 in CP1252
			'N5'  => 'µ', // 181 in CP1252
			'N6'  => '¶', // 182 in CP1252
			'N7'  => '·', // 183 in CP1252
			'+8'  => '¸', // 184 in CP1252
			'NQ'  => '¹', // 185 in CP1252
			'Nk'  => 'º', // 186 in CP1252
			'N;'  => '»', // 187 in CP1252
			'N<'  => '¼', // 188 in CP1252
			'N='  => '½', // 189 in CP1252
			'N>'  => '¾', // 190 in CP1252
			'N?'  => '¿', // 191 in CP1252
			'NAA' => 'À', // 192 in CP1252
			'NBA' => 'Á', // 193 in CP1252
			'NCA' => 'Â', // 194 in CP1252
			'NDA' => 'Ã', // 195 in CP1252
			'NHA' => 'Ä', // 196 in CP1252
			'NJA' => 'Å', // 197 in CP1252
			'Na'  => 'Æ', // 198 in CP1252
			'NKC' => 'Ç', // 199 in CP1252
			'NAE' => 'È', // 200 in CP1252
			'NBE' => 'É', // 201 in CP1252
			'NCE' => 'Ê', // 202 in CP1252
			'NHE' => 'Ë', // 203 in CP1252
			'NAI' => 'Ì', // 204 in CP1252
			'NBI' => 'Í', // 205 in CP1252
			'NCI' => 'Î', // 206 in CP1252
			'NHI' => 'Ï', // 207 in CP1252
			'Nb'  => 'Ð', // 208 in CP1252
			'NDN' => 'Ñ', // 209 in CP1252
			'NAO' => 'Ò', // 210 in CP1252
			'NBO' => 'Ó', // 211 in CP1252
			'NCO' => 'Ô', // 212 in CP1252
			'NDO' => 'Õ', // 213 in CP1252
			'NHO' => 'Ö', // 214 in CP1252
			'-7'  => '×', // 215 in CP1252
			'Ni'  => 'Ø', // 216 in CP1252
			'NAU' => 'Ù', // 217 in CP1252
			'NBU' => 'Ú', // 218 in CP1252
			'NCU' => 'Û', // 219 in CP1252
			'NHU' => 'Ü', // 220 in CP1252
			'-='  => 'Ý', // 221 in CP1252
			'Nl'  => 'Þ', // 222 in CP1252
			'N{'  => 'ß', // 223 in CP1252
			'NAa' => 'à', // 224 in CP1252
			'NBa' => 'á', // 225 in CP1252
			'NCa' => 'â', // 226 in CP1252
			'NDa' => 'ã', // 227 in CP1252
			'NHa' => 'ä', // 228 in CP1252
			'NJa' => 'å', // 229 in CP1252
			'Nq'  => 'æ', // 230 in CP1252
			'NKc' => 'ç', // 231 in CP1252
			'NAe' => 'è', // 232 in CP1252
			'NBe' => 'é', // 233 in CP1252
			'NCe' => 'ê', // 234 in CP1252
			'NHe' => 'ë', // 235 in CP1252
			'NAi' => 'ì', // 236 in CP1252
			'NBi' => 'í', // 237 in CP1252
			'NCi' => 'î', // 238 in CP1252
			'NHi' => 'ï', // 239 in CP1252
			'Ns'  => 'ð', // 240 in CP1252
			'NDn' => 'ñ', // 241 in CP1252
			'NAo' => 'ò', // 242 in CP1252
			'NBo' => 'ó', // 243 in CP1252
			'NCo' => 'ô', // 244 in CP1252
			'NDo' => 'õ', // 245 in CP1252
			'NHo' => 'ö', // 246 in CP1252
			'/7'  => '÷', // 247 in CP1252
			'Ny'  => 'ø', // 248 in CP1252
			'NAu' => 'ù', // 249 in CP1252
			'NBu' => 'ú', // 250 in CP1252
			'NCu' => 'û', // 251 in CP1252
			'NHu' => 'ü', // 252 in CP1252
			'/='  => 'ý', // 253 in CP1252
			'N|'  => 'þ', // 254 in CP1252
			'NHy' => 'ÿ', // 255 in CP1252
		);
	}

	/**
	 * Get whether mbstring extension is available
	 *
	 * @return boolean
	 */
	public static function getIsMbstringEnabled()
	{
		if (isset(self::$_isMbstringEnabled)) {
			return self::$_isMbstringEnabled;
		}

		self::$_isMbstringEnabled = function_exists('mb_convert_encoding') ?
			true : false;

		return self::$_isMbstringEnabled;
	}

	/**
	 * Get whether iconv extension is available
	 *
	 * @return boolean
	 */
	public static function getIsIconvEnabled()
	{
		if (isset(self::$_isIconvEnabled)) {
			return self::$_isIconvEnabled;
		}

		// Check that iconv exists
		// Sometimes iconv is not working, and e.g. iconv('UTF-8', 'UTF-16LE', 'x') just returns false,
		// we cannot use iconv when that happens
		// Also, sometimes iconv_substr('A', 0, 1, 'UTF-8') just returns false in PHP 5.2.0
		// we cannot use iconv in that case either (http://bugs.php.net/bug.php?id=37773)
		if (function_exists('iconv')
			&& @iconv('UTF-8', 'UTF-16LE', 'x')
			&& @iconv_substr('A', 0, 1, 'UTF-8') ) {

			self::$_isIconvEnabled = true;
		} else {
			self::$_isIconvEnabled = false;
		}

		return self::$_isIconvEnabled;
	}

	/**
	 * Convert from OpenXML escaped control character to PHP control character
	 *
	 * Excel 2007 team:
	 * ----------------
	 * That's correct, control characters are stored directly in the shared-strings table.
	 * We do encode characters that cannot be represented in XML using the following escape sequence:
	 * _xHHHH_ where H represents a hexadecimal character in the character's value...
	 * So you could end up with something like _x0008_ in a string (either in a cell value (<v>)
	 * element or in the shared string <t> element.
	 *
	 * @param 	string	$value	Value to unescape
	 * @return 	string
	 */
	public static function ControlCharacterOOXML2PHP($value = '') {
		if(empty(self::$_controlCharacters)) {
			self::_buildControlCharacters();
		}

		return str_replace( array_keys(self::$_controlCharacters), array_values(self::$_controlCharacters), $value );
	}

	/**
	 * Convert from PHP control character to OpenXML escaped control character
	 *
	 * Excel 2007 team:
	 * ----------------
	 * That's correct, control characters are stored directly in the shared-strings table.
	 * We do encode characters that cannot be represented in XML using the following escape sequence:
	 * _xHHHH_ where H represents a hexadecimal character in the character's value...
	 * So you could end up with something like _x0008_ in a string (either in a cell value (<v>)
	 * element or in the shared string <t> element.
	 *
	 * @param 	string	$value	Value to escape
	 * @return 	string
	 */
	public static function ControlCharacterPHP2OOXML($value = '') {
		if(empty(self::$_controlCharacters)) {
			self::_buildControlCharacters();
		}

		return str_replace( array_values(self::$_controlCharacters), array_keys(self::$_controlCharacters), $value );
	}

	/**
	 * Try to sanitize UTF8, stripping invalid byte sequences. Not perfect. Does not surrogate characters.
	 *
	 * @param string $value
	 * @return string
	 */
	public static function SanitizeUTF8($value)
	{
		if (self::getIsIconvEnabled()) {
			$value = @iconv('UTF-8', 'UTF-8', $value);
			return $value;
		}

		if (self::getIsMbstringEnabled()) {
			$value = mb_convert_encoding($value, 'UTF-8', 'UTF-8');
			return $value;
		}

		// else, no conversion
		return $value;
	}

	/**
	 * Check if a string contains UTF8 data
	 *
	 * @param string $value
	 * @return boolean
	 */
	public static function IsUTF8($value = '') {
		return utf8_encode(utf8_decode($value)) === $value;
	}

	/**
	 * Formats a numeric value as a string for output in various output writers forcing
	 * point as decimal separator in case locale is other than English.
	 *
	 * @param mixed $value
	 * @return string
	 */
	public static function FormatNumber($value) {
		if (is_float($value)) {
			return str_replace(',', '.', $value);
		}
		return (string) $value;
	}

	/**
	 * Converts a UTF-8 string into BIFF8 Unicode string data (8-bit string length)
	 * Writes the string using uncompressed notation, no rich text, no Asian phonetics
	 * If mbstring extension is not available, ASCII is assumed, and compressed notation is used
	 * although this will give wrong results for non-ASCII strings
	 * see OpenOffice.org's Documentation of the Microsoft Excel File Format, sect. 2.5.3
	 *
	 * @param string $value UTF-8 encoded string
	 * @return string
	 */
	public static function UTF8toBIFF8UnicodeShort($value)
	{
		// character count
		$ln = self::CountCharacters($value, 'UTF-8');

		// option flags
		$opt = (self::getIsIconvEnabled() || self::getIsMbstringEnabled()) ?
			0x0001 : 0x0000;

		// characters
		$chars = self::ConvertEncoding($value, 'UTF-16LE', 'UTF-8');

		$data = pack('CC', $ln, $opt) . $chars;
		return $data;
	}

	/**
	 * Converts a UTF-8 string into BIFF8 Unicode string data (16-bit string length)
	 * Writes the string using uncompressed notation, no rich text, no Asian phonetics
	 * If mbstring extension is not available, ASCII is assumed, and compressed notation is used
	 * although this will give wrong results for non-ASCII strings
	 * see OpenOffice.org's Documentation of the Microsoft Excel File Format, sect. 2.5.3
	 *
	 * @param string $value UTF-8 encoded string
	 * @return string
	 */
	public static function UTF8toBIFF8UnicodeLong($value)
	{
		// character count
		$ln = self::CountCharacters($value, 'UTF-8');

		// option flags
		$opt = (self::getIsIconvEnabled() || self::getIsMbstringEnabled()) ?
			0x0001 : 0x0000;

		// characters
		$chars = self::ConvertEncoding($value, 'UTF-16LE', 'UTF-8');

		$data = pack('vC', $ln, $opt) . $chars;
		return $data;
	}

	/**
	 * Convert string from one encoding to another. First try mbstring, then iconv, or no convertion
	 *
	 * @param string $value
	 * @param string $to Encoding to convert to, e.g. 'UTF-8'
	 * @param string $from Encoding to convert from, e.g. 'UTF-16LE'
	 * @return string
	 */
	public static function ConvertEncoding($value, $to, $from)
	{
		if (self::getIsIconvEnabled()) {
			$value = iconv($from, $to, $value);
			return $value;
		}

		if (self::getIsMbstringEnabled()) {
			$value = mb_convert_encoding($value, $to, $from);
			return $value;
		}

		// else, no conversion
		return $value;
	}

	/**
	 * Get character count. First try mbstring, then iconv, finally strlen
	 *
	 * @param string $value
	 * @param string $enc Encoding
	 * @return int Character count
	 */
	public static function CountCharacters($value, $enc = 'UTF-8')
	{
		if (self::getIsIconvEnabled()) {
			$count = iconv_strlen($value, $enc);
			return $count;
		}

		if (self::getIsMbstringEnabled()) {
			$count = mb_strlen($value, $enc);
			return $count;
		}

		// else strlen
		$count = strlen($value);
		return $count;
	}

	/**
	 * Get a substring of a UTF-8 encoded string
	 *
	 * @param string $pValue UTF-8 encoded string
	 * @param int $start Start offset
	 * @param int $length Maximum number of characters in substring
	 * @return string
	 */
	public static function Substring($pValue = '', $pStart = 0, $pLength = 0)
	{
		if (self::getIsIconvEnabled()) {
			$string = iconv_substr($pValue, $pStart, $pLength, 'UTF-8');
			return $string;
		}

		if (self::getIsMbstringEnabled()) {
			$string = mb_substr($pValue, $pStart, $pLength, 'UTF-8');
			return $string;
		}

		// else substr
		$string = substr($pValue, $pStart, $pLength);
		return $string;
	}


	/**
	 * Identify whether a string contains a fractional numeric value,
	 *    and convert it to a numeric if it is
	 *
	 * @param string &$operand string value to test
	 * @return boolean
	 */
	public static function convertToNumberIfFraction(&$operand) {
		if (preg_match('/^'.self::STRING_REGEXP_FRACTION.'$/i', $operand, $match)) {
			$sign = ($match[1] == '-') ? '-' : '+';
			$fractionFormula = '='.$sign.$match[2].$sign.$match[3];
			$operand = PHPExcel_Calculation::getInstance()->_calculateFormulaValue($fractionFormula);
			return true;
		}
		return false;
	}	//	function convertToNumberIfFraction()

	/**
	 * Get the decimal separator. If it has not yet been set explicitly, try to obtain number
	 * formatting information from locale.
	 *
	 * @return string
	 */
	public static function getDecimalSeparator()
	{
		if (!isset(self::$_decimalSeparator)) {
			$localeconv = localeconv();
			self::$_decimalSeparator = $localeconv['decimal_point'] != ''
				? $localeconv['decimal_point'] : $localeconv['mon_decimal_point'];
				
			if (self::$_decimalSeparator == '')
			{
				// Default to .
				self::$_decimalSeparator = '.';
			}
		}
		return self::$_decimalSeparator;
	}

	/**
	 * Set the decimal separator. Only used by PHPExcel_Style_NumberFormat::toFormattedString()
	 * to format output by PHPExcel_Writer_HTML and PHPExcel_Writer_PDF
	 *
	 * @param string $pValue Character for decimal separator
	 */
	public static function setDecimalSeparator($pValue = '.')
	{
		self::$_decimalSeparator = $pValue;
	}

	/**
	 * Get the thousands separator. If it has not yet been set explicitly, try to obtain number
	 * formatting information from locale.
	 *
	 * @return string
	 */
	public static function getThousandsSeparator()
	{
		if (!isset(self::$_thousandsSeparator)) {
			$localeconv = localeconv();
			self::$_thousandsSeparator = $localeconv['thousands_sep'] != ''
				? $localeconv['thousands_sep'] : $localeconv['mon_thousands_sep'];
		}
		return self::$_thousandsSeparator;
	}

	/**
	 * Set the thousands separator. Only used by PHPExcel_Style_NumberFormat::toFormattedString()
	 * to format output by PHPExcel_Writer_HTML and PHPExcel_Writer_PDF
	 *
	 * @param string $pValue Character for thousands separator
	 */
	public static function setThousandsSeparator($pValue = ',')
	{
		self::$_thousandsSeparator = $pValue;
	}

	/**
	 * Convert SYLK encoded string to UTF-8
	 *
	 * @param string $pValue
	 * @return string UTF-8 encoded string
	 */
	public static function SYLKtoUTF8($pValue = '')
	{
		// If there is no escape character in the string there is nothing to do
		if (strpos($pValue, '') === false) {
			return $pValue;
		}

		if(empty(self::$_SYLKCharacters)) {
			self::_buildSYLKCharacters();
		}

		foreach (self::$_SYLKCharacters as $k => $v) {
			$pValue = str_replace($k, $v, $pValue);
		}

		return $pValue;
	}

}
