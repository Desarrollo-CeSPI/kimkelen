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
 * CourseSubjectDay form.
 *
 * @package    sistema de alumnos
 * @subpackage form
 * @author     Your name here
 */
class ManageCourseSubjectDayForm extends BaseCourseSubjectForm
{
  public function configure()
  {
    unset(
      $this["course_id"],
      $this["career_subject_school_year_id"]
    );
    for ($i = $this->getWeekDayFrom(); $i <= $this->getWeekDayTo(); $i++)
    {
      $this->configureDay($i);
    }

    $this->widgetSchema->setNameFormat("manage_course_subject_days[%s]");

    $horizontal_formatter = new dcWidgetHorizontalFormatter($this->getWidgetSchema());
    $this->getWidgetSchema()->addFormFormatter('horizontal', $horizontal_formatter);
    $this->getWidgetSchema()->setFormFormatterName('horizontal');

  }

  public function getWeekDayFrom()
  {
    return SchoolBehaviourFactory::getInstance()->getFirstCourseSubjectWeekday();
  }

  public function getWeekDayTo()
  {
    return SchoolBehaviourFactory::getInstance()->getLastCourseSubjectWeekday();
  }

  public function getBlocksPerCourseSubjectDay()
  {
    return SchoolBehaviourFactory::getInstance()->getBlocksPerCourseSubjectDay();
  }

  public function getCourseSubjectDayName($i)
  {
    return constant("CourseSubjectDay::DAY_NAME_$i");
  }

  public function configureDay($day)
  {
    $prefix_name = "day_$day";
    //bloques

    for ($i = 1; $i <= $this->getBlocksPerCourseSubjectDay(); $i++)
    {
      //$js_id = "course_subject_day_{$this->getObject()->getDay()}";

      $course_subject_day = CourseSubjectDayPeer::retrieveOrCreateByDayAndBlockAndCourseSubjectId($day, $i, $this->getObject()->getId());

      $block_name= $prefix_name."_block_".$i;
      $js_id ="manage_course_subject_days_day_".$day."_block_".$i;
      $name = $block_name. "_enable";
      $this->setWidget($name, new sfWidgetFormInputCheckbox());
      $this->setValidator($name, new sfValidatorBoolean());
      $this->setDefault($name, !$course_subject_day->isNew());
      $this->getWidget($name)->setLabel('Habilitar (bloque '.$i. ')');
      $this->getWidget($name)->setAttribute("onchange", "course_subject_day_form_on_click_handler('$js_id')");

      $start_name = $block_name. "_starts_at";
      $this->setWidget($start_name, new sfWidgetFormTime(
        array(
          'hours'=> SchoolBehaviourFactory::getInstance()->getHoursArrayForSubjectWeekday(),
          'minutes'=>SchoolBehaviourFactory::getInstance()->getMinutesArrayForSubjectWeekday()),
        array(
          'disable'=>$course_subject_day->isNew()
       )));
      $this->setDefault($start_name, $course_subject_day->getStartsAt());
      $this->getWidgetSchema()->setLabel($start_name, 'Start');
      $this->setValidator($start_name,  new sfValidatorTime(array('required' => false)));

      $end_name = $block_name. "_ends_at";
      $this->setWidget($end_name, new sfWidgetFormTime(
        array(
          'hours'=> SchoolBehaviourFactory::getInstance()->getHoursArrayForSubjectWeekday(),
          'minutes'=>SchoolBehaviourFactory::getInstance()->getMinutesArrayForSubjectWeekday()),
        array(
          'disable'=>$course_subject_day->isNew()
       )));
      $this->getWidgetSchema()->setLabel($end_name, 'End');
      $this->setDefault($end_name, $course_subject_day->getEndsAt());
      $this->setValidator($end_name,  new sfValidatorTime(array('required' => false)));

      $name = $block_name. "_classroom_id";
      $this->setWidget($name, new sfWidgetFormPropelChoice(array('model' => 'Classroom', 'add_empty' => true), array('disable'=>$course_subject_day->isNew())));
      $this->setValidator($name, new sfValidatorPropelChoice(array('model' => 'Classroom', 'column' => 'id', 'required' => false)));
      $this->getWidget($name)->setLabel('Classroom');
      $this->setDefault($name, $course_subject_day->getClassroomId());


      //post validators
      $this->mergePostValidator(new sfValidatorSchemaCompare(
          $start_name,
          sfValidatorSchemaCompare::LESS_THAN_EQUAL,
          $end_name,
          array(),
          array("invalid" => "La hora de comienzo debe ser menor a la de fin.")
      ));

    }
  }


  protected function doSave($con = null)
  {
    if (is_null($con))
    {
      $con = $this->getConnection();
    }

    try
    {
      $con->beginTransaction();

      $values = $this->getValues();

      $id = $values['id'];
      for ($day = $this->getWeekDayFrom(); $day <= $this->getWeekDayTo(); $day++)
      {
        $prefix_name = "day_$day";

        for ($i = 1; $i <= $this->getBlocksPerCourseSubjectDay(); $i++)
        {
          $course_subject_day = CourseSubjectDayPeer::retrieveOrCreateByDayAndBlockAndCourseSubjectId($day, $i, $id);
          $block_name = $prefix_name . "_block_" . $i;
          $name = $block_name . "_enable";
          if ($values[$name])
          {
            $course_subject_day->setStartsAt($values[$block_name . "_starts_at"]);
            $course_subject_day->setEndsAt($values[$block_name . "_ends_at"]);
            $course_subject_day->setClassroomId($values[$block_name . "_classroom_id"]);
            $course_subject_day->save($con);
          }
          else
          {
            if (!$course_subject_day->isNew())
            {
              $course_subject_day->delete($con);
            }
          }
        }
      }
      $con->commit();
    }
    catch (PropelException $e)
    {
      $con->rollback();
      throw $e;
    }
  }

  public function getJavaScripts()
  {
    return array_merge(parent::getJavaScripts(), array("course_subject_day_form.js"));
  }

}