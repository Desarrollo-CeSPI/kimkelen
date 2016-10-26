<?php

class LetterMarkPeer extends BaseLetterMarkPeer
{
  public static function getLetterMarkByPk($id)
  {
    $criteria = new Criteria();
    $criteria->add(LetterMarkPeer::ID, $id);
    $result = LetterMarkPeer::doSelectOne($criteria);
    return $result; 
  }

	public static function getLetterMarkByValue($value)
  {
    $criteria = new Criteria();
  	$criteria->add(LetterMarkPeer::VALUE, $value);
  	$result = LetterMarkPeer::doSelectOne($criteria);
  	return $result;	
  }
  
}
