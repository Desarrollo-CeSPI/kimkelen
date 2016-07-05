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
 */?>
<?php

/**
 * MatriculateGraduatedStudentsForm
 *
 * @author María Emilia Corrons <ecorrons@cespi.unlp.edu.ar>
 */
class MatriculateGraduatedStudentsForm extends sfForm
{
  public function configure() {
    sfContext::getInstance()->getConfiguration()->loadHelpers(array('Url'));

    $sf_formatter_revisited = new sfWidgetFormSchemaFormatterRevisited($this);
    $this->getWidgetSchema()->addFormFormatter("Revisited", $sf_formatter_revisited);
    $this->getWidgetSchema()->setFormFormatterName("Revisited");
    $this->widgetSchema->setNameFormat('matriculate_graduated[%s]');
    $this->validatorSchema->setOption("allow_extra_fields", true);

    $c = new Criteria();
    $last_year_school_year = SchoolYearPeer::retrieveLastYearSchoolYear(SchoolYearPeer::retrieveCurrent());
    $c->add(CareerSchoolYearPeer::SCHOOL_YEAR_ID, $last_year_school_year->getId());
    $c->add(CareerSchoolYearPeer::ID, $this->getOption('id'), Criteria::NOT_EQUAL);

    $this->setWidget('career_school_year_id', new sfWidgetFormPropelChoice(array('model' => 'CareerSchoolYear', 'add_empty' => true, 'criteria' => $c)));
    $this->setValidator('career_school_year_id', new sfValidatorPropelChoice(array('model' => 'CareerSchoolYear','required'=> true)));
    $this->getWidgetSchema()->setLabel('career_school_year_id', 'Egresados de');
  }

  public function save()
  {
    $origin_career_school_year = CareerSchoolYearPeer::retrieveByPk($this->getValue('career_school_year_id'));
   
    $destiny_career_school_year = CareerSchoolYearPeer::retrieveByPk($this->getOption('destiny_career_id'));
    $last_year_school_year = SchoolYearPeer::retrieveLastYearSchoolYear(SchoolYearPeer::retrieveCurrent());

    $students = CareerStudentPeer::retrieveLastYearCareerGraduatedStudents($origin_career_school_year);

    $con = Propel::getConnection();

    try
    {
      $con->beginTransaction();
      
      foreach ($students as $student)
      {
        $student->registerToCareer($destiny_career_school_year->getCareer(), null, null, $destiny_career_school_year->getCareer()->getMinYear(), $con);
        $shift = $student->getShiftForSchoolYear($last_year_school_year);

        if (!$student->getIsRegistered($destiny_career_school_year->getSchoolYear()))
        {
          $student->registerToSchoolYear($destiny_career_school_year->getSchoolYear(), $shift, $con);
        }
      }
      $con->commit();
    }
    catch (PropelException $e)
    {
      $con->rollBack();
      throw $e;
    }
  }
}
