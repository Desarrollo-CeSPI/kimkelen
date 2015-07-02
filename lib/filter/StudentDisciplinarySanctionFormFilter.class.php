<?php

/**
 * StudentDisciplinarySanction filter form.
 *
 * @package    sistema de alumnos
 * @subpackage filter
 * @author     Your name here
 */
class StudentDisciplinarySanctionFormFilter extends BaseStudentDisciplinarySanctionFormFilter
{
  public function configure()
  {
		$this->unsetFilters();

		$this->setWidget('request_date', new csWidgetFormDateInput());
		$this->setWidget('resolution_date', new csWidgetFormDateInput());
  }

	protected  function unsetFilters()
	{
		unset($this['id'],$this['value'],$this['observation'],$this['document'],$this['applicant_other'],$this['name']);
	}
}
