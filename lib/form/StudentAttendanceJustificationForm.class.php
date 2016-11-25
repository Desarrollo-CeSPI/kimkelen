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
 * StudentAttendanceJustification form.
 *
 * @package    sistema de alumnos
 * @subpackage form
 * @author     Your name here
 */
class StudentAttendanceJustificationForm extends BaseStudentAttendanceJustificationForm
{
  public function configure()
  {
    $sf_formatter_revisited = new sfWidgetFormSchemaFormatterRevisited($this);
    $this->getWidgetSchema()->addFormFormatter("Revisited", $sf_formatter_revisited);
    $this->getWidgetSchema()->setFormFormatterName("Revisited");

    $this->widgetSchema->setNameFormat('student_attendance_justification[%s]');
    $this->validatorSchema->setOption("allow_extra_fields", true);

    $this->getWidget('justification_type_id')->setOption('add_empty', true);

    if($this->getObject()->getDocument())
    {
      $this->setWidget('current_document', new mtWidgetFormPartial(array('module' => 'attendance_justification', 'partial' => 'downloable_document', 'form' => $this)));
      $this->setValidator('current_document', new sfValidatorPass(array('required' => false)));      
    }

    $this->setWidget('document', new sfWidgetFormInputFile());
    $this->setValidator('document', new sfValidatorFile(array(
                                                        'path' => StudentAttendanceJustification::getDocumentDirectory(),
                                                        'max_size' => '2097152',                                                        
                                                        'required' => false)));
    
    $this->getWidgetSchema()->setHelp('document', 'The file must be of the following types: jpeg, jpg, gif, png, pdf.');
  }

  public function setStudentAttendances($student_attendance_ids)
  {    
    foreach ($student_attendance_ids as $student_attendance_id)
    {
      $student_attendance = StudentAttendancePeer::retrieveByPK($student_attendance_id);
      $this->setWidget("student_attendance_". $student_attendance_id.")", new mtWidgetFormPlain(array(
        "object" => $student_attendance,
        "add_hidden_input" => true,
        "use_retrieved_value" => false
      )));

      $this->setValidator("student_attendance_" . $student_attendance_id.")", new sfValidatorPropelChoice(array(
        "model" => "StudentAttendance",
        "required" => true
      )));

      $this->setDefault("student_attendance_".$student_attendance_id.")", $student_attendance_id);
      $this->widgetSchema->setLabel("student_attendance_".$student_attendance_id.")", "Absence");

      $this->widgetSchema->moveField("student_attendance_". $student_attendance_id . ")", "before", "justification_type_id");
    }
  }

  public function save($con = null)
  {
    $con = is_null($con) ? Propel::getConnection() : $con;
    parent::save($con);


    $values = $this->getValues();
    
    $justification_type_id = $values['justification_type_id'];
    $observation = $values['observation'];
    $document = $values['document'];    
    unset($values['justification_type_id'], $values['observation'], $values['document'], $values['id']);

    
    $con->beginTransaction();
    try
    {
      foreach($values as $value)
      {
        $student_attendance = StudentAttendancePeer::retrieveByPK($value);
        $student_attendance->setStudentAttendanceJustification($this->getObject());
        #$student_attendance->updateAbsence($con); no va mas
        $student_attendance->save($con);
      }
      $con->commit();
    }
    catch (PropelException $e)
    {
      throw $e;
      $con->rollBack();
    }
  }
}
