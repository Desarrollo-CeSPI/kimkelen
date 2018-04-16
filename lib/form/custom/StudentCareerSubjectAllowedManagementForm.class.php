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

class StudentCareerSubjectAllowedManagementForm extends StudentForm
{

  public function configure()
  {
    unset(
      $this['student_career_subject_allowed_pathway_list'], $this['student_career_subject_allowed_list'], $this["educational_dependency"],  $this["origin_school_id"], $this["global_file_number"], $this["person_id"], $this["occupation_id"], $this["busy_starts_at"], $this["blood_group"], $this["blood_factor"], $this["emergency_information"], $this["health_coverage_id"], $this["student_tag_list"], $this["busy_ends_at"]
    );

    $this->configureWidgetSchema();
    $this->configureValidatorSchema();

  }

  public function configureWidgetSchema()
  {
    $this->widgetSchema->setNameFormat("student[%s]");

    $sf_formatter_revisited = new sfWidgetFormSchemaFormatterRevisited($this);
    $this->getWidgetSchema()->addFormFormatter("Revisited", $sf_formatter_revisited);
    $this->getWidgetSchema()->setFormFormatterName("Revisited");

    $careers_ids = array_map(create_function("\$cs", "return \$cs->getCareerId();"), $this->getObject()->getCareerStudents());
    $criteria = new Criteria();
    $criteria->addAnd(CareerPeer::ID, $careers_ids, Criteria::IN);
    $criteria->addAscendingOrderByColumn(CareerPeer::CAREER_NAME);

    $this->widgetSchema["career_id"] = new sfWidgetFormPropelChoice(array(
        "model" => "Career",
        "criteria" => $criteria,
        "add_empty" => true
      ));

    $w = new sfWidgetFormChoice(array("choices" => array()));
    $this->setWidget('year', new dcWidgetAjaxDependence(array(
        "dependant_widget" => $w,
        "observe_widget_id" => "student_career_id",
        "message_with_no_value" => "Seleccione una carrera y apareceran los años que correspondan",
        "get_observed_value_callback" => array(get_class($this), "getYears"))));

    $this->setValidator("year", new sfValidatorInteger(array('required' => true)));

  }

  public static function getYears($widget, $value)
  {
    $career = CareerPeer::retrieveByPk($value);
    $choices = $career->getYearsForOption(true);
    $widget->setOption("choices", $choices);

  }

  public function configureValidatorSchema()
  {
    $this->validatorSchema["career_id"] = new sfValidatorPass();

    $this->validatorSchema->setOption("allow_extra_fields", true);

  }

  protected function doSave($con = null)
  {

    if (!$this->isValid())
    {
      throw $this->getErrorSchema();
    }

    if (is_null($con))
    {
      $con = $this->getConnection();
    }

    // Delete only the subjects off the selected career
    $c = new Criteria();
    $c->add(StudentCareerSubjectAllowedPeer::STUDENT_ID, $this->object->getPrimaryKey());
    // added:
    $subc = new Criteria();
    $subc->clearSelectColumns();
    $subc->addSelectColumn(CareerSubjectPeer::ID);
    $subc->add(CareerSubjectPeer::CAREER_ID, $this->getValue("career_id"));
    $stmt = CareerSubjectPeer::doSelectStmt($subc);
    $ids = $stmt->fetchAll(PDO::FETCH_COLUMN);
    $c->add(StudentCareerSubjectAllowedPeer::CAREER_SUBJECT_ID, $ids, Criteria::IN);

    $allowed = StudentCareerSubjectAllowedPeer::doSelectOne($c, $con);

    if ($allowed)
    {
        // Se consulta si el alumno esta en trayectorias antes de eliminarlo (Trayectoria actual)
        $sy = SchoolYearPeer::retrieveLastYearSchoolYear(SchoolYearPeer::retrieveCurrent());
        
        $student_id = $this->object->getPrimaryKey();
        $criteria = new Criteria();
        $criteria->add(PathwayStudentPeer::STUDENT_ID, $student_id);
        $criteria->addJoin(PathwayStudentPeer::PATHWAY_ID,PathwayPeer::ID);
        $criteria->add(PathwayPeer::SCHOOL_YEAR_ID,$sy->getId());
        $pathway = PathwayStudentPeer::doSelectOne($criteria, $con);
        if (!$pathway)
        {
          StudentCareerSubjectAllowedPeer::doDelete($c, $con);
        }
        else
        {   //Calculo las materias que rinde en trayectorias.  
            try
            {
                $con->beginTransaction();
                
                $cri = new Criteria();
                $cri->addJoin(StudentDisapprovedCourseSubjectPeer::COURSE_SUBJECT_STUDENT_ID, CourseSubjectStudentPeer::ID);
                $cri->add(CourseSubjectStudentPeer::STUDENT_ID,$student_id);
                $cri->addJoin(CourseSubjectStudentPeer::COURSE_SUBJECT_ID, CourseSubjectPeer::ID);
                $cri->addJoin(CourseSubjectPeer::CAREER_SUBJECT_SCHOOL_YEAR_ID,CareerSubjectSchoolYearPeer::ID);
                $cri->addJoin(CareerSubjectSchoolYearPeer::CAREER_SUBJECT_ID,CareerSubjectPeer::ID);
                $cri->add(CareerSubjectPeer::CAREER_ID,$this->getValue("career_id"));
                $cri->add(CareerSubjectPeer::YEAR,$pathway->getYear());
                $cri->add(StudentDisapprovedCourseSubjectPeer::STUDENT_APPROVED_CAREER_SUBJECT_ID,NULL);
                $career_subjects = CareerSubjectPeer::doSelect($cri);
                
                foreach ($career_subjects as $career_subject)
                { /* check if not exist */
                  $scsap= StudentCareerSubjectAllowedPathwayPeer::doCountStudentAndCareerSubject($this->object, $career_subject);
                  if($scsap == 0)
                  {
                      $obj = new StudentCareerSubjectAllowedPathway();
                      $obj->setStudentId($this->object->getPrimaryKey());
                      $obj->setCareerSubject($career_subject);
                      $obj->save($con);
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

    $year = $this->getValue('year');
    $career_school_year = CareerSchoolYearPeer::retrieveByCareerAndSchoolYear(CareerPeer::retrieveByPK($this->getValue('career_id')), SchoolYearPeer::retrieveCurrent());
    //First update the year at student_career_school_year

//    var_dump($career_school_year);die();
    $student_career_school_year = StudentCareerSchoolYearPeer::getCurrentForStudentAndCareerSchoolYear($this->getObject(), $career_school_year);
//    if (!$student_career_school_year)
//    {
//      $student_career_school_year = new StudentCareerSchoolYear();
//      $student_career_school_year->setCareerSchoolYear($career_school_year);
//      $student_career_school_year->setStudent($this->getObject());
//      $student_career_school_year->save();
//    }
    $career_student = CareerStudentPeer::retrieveByCareerAndStudent($this->getValue('career_id'), $this->getObject()->getId());
   
    try
    {
      $con->beginTransaction();
      $student_career_school_year->setYear($year);
      $student_career_school_year->save($con);

      $c = new Criteria();
      $c->add(CareerSubjectPeer::CAREER_ID, $this->getValue('career_id'));
      $c->add(CareerSubjectPeer::YEAR, $year);

      foreach (CareerSubjectPeer::doSelect($c) as $career_subject)
      { /* check if not exist */
        $scsa= StudentCareerSubjectAllowedPeer::doCountStudentAndCareerSubject($this->object, $career_subject);
        if($scsa == 0)
        {
            $obj = new StudentCareerSubjectAllowed();
            $obj->setStudentId($this->object->getPrimaryKey());
            $obj->setCareerSubject($career_subject);
            $obj->save($con);
        }
      }

      $prev_school_year = SchoolYearPeer::retrieveLastYearSchoolYear($career_school_year->getSchoolYear());

      if ($prev_school_year) {
      $prev_student_career_school_year = StudentCareerSchoolYearPeer::retrieveCareerSchoolYearForStudentAndYear($this->getObject(), $prev_school_year);
      }
      
      if (!empty($prev_student_career_school_year))
      {
        $prev_student_career_school_year = array_shift($prev_student_career_school_year);

        if ($year <= $prev_student_career_school_year->getYear())
        {
          $prev_student_career_school_year->setStatus(StudentCareerSchoolYearStatus::REPPROVED);
          $prev_student_career_school_year->save($con);
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