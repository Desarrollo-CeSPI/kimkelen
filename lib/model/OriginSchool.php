<?php

class OriginSchool extends BaseOriginSchool
{
	public function __toString() {
		return sprintf("%s (%s)", $this->getName(), $this->getAddress());
	}
}