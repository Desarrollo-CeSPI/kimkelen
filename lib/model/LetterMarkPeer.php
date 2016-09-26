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
    // esto es para los casos ya cargados cambiados a letras desp, que quedan con notas numerica no mapeadas.
  	if((0 < $value)&&($value < 7))
  	{
  		$value = 4;
  	}
  	
    $criteria = new Criteria();
  	$criteria->add(LetterMarkPeer::VALUE, $value);
  	$result = LetterMarkPeer::doSelectOne($criteria);
  	return $result;	
  }
  
}
