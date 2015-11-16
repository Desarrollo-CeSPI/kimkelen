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
 * SubjectConfiguration form.
 *
 * @package    conservatorio
 * @subpackage form
 * @author     Your name here
 * @version    SVN: $Id: sfPropelFormTemplate.php 10377 2008-07-21 07:10:32Z dwhittle $
 */
class SubjectConfigurationForm extends BaseSubjectConfigurationForm
{
  public function configure()
  {
    $sf_formatter_revisited = new sfWidgetFormSchemaFormatterRevisited($this);
    $this->getWidgetSchema()->addFormFormatter('Revisited', $sf_formatter_revisited);
    $this->getWidgetSchema()->setFormFormatterName('Revisited');

    unset(
      $this['created_at'],
      $this['evaluation_method'],
      $this['course_required'],
      $this['final_examination_required'],
      $this['course_type']
    );

    //Widget schema
    $this->getWidgetSchema()->setLabels(array(
      'course_marks' => 'Cantidad de Notas',
      'final_examination_required' => 'Examen final requerido',
      'course_required' => 'Cursada requerida',
      'course_minimun_mark'=>'Nota mínima de aprobacion',
      'course_examination_count' => 'Numero de mesas',
      'max_disciplinary_sanctions' => 'Cantidad máxima de sanciones',
    ));

    $choices = SchoolBehaviourFactory::getInstance()->getAttendanceTypeChoices();
    $this->setWidget('attendance_type', new sfWidgetFormChoice(array('choices' => $choices, 'expanded' => true)));
    $this->setValidator('attendance_type', new sfValidatorChoice(array('choices' => array_keys($choices))));

    $course_type_choices = SchoolBehaviourFactory::getInstance()->getCourseTypeChoices();
    $this->setWidget('course_type', new sfWidgetFormChoice(array('choices' => $course_type_choices)));
    $this->setValidator('course_type', new sfValidatorChoice(array('choices' => array_keys($course_type_choices), 'required' => true)));

    $choice = Array('1' => 'Numerica', '0' => 'Con letras'); //SchoolBehaviourFactory::getInstance()->getAttendanceTypeChoices();
    $this->setWidget('numerical_mark', new sfWidgetFormChoice(array('choices' => $choice)));
    $this->setValidator('numerical_mark', new sfValidatorChoice(array('choices' => array_keys($choice), 'required' => true)));
    $this->widgetSchema->setLabel('numerical_mark', 'Tipo de Nota');
//    $this->setDefault('course_type',SchoolBehaviourFactory::getInstance()->getDefaultCourseType());

    $this->setWidget('when_disapprove_show_string', new sfWidgetFormChoice(array('choices' => array(0=>'Muestra texto', 1=>'Muestra numero'), 'multiple'=>false,'expanded'=>true)));
    $this->getWidget('when_disapprove_show_string')->setDefault($this->getObject()->getWhenDisapproveShowString());
    $this->setValidator('when_disapprove_show_string', new sfValidatorChoice(array('required' => true,'choices' => array(0, 1))));

    $this->setWidget('necessary_student_approved_career_subject_to_show_prom_def', new sfWidgetFormChoice(array('choices' => array(0=>'Muestra', 1=>'No muestra'), 'multiple'=>false,'expanded'=>true)));
    $this->getWidget('necessary_student_approved_career_subject_to_show_prom_def')->setDefault($this->getObject()->getNecessaryStudentApprovedCareerSubjectToShowPromDef());

    $this->setValidator('necessary_student_approved_career_subject_to_show_prom_def', new sfValidatorChoice(array('required' => true,'choices' => array(0, 1))));

    $this->widgetSchema->setHelp('attendance_type','Se define el tipo de asistencia que tendran las materias.');
    $this->widgetSchema->setHelp('course_marks','Cantidad de notas de un alumno dentro de la cursada.');
    $this->widgetSchema->setHelp('final_examination_required','Indica si un alumno necesita un examen final luego de aprobar la cursada para tener aprobada la materia.');
    $this->widgetSchema->setHelp('course_required','Indica si la cursada es requerida o en caso negativo se puede rendir el final sin tener la cursada previamente aprobada.');
    $this->widgetSchema->setHelp('course_minimun_mark','Nota minima de aprobacion del curso.');
    $this->widgetSchema->setHelp('course_examination_count','Cantidad de mesas para que un alumno pueda aprobar la cursada.');
    $this->widgetSchema->setHelp('max_previous', 'Superado este número, el alumno debe repetir el año.');
    $this->widgetSchema->setHelp('max_disciplinary_sanctions', 'Superado este número, el alumno debe quedar libre.');
    $this->widgetSchema->setHelp('numerical_mark', 'Indica si las notas seran asignadas con numeros o letras.');

    $max_course_minimun_mark = SubjectConfigurationPeer::getMaxCourseMinimunMark();
    $min_course_minimun_mark = SubjectConfigurationPeer::getMinCourseMinuminMark();

    $this->validatorSchema["course_marks"]->setOption("min", 1);
    $this->validatorSchema["course_marks"]->setOption("max", 10);
    $this->validatorSchema["course_marks"]->setMessage("min", "La cantidad de calificaciones debe ser mayor que %min%");
    $this->validatorSchema["course_marks"]->setMessage("max", "La cantidad de calificaciones debe ser menor que %max%");

    $this->validatorSchema["course_minimun_mark"]->setOption("min", $min_course_minimun_mark);
    $this->validatorSchema["course_minimun_mark"]->setOption("max", $max_course_minimun_mark);
    $this->validatorSchema["course_minimun_mark"]->setMessage("min", "La nota mínima de aprobación de cursada debe ser mayor que %min%");
    $this->validatorSchema["course_minimun_mark"]->setMessage("max", "La nota maxima de aprobación de cursada debe ser menor que %max%");

    $this->configureCareerYearConfigurations();

     $this->validatorSchema->setPostValidator(
      new sfValidatorCallback(array('callback' => array($this, 'checkYearConfiguration')))
    );
  }

  public function checkYearConfiguration($validator, $values)
  {

    $errors = array();
    foreach ($this->getObject()->getCareerYearConfigurationsOrCreate() as $career_year_configuration)
    {
      $name = 'career_year_configuration_id_' . $career_year_configuration->getId() . '_has_max_absence_by_period';
      $name_max_absence = 'career_year_configuration_id_' . $career_year_configuration->getId() . '_max_absence';

      if ($values[$name] == 0 && $values[$name_max_absence] == '')
      {
        $errors = array_merge($errors, array($name_max_absence => new sfValidatorError($validator, 'Required.')));
      }
    }

    if (!empty($errors))
    {
      throw new sfValidatorErrorSchema($validator, $errors);
    }

    return $values;
  }

  public function configureCareerYearConfigurations()
  {
    $course_type_choices = array('' => '') + SchoolBehaviourFactory::getInstance()->getCourseTypeChoices();
    $choices = array(
      0 => 'Límite por año',
      1 => 'Límite por período'
    );

    foreach ($this->getObject()->getCareerYearConfigurationsOrCreate() as $career_year_configuration)
    {
      $name = 'career_year_configuration_id_' . $career_year_configuration->getId();
      $this->setWidget($name , new sfWidgetFormChoice(array('choices' => $course_type_choices)));
      $this->setValidator($name , new sfValidatorChoice(array('choices' => array_keys($course_type_choices), 'required' => true)));
      $this->setDefault($name, $career_year_configuration->getCourseType());
      $this->getWidgetSchema()->setLabel($name, 'Régimen de cursada del año ' . $career_year_configuration->getYear());

      $name = 'career_year_configuration_id_' . $career_year_configuration->getId() . '_has_max_absence_by_period';
      $this->setWidget($name, new sfWidgetFormChoice(array('choices' => $choices, 'multiple'=>false, 'expanded'=>true)));
      $this->setValidator($name, new sfValidatorChoice(array('required' => true,'choices' => array_keys($choices))));
      $this->getWidget($name)->setDefault($career_year_configuration->getHasMaxAbsenceByPeriod());
      $this->getWidgetSchema()->setLabel($name, 'Posee limite de asistencia por periodo ( Año ' . $career_year_configuration->getYear() . ' )');
      $this->getWidgetSchema()->setHelp($name, 'El alumno contabilizará las faltas por período o por el ciclo completo.');

      $name_max_absence = 'career_year_configuration_id_' . $career_year_configuration->getId() . '_max_absence';
      $this->getWidget($name)->setAttribute('onChange',"updateMaxAbsenceWidget('$name_max_absence' , '$name' )");


      $this->setWidget($name_max_absence, new sfWidgetFormInput());
      $this->setValidator($name_max_absence, new sfValidatorNumber(array('required' => false)));
      $this->getWidget($name_max_absence)->setDefault($career_year_configuration->getMaxAbsences());
      $this->getWidgetSchema()->setLabel($name_max_absence, 'Limite de asistencia por periodo ' . $career_year_configuration->getYear());
      $this->getWidgetSchema()->setHelp($name_max_absence, 'En caso de que el se contabilizén las faltas por año, se indica la cantidad maxima de assitencias permitidas. Caso contrarío, se especifica en el período o en la materia de acuerdo a la configuración establecida.');
    }
  }

  public function doSave($con = null)
  {
    parent::doSave($con);

    foreach ($this->getObject()->getCareerYearConfigurationsOrCreate() as $career_year_configuration)
    {
      $name = 'career_year_configuration_id_' . $career_year_configuration->getId();
      $career_year_configuration->setCourseType($this->getValue($name));

      $name = 'career_year_configuration_id_' . $career_year_configuration->getId() . '_has_max_absence_by_period';
      $career_year_configuration->setHasMaxAbsenceByPeriod($this->getValue($name));

      $name = 'career_year_configuration_id_' . $career_year_configuration->getId() . '_max_absence';
      $career_year_configuration->setMaxAbsences($this->getValue($name));

      $career_year_configuration->save();
    }
  }

  public function getJavascripts()
  {
    return array_merge(parent::getJavascripts(), array('subject_configuration.js'));
  }
}