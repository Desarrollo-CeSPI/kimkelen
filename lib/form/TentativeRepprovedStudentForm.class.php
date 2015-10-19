<?php

/**
 * TentativeRepprovedStudent form.
 *
 * @package    symfony
 * @subpackage form
 * @author     Your name here
 */
class TentativeRepprovedStudentForm extends sfForm
{

	public function configure()
	{

		sfContext::getInstance()->getConfiguration()->loadHelpers(array('I18N', 'Url'));



		$this->setWidget('students', new sfWidgetFormPropelChoice(array('model' => 'TentativeRepprovedStudent', 'peer_method' => 'getStudents', 'multiple'  => true,
			"renderer_class"  => "csWidgetFormSelectDoubleList"
		)));




		//$this->getWidgetSchema()->setDefault('pathway_student_list', array_map(create_function('$st', 'return $st;'), PathwayStudentPeer::getStudentsForSchoolYear(SchoolYearPeer::retrieveCurrent())));


		$this->validatorSchema['pathway_student_list'] = new sfValidatorPass();
		$this->validatorSchema->setOption('allow_extra_fields', true);

		$this->getWidgetSchema()->setNameFormat('tentative_repproved_students[%s]');

		$this->validatorSchema->setPostValidator(new sfValidatorCallback(array("callback" => array($this, "validatePathway"))));
	}


	public function updateDefaultsFromObject()
	{
		parent::updateDefaultsFromObject();

		if (isset($this->widgetSchema['pathway_student_list']))
		{
			$values = array();
			foreach ($this->object->getStudents() as $obj)
			{
				$values[$obj->getId()] = $obj->getId();
			}

			$this->setDefault('pathway_student_list', $values);
		}

	}



	public function save($con = null)
	{
		if (!isset($this->widgetSchema['pathway_student_list']))
		{
			// somebody has unset this widget
			return;
		}

		if (is_null($con))
		{
			$con = $this->getConnection();
		}
		$con->beginTransaction();
		try
		{
			$values = $this->getValue('students');

			if (is_array($values))
			{
				foreach ($values as $value)
				{

					$c = new Criteria();

					$c->addJoin(TentativeRepprovedStudentPeer::STUDENT_CAREER_SCHOOL_YEAR_ID, StudentCareerSchoolYearPeer::ID);
					$c->add(TentativeRepprovedStudentPeer::IS_DELETED, false);
					$c->add(StudentCareerSchoolYearPeer::STUDENT_ID, $value);

					$trs = TentativeRepprovedStudentPeer::doSelectOne($value);

					$pathway_student = new PathwayStudent();
					$pathway_student->setStudentId($trs->getStudentCareerSchoolYear()->getStudentId());
					$pathway_student->setPathway(PathwayPeer::retrieveCurrent());
					$pathway_student->setYear($trs->getStudentCareerSchoolYear()->getYear());
					$pathway_student->save($con);

					$trs->setIsDeleted(true);
					$trs->save($con);

					$trs->getStudentCareerSchoolYear()->setStatus(StudentCareerSchoolYearStatus::APPROVED);
					$trs->getStudentCareerSchoolYear()->save($con);
				}



			}
			$con->commit();
		}
		catch (Exception $e)
		{
			$con->rollBack();
			throw $e;
		}
	}

	public function validatePathway($validator, $values)
	{
		$criteria = new Criteria();
		$criteria->add(PathwayPeer::SCHOOL_YEAR_ID, SchoolYearPeer::retrieveCurrent()->getId());
		if (PathwayPeer::doCount($criteria) == 0)
		{
			throw new sfValidatorError($validator, "No se puede guardar el formulario si no existe una trayectoria para el año lectivo actual.");
		}

		return $values;
	}

}