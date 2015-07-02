<?php

class TentativeRepprovedStudent extends BaseTentativeRepprovedStudent
{
	public function __toString() {
		return $this->getStudentCareerSchoolYear()->getStudent();
	}
}