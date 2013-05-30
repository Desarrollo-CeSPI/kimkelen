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

class CourseSubjectDay extends BaseCourseSubjectDay
{
  const DAY_NAME_1="Monday";
  const DAY_NAME_2="Tuesday";
  const DAY_NAME_3="Wednesday";
  const DAY_NAME_4="Thursday";
  const DAY_NAME_5="Friday";
  const DAY_NAME_6="Saturday";
  const DAY_NAME_7="Sunday";

  public static function geti18Names() {
    sfContext::getInstance()->getConfiguration()->loadHelpers('I18N');
    $names = array();
    for ($i=1; $i<=7; $i++)
    {
      $names[$i]=__(constant("CourseSubjectDay::DAY_NAME_".$i));
    }
    return $names;
  }

  public function __toString() {
    return sprintf("%s (%s %s)",
            $this->getDayName(),
            is_null($this->getStartsAt())?'':$this->getTimeRangeString(),
            $this->getClassroom()
            );
  }

  public function getTimeRangeString()
  {
        return $this->getStartsAt("H:i")." - ".$this->getEndsAt("H:i");
  }
  public function getDayName()
  {
    return constant("self::DAY_NAME_".$this->getDay());
  }


  protected function getEventTitle()
  {
    return $this->getCourseSubject()->getCourse()->getName().' ('.$this->getClassroom().') - '.$this->getTeachers();
  }
  /**
   * Creates a stdClass object with required properties for jquery-weekday json
   * events:
   *   - Id
   *   - Title
   *   - start date time in ISO 8601 format
   *   - end date time in ISO 8601 format
   *
   * @return stdClass
   */
  public function getWeekDay()
  {
    $obj = new stdClass();
    $obj->id = $this->getId();
    $obj->title = $this->getEventTitle();
    $start = new DateTime();
    $start->setTime($this->getStartsAt('H'),$this->getStartsAt('i'));
    $end = new DateTime();
    $end->setTime($this->getEndsAt('H'),$this->getEndsAt('i'));

    if ( $start->format('N') > $this->getDay() )
    {
      $start->modify(sprintf("-%d days",$start->format('N') - $this->getDay()));
      $end->modify(sprintf("-%d days",$end->format('N') - $this->getDay()));
    }
    elseif( $start->format('N') < $this->getDay())
    {
      $start->modify(sprintf("+%d days",$this->getDay() - $end->format('N')));
      $end->modify(sprintf("+%d days",$this->getDay() - $end->format('N')));

    }
    $obj->start = $start->format('c');
    $obj->end = $end->format('c');
    return $obj;
  }

  public function getTeachers()
  {
    $teachers = '';
    foreach($this->getCourseSubject()->getCourseSubjectTeachers() as $course_subject_teacher){
      $teachers .=$course_subject_teacher->getTeacher().' ';

    }
    return $teachers;
  }
}