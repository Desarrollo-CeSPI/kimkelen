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
		self::$_students = TentativeRepprovedStudentPeer::doSelect(new Criteria());
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
		$criteria->add(StudentPeer::ID,$ret,Criteria::IN);
		$criteria->addJoin(StudentPeer::PERSON_ID, PersonPeer::ID);
		$criteria->add(PersonPeer::IS_ACTIVE, true);

		return $criteria;
	}


	public function configure()
	{
		self::setAvailableStudents();

		sfContext::getInstance()->getConfiguration()->loadHelpers(array('Url'));
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

		$this->getWidgetSchema()->setLabel("students", "Alumnos que repetirán el año");
	}

	public function configureValidators()
	{

		$this->setValidator("students", new sfValidatorPropelChoice(array(
			"model" => "Student",
			"multiple" => true,
			'required' => true
		)));
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
					$c->add(StudentCareerSchoolYearPeer::STUDENT_ID, $value);

					$trs = TentativeRepprovedStudentPeer::doSelectOne($c);

					$pathway_student = new PathwayStudent();
					$pathway_student->setStudentId($value);
					$pathway_student->setPathway(PathwayPeer::retrieveCurrent());
					$pathway_student->setYear($trs->getStudentCareerSchoolYear()->getYear());
					$pathway_student->save($con);

					$trs->delete($con);
				}

				foreach (TentativeRepprovedStudentPeer::doSelect(new Criteria()) as $trs) {
					$behavior = SchoolBehaviourFactory::getEvaluatorInstance();
					$behavior->repproveStudent($trs->getStudentCareerSchoolYear()->getStudent(), $trs->getStudentCareerSchoolYear());
					$trs->delete($con);
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
}