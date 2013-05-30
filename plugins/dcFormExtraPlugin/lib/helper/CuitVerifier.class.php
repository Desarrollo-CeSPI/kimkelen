<?php

class CuitVerifier
{
  /**
   *  Check if the argument $cuit is valid.
   *  $cuit can either be in a dashed format:
   *    XX-XXXXXXXX-X (13 characters)
   *  or in a non-dashed format:
   *    XXXXXXXXXXX (11 characters).
   *
   *  @return boolean: true if $cuit is valid,
   *          otherwise false.
   */
  public static function verify($cuit)
  {
    return (self::generateDigit($cuit) == substr($cuit, strlen($cuit) - 1, 1));
  }

  /**
   *  Generate the verifier digit for $cuit and return it.
   *
   *  @return integer: the verifier digit for $cuit.
   */
  public static function generateDigit($cuit)
  {
    $multipliers = array(5, 4, 3, 2, 7, 6, 5, 4, 3, 2);
    $my_cuit = str_split(substr(self::removeDashes($cuit), 0, 10));

    $sum = 0;
    foreach ($my_cuit as $i => $digit) {
      $sum += intval($digit) * $multipliers[$i];
    }

    $mod = $sum % 11;
    $digit  = 11 - $mod;
    if ($digit == 11) {
      $digit = 0;
    } else if ($digit == 10) {
      $digit = 9;
    }

    return $digit;
  }

  public static function removeDashes($cuit)
  {
    $formatted = null;
    switch (strlen($cuit)) {
      case 11:
        //non-dashed version, do nothing
        $formatted = $cuit;
        break;
      case 13:
        //dashed version, remove dashes
        $formatted = substr($cuit, 0, 2) . substr($cuit, 3, 8) . substr($cuit, 12, 1);
        break;
      default:
        //invalid value, return null.
    }

    return $formatted;
  }

  public static function addDashes($cuit)
  {
    $formatted = null;
    switch (strlen($cuit)) {
      case 11:
        //non-dashed version, add dashes
        $formatted = substr($cuit, 0, 2) . '-' . substr($cuit, 2, 8) . '-' . substr($cuit, 10, 1);
        break;
      case 13:
        //dashed version, do nothing
        $formatted = substr($cuit, 0, 2) . '-' . substr($cuit, 3, 8) . '-' . substr($cuit, 12, 1);
        break;
      default:
        //invalid value, return null.
    }

    return $formatted;
  }
}
