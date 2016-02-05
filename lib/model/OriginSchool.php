<?php

class OriginSchool extends BaseOriginSchool
{
	public function __toString() {
		return $this->getName();
	}
}