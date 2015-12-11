<?php

class LetterMarkPeer extends BaseLetterMarkPeer
{
	public static function getPkByValue ($value)
	{
		if((0 < $value)&&($value < 7))
  		{
  			$value = 4;
  		}

		if ($value != 0)
		{
			$criteria = new Criteria();
			$criteria->add(LetterMarkPeer::VALUE, $value); 
			$result = LetterMarkPeer::doSelectOne($criteria);
			return $result->getId();
		}
    else
    {
      return "Libre";
    }
	}

	public static function getOption($value)
  {
  	$value = round($value);

  	if((0 < $value)&&($value < 7))
  	{
  		$value = 4;
  	}

  	if($value != 0)
  	{ 
  		$criteria = new Criteria();
  		$criteria->add(LetterMarkPeer::VALUE, $value);
  		$result = LetterMarkPeer::doSelectOne($criteria);
  		return $result->getLetter();
  	}
    else
    {
      return "Libre";
    }
  }
  
}
