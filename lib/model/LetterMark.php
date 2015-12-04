<?php

class LetterMark extends BaseLetterMark
{
	public function __toString ()
	{
		return $this->getLetter();
	}

	public static function getPkByValue ($value)
	{
		if ($value != 0)
		{
			$criteria = new Criteria();
			$criteria->add(LetterMarkPeer::VALUE, $value); 
			$result = LetterMarkPeer::doSelectOne($criteria);
			return $result->getId();
		}
	}
}
