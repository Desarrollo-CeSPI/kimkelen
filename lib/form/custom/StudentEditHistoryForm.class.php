<?php
/*
 * Kimkëlen - School Management Software
 * Copyright (C) 2013 CeSPI - UNLP <desarrollo@cespi.unlp.edu.ar>
 *
 * This file is part of Kimkëlen.
 *
 * Kimkëlen is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License v2.0 as published by
 * the Free Software Foundation.
 *
 * Kimkëlen is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with Kimkëlen.  If not, see <http://www.gnu.org/licenses/gpl-2.0.html>.
 */ ?>
<?php
/**
 * Description of StudentEditHistoryForm
 *
 * @author gramirez
 */
class StudentEditHistoryForm extends sfFormPropel
{
  public function configure()
  {
    sfContext::getInstance()->getConfiguration()->loadHelpers(array('I18N'));
    //Set the formatter to the form
    $sf_formatter_revisited = new sfWidgetFormSchemaFormatterRevisited($this);
    $this->getWidgetSchema()->addFormFormatter('Revisited', $sf_formatter_revisited);
    $this->getWidgetSchema()->setFormFormatterName('Revisited');

    $this->setDefault('id', $this->getObject()->getId());
    $this->setWidget('id', new sfWidgetFormInputHidden());
    $this->setValidator('id', new sfValidatorPropelChoice(array('model' => 'CourseSubjectStudent', 'required' => 'true')));

    $this->mark_fields = $this->configureMarks();
    $this->close_field = $this->configureCloseCourse();
    $this->examination_fields = $this->configureExaminationSubjects();
    $this->student_approved_career_subject = $this->configureStudentApprovedCareerSubject();

    //$this->repproved_course_subjects = $this->configureRepprovedCourseSubjects();

    $this->getWidgetSchema()->setNameFormat('course_subject_student[%s]');

    parent::configure();
  }

  public function canEditMarks()
  {
    return !$this->getObject()->isClosed();
  }

  public function configureMarks()
  {
    $fields = array();
    $i = 1;
    foreach ($this->getObject()->getSortedCourseSubjectStudentMarks() as $cssm)
    {
      $name = 'mark_' . $cssm->getId();
      $fields[] = $name;
      
      $configuration = $this->getObject()->getConfiguration();
      if ($this->canEditMarks())
      {
        if($configuration->isNumericalMark())
        {
          $this->setWidget($name, new sfWidgetFormInput());
          $this->setValidator($name, new sfValidatorInteger(array('required' => false)));
          $this->setDefault($name, $cssm->getMark());
          
          $this->getWidgetSchema()->setHelp($name, 'Mark should de 0 (zero) if you want the student to be free at this period');
        }
        else
        {
		  
          $letter_mark = LetterMarkPeer::getLetterMarkByValue((Int)$cssm->getMark());
          if(is_null($letter_mark)){
			  $letter = null;
		  }else{
			$letter = $letter_mark->getId();
		  }
          $this->setWidget($name, new sfWidgetFormPropelChoice(array('model'=> 'LetterMark', 'add_empty' => true, 'default' => $letter)));
          $this->setValidator($name, new sfValidatorPropelChoice(array('model' => 'LetterMark', 'required' => false)));
        }
      }
      else
      {
        $this->setWidget($name,  new mtWidgetFormPlain(array('object' => $cssm, 'method' => 'getMarkByConfig', 'method_args' => $configuration, 'add_hidden_input' => true)));
        $this->setValidator($name, new sfValidatorString(array('required' => false)));
        $this->setDefault($name, $cssm->getMark());
      }

      $this->getWidget($name)->setLabel(__('Mark %number%', array('%number%' => $i)));
      $i++;
    }

    return $fields;
  }

  public function configureCloseCourse()
  {

    $course_result = $this->getObject()->getCourseResult();

    if (is_null($course_result) || is_null($course_result->getId()))
    {
      return array();
    }

    $name = 'status';

    $this->setWidget($name, new mtWidgetFormPlain(array('object' => $course_result)));
    $this->setValidator($name, new sfValidatorPass());

    return array($name);
  }


  public function configureStudentApprovedCareerSubject()
  {
    $student_approved_career_subject = StudentApprovedCareerSubjectPeer::retrieveByCourseSubjectStudent($this->getObject());

    if (is_null($student_approved_career_subject))
    {
      return array();
    }

    $name = 'student_approved_career_subject';

    $this->setWidget($name, new mtWidgetFormPlain(array('object' => $student_approved_career_subject, 'method' => 'getResult')));
    $this->setValidator($name, new sfValidatorPass());

    return array($name);
  }

  public function canEditExaminationSubject()
  {
    $student_approved_career_subject = StudentApprovedCareerSubjectPeer::retrieveByCourseSubjectStudent($this->getObject());

    if (!is_null($student_approved_career_subject))
    {

      return false;
    }

    return $this->getObject()->countCourseSubjectStudentExaminations() > 0 ;
    //return $this->getObject()->countStudentRepprovedCourseSubjects() == 0 && $this->getObject()->countCourseSubjectStudentExaminations() > 0 ;
  }

  public function configureExaminationSubjects()
  {
    $i = 1;
    $fieldset = array();
    $last_examination_number = $this->getObject()->countCourseSubjectStudentExaminations();

    foreach ($this->getObject()->getCourseSubjectStudentExaminations() as $course_subject_student_examination)
    {
      $fields = array();
      $name = 'course_subject_student_examination_id_' . $course_subject_student_examination->getId() .'_mark';
      $fields[] = $name;

      if ($i < $last_examination_number ||  !$this->canEditExaminationSubject())
      {
        $this->setWidget($name,  new mtWidgetFormPlain(array('object' => $course_subject_student_examination, 'method' => 'getValueString', 'add_hidden_input' => true)));
        $this->setValidator($name, new sfValidatorPass());
      }
      else
      {
        $this->setWidget($name, new sfWidgetFormInput());
        $this->setDefault($name, $course_subject_student_examination->getMark());
        $this->setValidator($name, new sfValidatorNumber(array('required' => false)));

        $name_absence = 'course_subject_student_examination_id_' . $course_subject_student_examination->getId() .'_is_absent';
        $fields[] = $name_absence;

        $this->setWidget($name_absence, new sfWidgetFormInputCheckbox());
        $this->setValidator($name_absence, new sfValidatorBoolean(array('required' => false)));
        $this->setDefault($name_absence, $course_subject_student_examination->getIsAbsent());
        $this->getWidget($name_absence)->setLabel(__('Is absent'));

        $name_date = 'course_subject_student_examination_id_' . $course_subject_student_examination->getId() .'_date';
        $fields[] = $name_date;

        $this->setWidget($name_date, new csWidgetFormDateInput());
        $this->setValidator($name_date, new mtValidatorDateString(array('required' => false)));
        $this->setDefault($name_date, $course_subject_student_examination->getDate());
        $this->getWidget($name_date)->setLabel(__('Day'));

        $name_folio_number = 'course_subject_student_examination_id_' . $course_subject_student_examination->getId() .'_folio_number';
        $fields[] = $name_folio_number;

        $this->setWidget($name_folio_number, new sfWidgetFormInput());
        $this->setValidator($name_folio_number, new sfValidatorString(array('required' => false)));
        $this->setDefault($name_folio_number, $course_subject_student_examination->getFolioNumber());
        $this->getWidget($name_folio_number)->setLabel(__('Folio number'));

      }


      $this->getWidget($name)->setLabel(__('Mark', array('%number%' => $i)));

      $fieldset[] = array('Mesa de examen ' . $i => $fields);
      $i++;
    }

    return $fieldset;
  }

  /* Esto se hizo para las previas, pero me parece que no tiene sentido poder editarlas
  public function canEditStudentRepprovedCourseSubjects()
  {
    $student_approved_career_subject = StudentApprovedCareerSubjectPeer::retrieveByCourseSubjectStudent($this->getObject());

    $student_repproved_course_subject = $this->getObject()->getStudentRepprovedCourseSubject();

    if (is_null($student_repproved_course_subject))
    {
      return false;
    }

    $student_examination_repproved_subject = $student_repproved_course_subject->getLastStudentExaminationRepprovedSubject();

    return is_null($student_approved_career_subject) && !is_null($student_examination_repproved_subject);
  }

  public function configureRepprovedCourseSubjects()
  {
    $student_repproved_course_subject = $this->getObject()->getStudentRepprovedCourseSubject();
    $fieldset = array();

    if (!is_null($student_repproved_course_subject))
    {
      $i = 1;
      $last_student_examination_repproved_subjects = $student_repproved_course_subject->countStudentExaminationRepprovedSubjects();

      $student_examination_repproved_subjects = $student_repproved_course_subject->getStudentExaminationRepprovedSubjects();


      foreach ($student_examination_repproved_subjects as $student_examination_repproved_subject)
      {
        $fields = array();
        $name = 'student_examination_repproved_subject_' . $student_examination_repproved_subject->getId() .'_mark';
        $fields[] = $name;

        if ($i < $last_student_examination_repproved_subjects || !$this->canEditStudentRepprovedCourseSubjects())
        {
          $this->setWidget($name,  new mtWidgetFormPlain(array('object' => $student_examination_repproved_subject, 'method' => 'getValueString', 'add_hidden_input' => true)));
          $this->setValidator($name, new sfValidatorPass());
        }
        else
        {
          $this->setWidget($name, new sfWidgetFormInput());
          $this->setValidator($name, new sfValidatorNumber(array('required' => false)));
          $this->setDefault($name, $student_examination_repproved_subject->getMark());

          $name_absence = 'student_examination_repproved_subject_' . $student_examination_repproved_subject->getId() .'_is_absent';
          $fields[] = $name_absence;

          $this->setWidget($name_absence, new sfWidgetFormInputCheckbox());
          $this->setValidator($name_absence, new sfValidatorBoolean(array('required' => false)));
          $this->setDefault($name_absence, $student_examination_repproved_subject->getIsAbsent());
          $this->getWidget($name_absence)->setLabel(__('Is absence', array('%number%' => $i)));
        }

        $this->getWidget($name)->setLabel(__('Mark', array('%number%' => $i)));

        $fieldset[] = array('Examination repproved ' . $i => $fields);
        $i++;
      }
    }

    return $fieldset;
  }
  */

  public function getFormFieldsDisplay()
  {
    $fields = array(
      'Course Marks'   =>  array_merge($this->mark_fields, array('id')),
      'Result'         =>  $this->close_field
    );

    foreach ($this->examination_fields as $examination_field)
    {
      $fields = array_merge($fields, $examination_field);
    }

    $fields = array_merge($fields, array('Materia aprobada' => $this->student_approved_career_subject));

    return $fields;
  }

  public function getBackTo()
  {
    if ( $this->getObject()->countCourseSubjectStudentExaminations() )
    {
      return __('Edit previous examination');
    }
    elseif (is_null($this->getObject()->getCourseResult()) || !is_null($this->getObject()->getCourseResult()->getId()))
    {
      return __('Edit marks');
    }
    else
    {
      return '';
    }
  }

  public function getModelName()
  {
    return 'CourseSubjectStudent';
  }

  public function canEdit()
  {
    return $this->canEditMarks() || $this->canEditExaminationSubject();
  }

  public function save($con = null)
  {
    if (is_null($con))
    {
      $con = $this->getConnection();
    }

    try
    {
      $con->beginTransaction();
      $values = $this->getValues();
      $any_change = false;

      //First check the marks
      if ($this->canEditMarks())
      {
        foreach ($this->getObject()->getSortedCourseSubjectStudentMarks() as $cssm)
        {
          $mark = $values['mark_' . $cssm->getId()];

          $configuration = $this->getObject()->getConfiguration();
          if(!$configuration->isNumericalMark())
          {
            $letter_mark = LetterMarkPeer::retrieveByPk($mark);
            if(is_null($letter_mark))
            {	
				$mark= null;
			}
			else{
				$mark = $letter_mark->getValue();
			}
          }
         
          if ($cssm->getMark() !== $mark)
          {
            $cssm->setMark($mark);

            if ($mark == 0){
              $cssm->setIsFree(true);
            }
            else {
              $cssm->setIsFree(false);
            }
            $cssm->save($con);
            $any_change = true;
          }
        }


        if ($any_change && $this->getObject()->areAllMarksClosed())
        {
          //Creo de nuevo el result porque cambiaron las notas
          $course_result = SchoolBehaviourFactory::getEvaluatorInstance()->getCourseSubjectStudentResult($this->getObject(), $con);
          $course_result->save($con);

          if ($course_result instanceOf StudentApprovedCourseSubject)
          {
            $this->getObject()->setStudentApprovedCourseSubject($course_result);
            $this->getObject()->save($con);
          }

          //Si ya fue procesado el año lectivo, entonces crea si corresponde la materia aprobada o la mesa de examen
          if ($this->getObject()->getCourseSubject()->getCareerSubjectSchoolYear()->getCareerSchoolYear()->getIsProcessed() &&
            $this->getObject()->getCourseSubject()->getCourse()->getIsClosed()
          )
          {
            SchoolBehaviourFactory::getEvaluatorInstance()->closeCourseSubjectStudent($course_result, $con);
          }
        }
      }

      //if para la edición de notas en mesas de examen
      if ($this->canEditExaminationSubject())
      {
        $course_subject_student_examination = $this->getObject()->getLastCourseSubjectStudentExamination();

        if ((isset($values['course_subject_student_examination_id_' . $course_subject_student_examination->getId(). '_mark'])) || (isset($values['course_subject_student_examination_id_' . $course_subject_student_examination->getId(). '_is_absent'])))
        {

          $mark = $values['course_subject_student_examination_id_' . $course_subject_student_examination->getId() . '_mark'];
          $is_absent = $values['course_subject_student_examination_id_' . $course_subject_student_examination->getId() . '_is_absent'];
          $date = $values['course_subject_student_examination_id_' . $course_subject_student_examination->getId() . '_date'];
          $folio = $values['course_subject_student_examination_id_' . $course_subject_student_examination->getId() . '_folio_number'];


          if ($mark !== $course_subject_student_examination->getMark() || $is_absent !== $course_subject_student_examination->getIsAbsent())
          {
            $course_subject_student_examination->setExaminationSubject(ExaminationSubjectPeer::retrieveByCourseSubjectStudentExamination($course_subject_student_examination));
            $course_subject_student_examination->setMark($mark);
            $course_subject_student_examination->setIsAbsent($is_absent);
            $course_subject_student_examination->setDate($date);
            $course_subject_student_examination->setFolioNumber($folio);
            $course_subject_student_examination->save($con);

            SchoolBehaviourFactory::getEvaluatorInstance()->closeCourseSubjectStudentExamination($course_subject_student_examination, $con);
          }
        }
      }

      /* Esto se habia creado para las previas, pero no tiene sentido editarlas. Lo dejo comentado por si sirve para mas adelante.
      if ($this->canEditStudentRepprovedCourseSubjects())
      {

        $student_repproved_course_subject = $this->getObject()->getStudentRepprovedCourseSubject();

        $student_examination_repproved_subject = $student_repproved_course_subject->getLastStudentExaminationRepprovedSubject();

        $mark = $values['student_examination_repproved_subject_' . $student_examination_repproved_subject->getId() .'_mark'];
        $is_absence_name = 'student_examination_repproved_subject_' . $student_examination_repproved_subject->getId() .'_is_absent';
        $is_absence = isset($values[$is_absence_name]) ? $values[$is_absence_name] : null;

        if ($mark !== $student_examination_repproved_subject->getMark() || $is_absence !== $student_examination_repproved_subject->getIsAbsent())
        {
          $student_examination_repproved_subject->setMark($mark);
          $student_examination_repproved_subject->setIsAbsence($is_absence);
        }
      }
      */

      $con->commit();
    }
    catch (PropelException $e)
    {
      $con->rollBack();
      throw $e;
    }
  }
}
