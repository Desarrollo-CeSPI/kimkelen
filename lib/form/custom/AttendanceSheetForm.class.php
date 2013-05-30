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
 * AttendanceSheetForm
 *
 * @author Corrons María Emilia <ecorrons@cespi.unlp.edu.ar>
 */
class AttendanceSheetForm extends sfForm
{
  public function configure()
  {
    sfContext::getInstance()->getConfiguration()->loadHelpers(array('Tag', 'Asset'));
    $this->configureWidgets();
    $this->configureValidators();

    $this->getWidgetSchema()->setNameFormat('attendance_sheet[%s]');

     $this->mergePostValidator(new sfValidatorCallback(array(
          'callback' => array($this, 'validateDateRange')
        )));
  }

  public function configureWidgets()
  {
    $this->setWidget('division_or_course_id', new sfWidgetFormInputHidden());
    $from_date = new csWidgetFormDateInput(array('change_year' => false, 'change_month' => true));
    $to_date = new csWidgetFormDateInput(array('change_year' => false, 'change_month' => true));
    $this->setWidget('date_range', new sfWidgetFormDateRange(array('from_date' => $from_date, 'to_date' => $to_date, 'template' => "Desde el %from_date%<br/> Hasta el %to_date%")));

  }

  public function configureValidators()
  {
    $this->setValidator('division_or_course_id', new sfValidatorInteger());
    $from_date = new mtValidatorDateString();
    $to_date = new mtValidatorDateString(array('required' => true));

    $this->setValidator('date_range', new sfValidatorDateRange(array('from_date' => $from_date, 'to_date' => $to_date)));
  }


/*
   * Validates if date range chosen is inside a school year period
   */
  public function validateDateRange(sfValidatorBase $validator, $values, $arguments = array())
  {
    $from_date = strtotime($values['date_range']['from']);
    $to_date = strtotime($values['date_range']['to']);

    $periods = CareerSchoolYearPeriodPeer::retrieveCurrents();
    $dates = array();
    foreach ($periods as $p)
    {
      $dates[] = $p->getStartAt();
      $dates[] = $p->getEndAt();
    }
    sort($dates);
    $last_date = array_pop($dates);
    $first_date = array_shift($dates);
    $first_date = strtotime($first_date);
    $last_date = strtotime($last_date);

    if ($from_date < $first_date || $to_date > $last_date)
    {
      throw new sfValidatorErrorSchema($validator, array(
        new sfValidatorError($validator, 'Range chosen is not valid')));
    }

    return $values;
  }

}