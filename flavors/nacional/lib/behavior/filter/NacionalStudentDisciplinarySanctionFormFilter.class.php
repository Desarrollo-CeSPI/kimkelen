<?php

/**
 * StudentDisciplinarySanction filter form.
 *
 * @package    sistema de alumnos
 * @subpackage filter
 * @author     Your name here
 */
class NacionalStudentDisciplinarySanctionFormFilter extends StudentDisciplinarySanctionFormFilter
{
	protected function unsetFilters()
	{
		unset($this['id'],$this['value'],$this['observation'],$this['document'],$this['applicant_other'],$this['name'],$this['disciplinary_sanction_type_id']);
	}
}
