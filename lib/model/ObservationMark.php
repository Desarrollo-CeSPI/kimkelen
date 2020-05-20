<?php

class ObservationMark extends BaseObservationMark
{
  public function __toString ()
	{
		return $this->getLetter();
	}
}
