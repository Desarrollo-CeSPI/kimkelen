<?php

/**
 * sfNumberFormat class file.
 *
 * @author     oweitman
 * @package    symfony
 * @subpackage i18n
 */

/**
 * sfNumberFormat class.
 *
 *
 */
class sfNumberFormatPlus extends sfNumberFormat
{
  /**
   * Returns the normalized number from a localized one
   * Parsing depends on given locale (grouping and decimal)
   *
   * Examples for input:
   * '2345.4356,1234' = 23455456.1234
   * '+23,3452.123' = 233452.123
   * '12343 ' = 12343
   * '-9456' = -9456
   * '0' = 0
   *
   * @param  string $input    Input string to parse for numbers
   * @param  array  $options  Options: locale, precision. See {@link setOptions()} for details.
   * @return string Returns the extracted number
   * @throws Zend_Locale_Exception
  */
  public static function getNumber($value, $culture)
  {
    if (!is_string($value)) {
      return $value;
    }
    
		if (!self::isNumber($value,  $culture)) {
		    throw new sfException('No localized value in ' . $value . ' found, or the given number does not match the localized format');
		}
        
    $num_format = sfCultureInfo::getInstance($culture)->getNumberFormat();

    if ((strpos($value, $num_format->getNegativeSign()) !== false) ||
    (strpos($value, '-') !== false)) {
      $value = strtr($value, array($num_format->getNegativeSign() => '', '-' => ''));
      $value = '-' . $value;
    }

    $value = str_replace($num_format->getGroupSeparator(),'', $value);
    if (strpos($value, $num_format->getDecimalSeparator()) !== false) {
      if ($num_format->getDecimalSeparator() != '.') {
        $value = str_replace($num_format->getDecimalSeparator(), ".", $value);
      }
    }

    return $value;
  }
  /**
   * Checks if the input contains a normalized or localized number
   *
   * @param   string  $input    Localized number string
   * @param   array   $options  Options: locale. See {@link setOptions()} for details.
   * @return  boolean           Returns true if a number was found
  */
  public static function isNumber($input, $culture)
  {

    $regexs = self::_getRegexForType(sfNumberFormatInfo::DECIMAL,$culture);
    foreach ($regexs as $regex) {
      preg_match($regex, $input, $found);
      if (isset($found[0])) {
        return true;
      }
    }
    return false;
  }
  /**
   * Internal method to convert cldr number syntax into regex
   *
   * @param  string $type
   * @return string
  */
  private static function _getRegexForType($type, $culture)
  {

    $num_format = sfNumberFormatInfo::getInstance($culture,$type);
    $pos_pattern = $num_format->getPattern();
    $decimal = $pos_pattern['positive'];
    $decimal  = preg_replace('/[^#0,;\.\-Ee]/', '',$decimal);
    $patterns = explode(';', $decimal);

     if (count($patterns) == 1) {
         $patterns[1] = '-' . $patterns[0];
     }

    $num_format = sfCultureInfo::getInstance($culture)->getNumberFormat();
    foreach($patterns as $pkey => $pattern) {
      $regex[$pkey]  = '/^';
      $rest   = 0;
      $end    = null;
      if (strpos($pattern, '.') !== false) {
        $end     = substr($pattern, strpos($pattern, '.') + 1);
        $pattern = substr($pattern, 0, -strlen($end) - 1);
      }

      if (strpos($pattern, ',') !== false) {
        $parts = explode(',', $pattern);
        $count = count($parts);
        foreach($parts as $key => $part) {
          switch ($part) {
            case '#':
            case '-#':
              if ($part[0] == '-') {
                $regex[$pkey] .= '[' . $num_format->getNegativeSign() . '-]{0,1}';
              } else {
                $regex[$pkey] .= '[' . $num_format->getPositiveSign() . '+]{0,1}';
              }

              if (($parts[$key + 1]) == '##0')  {
                $regex[$pkey] .= '[0-9]{1,3}';
              } else if (($parts[$key + 1]) == '##') {
                $regex[$pkey] .= '[0-9]{1,2}';
              } else {
                throw new sfException('Unsupported token for numberformat (Pos 1):"' . $pattern . '"');
              }
              break;
            case '##':
              if ($parts[$key + 1] == '##0') {
                $regex[$pkey] .=  '(\\' . $num_format->getGroupSeparator() . '{0,1}[0-9]{2})*';
              } else {
                throw new sfException('Unsupported token for numberformat (Pos 2):"' . $pattern . '"');
              }
              break;
            case '##0':
              if ($parts[$key - 1] == '##') {
                $regex[$pkey] .= '[0-9]';
              } else if (($parts[$key - 1] == '#') || ($parts[$key - 1] == '-#')) {
                $regex[$pkey] .= '(\\' . $num_format->getGroupSeparator() . '{0,1}[0-9]{3})*';
              } else {
                throw new sfException('Unsupported token for numberformat (Pos 3):"' . $pattern . '"');
              }
              break;
            case '#0':
              if ($key == 0) {
                $regex[$pkey] .= '[0-9]*';
              } else {
                throw new sfException('Unsupported token for numberformat (Pos 4):"' . $pattern . '"');
              }
              break;
          }
        }
      }

      if (strpos($pattern, 'E') !== false) {
        if (($pattern == '#E0') || ($pattern == '#E00')) {
          $regex[$pkey] .= '[' . $num_format->getPositiveSign() . '+]{0,1}[0-9]{1,}(\\' . $num_format->getDecimalSeparator() . '[0-9]{1,})*[eE][' . $num_format->getPositiveSign() . '+]{0,1}[0-9]{1,}';
        } else if (($pattern == '-#E0') || ($pattern == '-#E00')) {
          $regex[$pkey] .= '[' . $num_format->getNegativeSign() . '-]{0,1}[0-9]{1,}(\\' . $num_format->getDecimalSeparator() . '[0-9]{1,})*[eE][' . $num_format->getNegativeSign() . '-]{0,1}[0-9]{1,}';
        } else {
          throw new sfException('Unsupported token for numberformat (Pos 5):"' . $pattern . '"');
        }
      }

      if (!empty($end)) {
        if ($end == '###') {
          $regex[$pkey] .= '(\\' . $num_format->getDecimalSeparator() . '{1}[0-9]{1,}){0,1}';
        } else if ($end == '###-') {
          $regex[$pkey] .= '(\\' . $num_format->getDecimalSeparator() . '{1}[0-9]{1,}){0,1}[' . $num_format->getNegativeSign() . '-]';
        } else {
          throw new sfException('Unsupported token for numberformat (Pos 6):"' . $pattern . '"');
        }
      }

      $regex[$pkey] .= '$/';
    }

    return $regex;
  }
}
