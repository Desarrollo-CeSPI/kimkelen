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
	static $_students=array();

	public static function setAvailableStudents()
	{
		$c = new Criteria();
		$c->add(TentativeRepprovedStudentPeer::IS_DELETED, false);
		self::$_students = TentativeRepprovedStudentPeer::doSelect($c);
	}

	public static function setAvailableStudentsIds()
	{
		$ret = array();

		foreach (self::$_students as $st)
		{
			$ret[]=$st->getStudentCareerSchoolYear()->getStudentId();
		}

		$criteria = new Criteria();
		$criteria->addJoin(StudentCareerSchoolYearPeer::ID, TentativeRepprovedStudentPeer::STUDENT_CAREER_SCHOOL_YEAR_ID);
		$criteria->addJoin(StudentCareerSchoolYearPeer::STUDENT_ID, StudentPeer::ID);
		$criteria->add(StudentPeer::ID, $ret, Criteria::IN);
		$criteria->addJoin(StudentPeer::PERSON_ID, PersonPeer::ID);
		$criteria->add(PersonPeer::IS_ACTIVE, true);

		return $criteria;
	}


	public function configure()
	{
		sfContext::getInstance()->getConfiguration()->loadHelpers(array('I18N', 'Url'));

		self::setAvailableStudents();

		$sf_formatter_revisited = new sfWidgetFormSchemaFormatterRevisited($this);
		$this->getWidgetSchema()->addFormFormatter("Revisited", $sf_formatter_revisited);
		$this->getWidgetSchema()->setFormFormatterName("Revisited");
		$this->getWidgetSchema()->setNameFormat('tentative_repproved_students[%s]');

		$this->configureWidgets();
		$this->configureValidators();
	}

	public function configureWidgets()
	{
		$this->setWidget('students', new csWidgetFormStudentMany(array('criteria' => self::setAvailableStudentsIds())));

		$this->getWidgetSchema()->setLabel("students", __("Students that will go to pathway plan"));
	}

	public function configureValidators()
	{

		$this->setValidator("students", new sfValidatorPropelChoice(array(
			"model" => "Student",
			"multiple" => true,
			'required' => true
		)));

		$this->validatorSchema->setPostValidator(new sfValidatorCallback(array("callback" => array($this, "validatePathway"))));
	}

	public function save($con = null)
	{
		if (!isset($this->widgetSchema['students']))
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

					$trs = TentativeRepprovedStudentPeer::doSelectOne($c);

					$pathway_student = new PathwayStudent();
					$pathway_student->setStudentId($value);
					$pathway_student->setPathway(PathwayPeer::retrieveCurrent());
					$pathway_student->setYear($trs->getStudentCareerSchoolYear()->getYear());
					$pathway_student->save($con);

					$trs->setIsDeleted(true);
					$trs->save($con);

					$trs->getStudentCareerSchoolYear()->setStatus(StudentCareerSchoolYearStatus::APPROVED);
					$trs->getStudentCareerSchoolYear()->save($con);
				}

				$c = new Criteria();
				$c->add(TentativeRepprovedStudentPeer::IS_DELETED, false);

				foreach (TentativeRepprovedStudentPeer::doSelect($c) as $trs) {
					$behavior = SchoolBehaviourFactory::getEvaluatorInstance();
					$behavior->repproveStudent($trs->getStudentCareerSchoolYear()->getStudent(), $trs->getStudentCareerSchoolYear());
					$trs->setIsDeleted(true);
					$trs->save($con);
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
			throw new sfValidatorError($validator, "No se puede guardar el formulario si no existe una trayectoria.");
		}

		return $values;
	}

}