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

class myUser extends sfGuardSecurityUser
{

    static $reference_key = "students.referrer.from.%s.key";

    /**
     * This method is intended for be used inside sfActions subclasess so when
     * user access some action from that module, and this method is called, we can
     * get track of some ID for sfActions module to be considered in future calls
     * in other modules. For example if we are working with career and we want to
     * view students registered in that career, we will set carrer id as referrer
     *
     * @param sfActions $action Action from which we are called. Attribute key will
     * be extracted from $action->getModuleName
     * @param <type> $override_route_object if $action->getRoute()->getObject()->getId()
     * is not applicable, then provide which value is to be saved as referrer
     * @param <type> $override_key if getModuleName is not enough, you could use this parameter
     */
    public function setReferenceFor(sfActions $action, $override_route_object=false, $override_key=false )
    {
      $key = $override_key!==false? $override_key:$action->getModuleName();
      $id = $override_route_object!==false? $override_route_object: $action->getRoute()->getObject()->getId();
      return $this->setAttribute(sprintf(self::$reference_key,$key),$id);
    }

    /**
     * Companion method for setReferenceFor. This method will return the attribute
     * stored with setReferenceFor, so access to module context object will be posible
     *
     * @param string $module_name name of the override_key / module used when set id
     * @return int  Associated id
     */
    public function getReferenceFor($module_name)
    {
      return $this->getAttribute(sprintf(self::$reference_key,$module_name));
    }

    public function removeReferenceFor($module_name)
    {
      $this->getAttributeHolder()->remove(sprintf(self::$reference_key,$module_name));
    }


    /**
   *  dcStatefulSecurity plugin
   *
   *  canActionModule
   */
    ////////////////////////////////////////////////////////////////////////////

  /* Career conditions */
  public function canEditCareer($career)
  {
    return $career->canBeEdited();
  }

  public function canDeleteCareer($career)
  {
    return $career->canBeDeleted();
  }


  public function canEditCorrelativesCareerSubject($career_subject)
  {
    /* @var $career_subject CareerSubject */
    return $career_subject->canBeEditedCorrelatives();
  }

  /* I quit this since this restrictions are no longer being used.
   *
  public function canNewCareerSubject($career)
  {
    $career = CareerPeer::retrieveByPK($this->getReferenceFor('career'));

    return $career->canCreateNewCareerSubject();
  }
   * *
   */


  /* CareerSubjectSchoolYear conditions */

   public function canCreateCareerCareerSchoolYear()
  {
    $school_year = SchoolYearPeer::retrieveByPk($this->getReferenceFor('schoolyear'));

    if (is_null($school_year))
      return false;

    return true;
  }


  public function canConfigurationCareerSchoolYear($career_school_year)
  {
    return $career_school_year->canBeEdited();
  }

  public function canConfigurationCareerSubjectSchoolYear($career_subject_school_year)
  {
    return true;//$career_subject_school_year->canBeEdited();
  }



      /**###### DIVISION ######**/
  public function canShowDivision(Division $division)
  {
    if($this->isPreceptor())
    {
      return $division->canBeSeenByPreceptorUser($this->getGuardUser());
    }
    elseif($this->isTeacher())
    {
      return $division->canBeSeenByTeacherUser($this->getGuardUser());
    }
    return true;
  }

  public function canDivisionStudentsDivision(Division $division)
  {
    if($this->isPreceptor())
    {
      return $division->canBeEditedByPreceptorUser($this->getGuardUser());
    }
    return true;
  }

  public function canUpdateDivisionStudentsDivision($division)
  {
    if($this->isPreceptor())
    {
      if(sfContext::getInstance()->getRequest()->hasParameter('division[id]'))
      {
        $division_id = sfContext::getInstance()->getRequest()->getParameter('division[id]');
        $division = DivisionPeer::retrieveByPK($division_id);
      }

      $division->canBeEditedByPreceptorUser($this->getGuardUser());
    }
    return true;
  }

  public function canDivisionAttendanceDayDivision($division)
  {
    if($this->isPreceptor())
    {
      if(sfContext::getInstance()->getRequest()->hasParameter('division_id'))
      {
        $division_id = sfContext::getInstance()->getRequest()->getParameter('division_id');
        $division = DivisionPeer::retrieveByPK($division_id);
      }

      $division->canBeEditedByPreceptorUser($this->getGuardUser());
    }
    return true;
  }


  public function canUpdateDivisionAttendanceDayDivision($division)
  {
    return $this->canDivisionAttendanceDayDivision($division);
  }

  public function canDivisionCoursesDivision($division)
  {
    if($this->isPreceptor())
    {
      return $division->canBeEditedByPreceptorUser($this->getGuardUser());
    }
    elseif($this->isTeacher())
    {
      return $division->canBeSeenByTeacherUser($this->getGuardUser());
    }
    return true;
  }
      /**###### END DIVISION ######**/


      /**###### COURSE ######**/

  public function canEditCourse($course)
  {
    return $course->canBeEdited();
  }

  public function canDeleteCourse($course)
  {
    return $course->canBeDeleted();
  }

  public function canManageStudents($course)
  {
    return $course->canCourseSubjectStudent();
  }

  public function canIndexCourseStudentMark($course)
  {
    if(!$course)
    {
     $course = CoursePeer::retrieveByPK(sfContext::getInstance()->getRequest()->getParameter("id"));
     return true;
     //Because is an extraordinary course, is not be in a division, and the preceptors are related to a course by a division.
     //So, we show all the extraordinary courses to a preceptor
    }

    if($this->isPreceptor())
    {
      return $course->canBeStudentMarksEditedByPreceptorUser($this->getGuardUser());
    }
    elseif($this->isTeacher())
    {
      return $course->canBeStudentMarksEditedByTeacherUser($this->getGuardUser());
    }


    return true;
  }
      /**###### END COURSE ######**/


      /**###### COMMISSION ######**/

  public function canEditCommission($course)
  {
    if($this->isPreceptor())
    {
      return $course->canBeEditedByPreceptorUser($this->getGuardUser());
    }
    elseif($this->isTeacher())
    {
      return $course->canBeEditedByTeacherUser($this->getGuardUser());
    }

    return $course->canBeEdited();
  }

  public function canDeleteCommission($course)
  {
    return $course->canBeDeleted();
  }
      /**###### END COURSE ######**/


////////////////////////////////////////////////////////////////////////////

  /*
   *  PmModuleEnabler
   */
////////////////////////////////////////////////////////////////////////////
  public function disciplinarySanctionListsIsEnabled()
  {
    return pmConfiguration::getInstance()->isEnabled('disciplinarysanctionlists');
  }

  public function justificationTypeIsEnabled()
  {
    return pmConfiguration::getInstance()->isEnabled('justification_type');
  }

  public function absenceReasonIsEnabled()
  {
    return pmConfiguration::getInstance()->isEnabled('absence_reason');
  }

  public function absencePerDayIsEnabled()
  {
    return pmConfiguration::getInstance()->isEnabled('absence_per_day');
  }

  public function absencePerSubjectIsEnabled()
  {
    return pmConfiguration::getInstance()->isEnabled('absence_per_subject');
  }

  public function statisticsIsEnabled()
  {
    return $this->disapprovedStudentIsEnabled() | $this->deserterStudentIsEnabled() | $this->repeaterStudentIsEnabled();
  }

  public function disapprovedStudentIsEnabled()
  {
    return pmConfiguration::getInstance()->isEnabled('disapproved_student');
  }

  public function deserterStudentIsEnabled()
  {
    return pmConfiguration::getInstance()->isEnabled('deserter_student');
  }

  public function repeaterStudentIsEnabled()
  {
    return pmConfiguration::getInstance()->isEnabled('repeater_student');
  }
  ////////////////////////////////////////////////////////////////////////////

  //Know the group of the user
  public function isPreceptor()
  {
    return $this->getAttribute('login_role') == 'Preceptor';
    //return $this->getGuardUser()->hasGroup('Preceptor');
  }

  public function isHeadPreceptor()
  {
    return $this->getGuardUser()->hasGroup('Jefe de preceptores');
  }

  public function isTeacher()
  {
    return $this->getAttribute('login_role') == 'Profesor';
//    return $this->getGuardUser()->hasGroup('Profesor');
  }

  public function isAdministrator()
  {
    return $this->getGuardUser()->hasGroup('Administrador');
  }

  public function getPreceptor()
  {
    $c = new Criteria();
    $c->add(PersonPeer::USER_ID, $this->getGuardUser()->getId());
    $c->addJoin(PersonPeer::ID, PersonalPeer::PERSON_ID);

    return PersonalPeer::doSelectOne($c);
  }

  /**
   * This method returns the personals ids related in head_personal_personal of the head_personal. The User must be headPreceptor
   *
   * @return array //with the Ids of the personal related
   */
  public function getPersonalIds()
  {
    if ($this->isHeadPreceptor())
    {
      $c = new Criteria();
      $c->add(PersonPeer::USER_ID, $this->getGuardUser()->getId());
      $c->addJoin(PersonPeer::ID, PersonalPeer::PERSON_ID);
      $c->addJoin(HeadPersonalPersonalPeer::HEAD_PERSONAL_ID, PersonalPeer::ID);
      $head_personal = PersonalPeer::doSelectOne($c);

      $personal_in =array();

      if (!is_null($head_personal))
      {
        foreach($head_personal->getHeadPersonalPersonals() as $head_personal_personal)
        {
          $personal_in[] = $head_personal_personal->getPersonalId();
        }
      }

      return $personal_in;
    }
  }

  public function getLoginRole()
  {
    return $this->getAttribute('login_role');
  }

  public function setLoginRole($role)
  {
    return $this->setAttribute('login_role', $role);
  }

  /**
   * Chooses a role from the groups the user is in, right after the last one logs in.
   *
   */
  public function loginRole()
  {
    $c = new Criteria();
    $c->add(sfGuardUserGroupPeer::USER_ID, $this->getGuardUser()->getId(), Criteria::EQUAL);
    $c->addJoin(sfGuardGroupPeer::ID, sfGuardUserGroupPeer::GROUP_ID);
    $role = sfGuardGroupPeer::doSelectOne($c);

    if ($role)
    {
      if ($this->isPreceptor())
      {
        $this->setLoginRole('Preceptor');
      }
      else
      {
        $this->setLoginRole($role->getName());
      }
    }
  }

  public function signOut()
  {
    $this->setLoginRole(null);
    parent::signOut();
  }

  /****************
   * Menu methods *
   ****************/

  /**
   * Adds the career menu to the main menu.
   *
   * @param pmJSCookMenu $menu
   * @return void
   */
  public function addCareerMenu(pmJSCookMenu $menu)
  {
    $career = CareerPeer::retrieveByPK($this->getReferenceFor("career"));

    sfContext::getInstance()->getConfiguration()->loadHelpers(array("I18N"));

    if (!is_null($career))
    {
      $menu->addChild("menu_separator", new pmJSCookMenuSeparator());

      $submenu = new pmJSCookMenu();
      $submenu
        ->setTitle("<strong>".__("Current career")."</strong>")
        ->setCredentials("edit_career");

      $item = new pmJSCookMenuItem();
      $item->setTitle($career);
      $submenu->addChild("career_name", $item);

      $submenu->addChild("first_separator", new pmJSCookMenuSeparator());

      // edit
      if ($career->canBeEdited())
      {
        $item = new pmJSCookMenuItem();
        $item
          ->setTitle("Edit")
          ->setUrl("@career_edit?id=".$career->getId())
          ->setCredentials("edit_career");
        $submenu->addChild("edit", $item);
      }

      // delete
      if ($career->canBeDeleted())
      {
        // TODO: ver esto porque REST no funciona con JS
        $delete_url = url_for(array(
          "sf_route" => "career_delete",
          "sf_subject" => $career,
          "sf_method" => "delete"
        ));
        $item = new pmJSCookMenuItem();
        $item
          ->setTitle("Delete")
          ->setUrl($delete_url)
          ->setCredentials("edit_career");
        $submenu->addChild("delete", $item);
      }

      if ($career->canBeEdited() || $career->canBeDeleted())
      {
        $submenu->addChild("second_separator", new pmJSCookMenuSeparator());
      }

      // copy
      $item = new pmJSCookMenuItem();
      $item
        ->setTitle("Copy")
        ->setUrl("career/copy?id=".$career->getId())
        ->setCredentials("edit_career");
      $submenu->addChild("copy", $item);

      // subjects
      $item = new pmJSCookMenuItem();
      $item
        ->setTitle("Subjects")
        ->setUrl("career/subjects?id=".$career->getId())
        ->setCredentials("edit_career");
      $submenu->addChild("subjects", $item);

      // subject options
      $item = new pmJSCookMenuItem();
      $item
        ->setTitle("Subject options")
        ->setUrl("career/subjectOptions?id=".$career->getId())
        ->setCredentials("edit_career");
      $submenu->addChild("subject_options", $item);

      // career view
      $item = new pmJSCookMenuItem();
      $item
        ->setTitle("Career view")
        ->setUrl("career/careerView?id=".$career->getId())
        ->setCredentials("edit_career");
      $submenu->addChild("career_view", $item);

      // show inscripted
      $item = new pmJSCookMenuItem();
      $item
        ->setTitle("Show inscripted")
        ->setUrl("career/students?id=".$career->getId())
        ->setCredentials("edit_career");
      $submenu->addChild("career_view", $item);

      $menu->addChild("career", $submenu);
    }
  }

  public function canCreateExamination()
  {
    $school_year = SchoolYearPeer::retrieveByPK($this->getReferenceFor("schoolyear"));

    $not_closed_examination_subjects_count = ExaminationSubjectPeer::countNotClosedExaminationSubjectsFor($school_year);

   // return $school_year->countExaminations() < $school_year->getMaxCourseExaminationCount() && $not_closed_examination_subjects_count == 0;
    return $not_closed_examination_subjects_count == 0;
  }

  public function canCreateExaminationRepproved()
  {
    $school_year = SchoolYearPeer::retrieveByPK($this->getReferenceFor("schoolyear"));

    return  ExaminationRepprovedSubjectPeer::canCreateExaminationRepprovedFor($school_year) && $school_year->getIsActive();
  }

  public function canCreateFinalExamination()
  {
    $school_year = SchoolYearPeer::retrieveByPK($this->getReferenceFor("schoolyear"));

    return $school_year->getIsActive();
  }
  public function clearAttribute($name)
  {
    $this->attributeHolder->remove($name);

  }

  public function canStudentAttendanceStudentAttendance()
  {
    return $this->hasCredential(array(array('edit_absense_per_subject', 'edit_absense_per_day')));
  }
}