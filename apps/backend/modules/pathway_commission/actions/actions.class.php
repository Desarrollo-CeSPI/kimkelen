<?php

require_once dirname(__FILE__) . '/../lib/pathway_commissionGeneratorConfiguration.class.php';
require_once dirname(__FILE__) . '/../lib/pathway_commissionGeneratorHelper.class.php';

/**
 * pathway_commission actions.
 *
 * @package    symfony
 * @subpackage pathway_commission
 * @author     Your name here
 * @version    SVN: $Id: actions.class.php 12474 2008-10-31 10:41:27Z fabien $
 */
class pathway_commissionActions extends autoPathway_commissionActions
{

    protected function getForms($course_subjects)
    {
        $forms = array();
        $i = 0;
        foreach ($course_subjects as $course_subject)
        {
            $forms[$course_subject->getId()] = new PathwayCourseSubjectStudentManyForm($course_subject);
            $forms[$course_subject->getId()]->getWidgetSchema()->setNameFormat("course_subject_${i}[%s]");
            $i++;
        }

        return $forms;
    }

    public function executeAddSubject(sfWebRequest $request)
    {
        if ($request->isMethod('POST'))
        {
            $params = $request->getPostParameters();
            $this->course = CoursePeer::retrieveByPk($params['course']['id']);
            $this->form = new SubjectForPathwayCommissionForm($this->course);
            $this->form->bind($request->getParameter($this->form->getName()), $request->getFiles($this->form->getName()));
            if ($this->form->isValid())
            {
                $this->form->save();

                $this->getUser()->setFlash("notice", "New subject added to commission successfully");

                $this->redirect("@pathway_commission");
            }
        }
        else
        {
            $this->course = $this->getRoute()->getObject();
            $this->course_subjects = $this->course->getCourseSubjects();
            $this->form = new SubjectForPathwayCommissionForm($this->course);
        }
    }

    public function executeDeleteSubject(sfWebRequest $request)
    {
        //TODO: Ver de extenderlo de commissionActions
        $cs = CourseSubjectPeer::retrieveByPK($request->getParameter('course_subject_id'));

        if ($cs and $course = $cs->getCourse() and $course->isPathway())
        {
            try
            {
                $cs->delete();
                $this->getUser()->setFlash("notice", "The item was deleted successfully.");
            }
            catch (PropelException $e)
            {
                $this->getUser()->setFlash('error', 'A problem occurs when deleting the selected items.');
            }
        }
        else
        {
            $this->getUser()->setFlash('error', 'The selected item is not a pathway commission.');
        }

        $this->redirect("@pathway_commission");
    }

    /**
     * alumnos
     */
    public function executeCourseSubjectStudent(sfWebRequest $request)
    {
        $this->course = $this->getRoute()->getObject();
        $this->course_subjects = $this->course->getCourseSubjects();
        $this->forms = $this->getForms($this->course_subjects);

        $this->handleSelectedTab($request);
    }

    public function handleSelectedTab(sfWebRequest $request)
    {
        if ($request->hasParameter("selected"))
        {
            $this->selected = $request->getParameter("selected");
        }
        else
        {
            // siempre hay uno.
            $this->selected = $this->course_subjects[0]->getId();
        }
    }

    public function executeUpdateCourseSubjectStudents(sfWebRequest $request, $con = null)
    {
        if (!$request->isMethod("POST"))
        {
            $this->redirect($this->referer_module . '/index');
        }

        $this->course = CoursePeer::retrieveByPK($request->getParameter('id'));
        $this->course_subjects = $this->course->getCourseSubjects();
        $this->forms = $this->getForms($this->course_subjects);

        $this->handleSelectedTab($request);

        $valid = count($this->forms);

        foreach ($this->forms as $form)
        {
            $form->bind($request->getParameter($form->getName()));

            if ($form->isValid())
            {
                $valid--;
            }
        }

        if (is_null($con))
        {
            $con = Propel::getConnection(DivisionPeer::DATABASE_NAME, Propel::CONNECTION_WRITE);
        }

        $con->beginTransaction();
        try
        {
            if ($valid == 0)
            {
                foreach ($this->forms as $form)
                {
                    $form->save($con);
                }
                $this->getUser()->setFlash('notice', 'Los alumnos se guardaron satisfactoriamente.');
            }
            else
            {
                $this->getUser()->setFlash('error', 'Ocurrieron errores al intentar guardar los alumnos. Por favor, intente nuevamente la operación.');
            }
            $con->commit();
        }
        catch (Exception $e)
        {
            $con->rollBack();
            $this->getUser()->setFlash('error', $e->getMessage() . 'hjgjh');
        }

        $this->setTemplate('courseSubjectStudent');
    }

  public function executeCalifications(sfWebRequest $request)
  {
    $this->course = $this->getRoute()->getObject();

    $this->getUser()->setAttribute("referer_module", "pathway_commission");

    $this->redirect("course_student_mark/index?id=" . $this->course->getId());

  }

  public function executePrintCalifications(sfWebRequest $request)
  {
    $this->course = $this->getRoute()->getObject();
    $this->course_subjects = $this->course->getCourseSubjects();
    $this->setLayout('cleanLayout');
    $this->getUser()->setAttribute("referer_module", "commission");
    
    if (count($this->course_subjects) <= 0)
    {
      $this->getUser()->setFlash('error', 'La comision no posee materia/s.');
      $this->redirect("@pathway_commission");
    }

  }

  public function executeClose(sfWebRequest $request)
  {
    $this->course = $this->getRoute()->getObject();
  }

  public function executeSaveClose(sfWebRequest $request)
  {
    // TODO
    // si es mayor que 7 crear el student approved career subject. si no mandarlo a previa.
    sfContext::getInstance()->getConfiguration()->loadHelpers('I18N');
    $this->course = CoursePeer::retrieveByPk($request->getParameter('id'));
    $this->course->pathwayClose();
    $this->getUser()->setFlash('notice', __('The course has been closed successfuly'));
    $this->setTemplate('close');
  }
  
   public function executePreceptors(sfWebRequest $request)
  {
    $this->course = $this->getRoute()->getObject();
    $this->form = new CoursePreceptorsForm($this->course);
  }
  
  public function executeUpdatePreceptors(sfWebRequest $request)
  {
    $this->course = CoursePeer::retrieveByPk($request->getParameter('id'));

    if (null === $this->course)
    {
      $this->getUser()->setFlash('error', 'Debe seleccionar una comisión para configurar sus preceptores');

      $this->redirect('@commission');
    }

    $this->form = new CoursePreceptorsForm($this->course);

    $this->form->bind($request->getParameter($this->form->getName()), $request->getFiles($this->form->getName()));
    if ($this->form->isValid())
    {
      $this->form->save();

      $this->getUser()->setFlash('notice', 'Los preceptores seleccionados han sido correctamente asignados a la comisión.');
    }
    else
    {
      $this->setProcessFormErrorFlash();
    }

    $this->setTemplate('preceptors');

  }
  
  public function executeAttendanceSubject(sfWebRequest $request)
  {
    $this->redirectIf($this->getUser()->isTeacher());
    $course = $this->getRoute()->getObject();
    if (count($course->getCourseSubjects()) > 1){
      $course_id = $course->getId();
      $this->redirect("student_attendance/MultipleSubjectsCommissionAttendance?course=$course_id&division_id=");
    }
    else {
      $career_school_year_id = $course->getCareerSchoolYear()->getId();
      $course_subject_id = array_shift($course->getCourseSubjectIds());
      $year = $course->getYear();
      $this->redirect("student_attendance/StudentAttendance?url=pathway_commission&year=$year&course_subject_id=$course_subject_id&career_school_year_id=$career_school_year_id&division_id=");
    }
  }
  
  public function executeGenerateRecordSubject(sfWebRequest $request)
  {
       $con =  Propel::getConnection();
       
       try
       {  
            $cs = CourseSubjectPeer::retrieveByPK($request->getParameter('course_subject_id'));
            $setting = SettingParameterPeer::retrieveByName(BaseSchoolBehaviour::LINES_PATHWAY);

                $r = new Record();
                $r->setRecordType(RecordType::COURSE);
                $r->setCourseOriginId($cs->getId());
                $r->setLines($setting->getValue());
                $r->setStatus(RecordStatus::ACTIVE); 
                $r->setUsername(sfContext::getInstance()->getUser());
                $r->save();

                $record = RecordPeer::retrieveByCourseOriginIdAndRecordType($cs->getId(), RecordType::COURSE);

                $i = 1;
                $sheet =1;
                $record_sheet = new RecordSheet();
                $record_sheet->setRecord($record);
                $record_sheet->setSheet($sheet);
                $record_sheet->save();

                foreach ($cs->getCourseSubjectStudentPathways() as $cssp)
                {
                   $rd = new RecordDetail();
                   $rd->setRecordId($record->getId());
                   $rd->setStudent($cssp->getStudent());
                   $rd->setMark($cssp->getMark());
                   $rd->setIsAbsent(FALSE);
                   if ($cssp->getMark() < SchoolBehaviourFactory::getEvaluatorInstance()->getPathwayPromotionNote())
                   {
                       $rd->setResult(SchoolBehaviourFactory::getEvaluatorInstance()->getDisapprovedResult());
                   }
                   else
                   {
                       $rd->setResult(SchoolBehaviourFactory::getEvaluatorInstance()->getApprovedResult());
                   }

                   $rd->setLine($i);

                   if ($i > ($sheet * $record->getLines()))
                   {
                       $sheet ++;
                       $record_sheet = new RecordSheet();
                       $record_sheet->setRecord($record);
                       $record_sheet->setSheet($sheet);
                       $record_sheet->save();

                   }
                   $rd->setSheet($sheet);
                   $i++;

                   $rd->save();

                   ####Liberando memoria###
                   $rd->clearAllReferences(true);
                   unset($rd);
                   ##################*/
                }
            
            
            $con->commit();
        echo "listo";die();
       }
       catch (Exception $e)
       {
          $con->rollBack();
          $this->getUser()->setFlash('error', 'Ocurrió un error y no se guardaron los cambios.');
          $this->redirect('@pathway_commission');
       }
              
  }
  
  public function executeGenerateRecord(sfWebRequest $request)
  {
      $this->course = $this->getRoute()->getObject();
      $this->course_subjects = $this->course->getCourseSubjects();
      
      if (count($this->course_subjects) == 1)
      {
          
          $this->redirect("pathway_commission/generateRecordSubject?course_subject_id=" . $this->course->getCourseSubject()->getId());
      }
      
  }

}
