<?php

class dcFormExtraArrayToolkit
{
  public static function arrayToString($array, $between_separator, $end_separator)
  {
    $string = "";
    $i=0;
    foreach ($array as $key => $value)
    {
      if ($i>0) $string.= $end_separator;
      $string.=$key.$between_separator.$value;
      $i++;
    }
    return $string;
  }

  public static function arrayToArrayedString($name, $array)
  {
    $string = "";
    $i=0;
    if ((!is_null($array)) && is_array($array) && (count($array) > 0))
    {
      foreach ($array as $key => $value)
      {
        if (is_object($value)) return "la=";
        if ($i>0) $string.= "&";
        $string.=$name."[".$key."]=".$value;
        $i++;
      }
    }
    else
    {
      $string = "empty=";
    }
    return $string;
  }

  public static function arrayKeysToString($array, $separator)
  {
    $string="";
    $i=0;
    foreach ($array as $val)
    {
      if ($i>0) $string.="_";
      $string.=$val;
      $i++;
    }
    return $string;
  }
}
