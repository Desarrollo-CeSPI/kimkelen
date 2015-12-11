<?php

class LetterMark extends BaseLetterMark
{
	public function __toString ()
	{
		return $this->getLetter();
	}

}
