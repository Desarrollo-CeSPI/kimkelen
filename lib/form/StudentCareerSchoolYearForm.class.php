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
 * StudentCareerSchoolYear form.
 *
 * @package    sistema de alumnos
 * @subpackage form
 * @author     Your name here
 */
class StudentCareerSchoolYearForm extends BaseStudentCareerSchoolYearForm
{
  public function configure()
  {
	$sf_formatter_revisited = new sfWidgetFormSchemaFormatterRevisited($this);
    $this->getWidgetSchema()->addFormFormatter('Revisited', $sf_formatter_revisited);
    $this->getWidgetSchema()->setFormFormatterName('Revisited');
	
	unset($this['created_at'], $this['career_school_year_id'], $this['is_processed'] , $this['id'], $this['year']);
    
	$this->setWidget('student_id', new sfWidgetFormInputHidden());
	$status = BaseCustomOptionsHolder::getInstance('StudentCareerSchoolYearStatus')->getOptionsSelect();
	$this->setWidget('status',  new sfWidgetFormSelect(array('choices'  => $status)));
    
    $w = new sfWidgetFormChoice(array('choices' => array()));
    $this->setWidget('change_status_motive_id', new dcWidgetAjaxDependence(array(
        'dependant_widget' => $w,
        'observe_widget_id' => 'student_career_school_year_status',
        'message_with_no_value' => 'Seleccione un estado y aparecerán los motivos correspondientes',
        'get_observed_value_callback' => array(get_class($this), 'getMotives')
      )));
      
    $this->setWidget('start_date_reserve', new csWidgetFormDateInput());
    $this->setValidator('start_date_reserve', new mtValidatorDateString(array('date_output'=>'Y-m-d')));
    
    $this->setWidget('end_date_reserve', new csWidgetFormDateInput());
    $this->setValidator('end_date_reserve', new mtValidatorDateString(array('date_output'=>'Y-m-d')));
    
	//si ya tiene reserva muestro la fecha
	if($this->getObject()->getStatus() == StudentCareerSchoolYearStatus::WITHDRAWN_WITH_RESERVE)
	{
		$reserve= $this->getObject()->getStudent()->hasActiveReserve();
		
		if(!is_null($reserve))
		{ 
			$start_date = new DateTime($reserve->getStartDate());
			
			if(!is_null($start_date)){
				$this->getWidget('start_date_reserve')->setOption('default',$start_date->format('d/m/Y') );
			}	
		}
	}
    
	$this->setValidators(array(
      'student_id'              => new sfValidatorPropelChoice(array('model' => 'Student', 'column' => 'id', 'required' => false)),
      'status'   		        => new sfValidatorChoice(array('choices' => array_keys($status))),
      'change_status_motive_id' => new sfValidatorPropelChoice(array('required' => false, 'model' => 'ChangeStatusMotive','column' => 'id')),
     
    ));
    
    $this->validatorSchema->setOption("allow_extra_fields", true);
  }
  
  public static function getMotives($widget, $values)
  {
	
	$motives = ChangeStatusMotivePeer::getMotivesByStatusId($values);
	$choices = array();
	
	foreach ($motives as $m):
		$choices[$m->getId()] = $m->getName(); 
	endforeach;	
	
    $widget->setOption('choices', $choices);
  }
  
  protected function doSave($con = null)
  {
    $con = is_null($con) ? Propel::getConnection() : $con;
    $status = $this->getValue('status');
    $student = $this->getObject()->getStudent();

    try 
    {       
     switch($status)
     {
        case StudentCareerSchoolYearStatus::WITHDRAWN:
            //Retirado
            parent::doSave($con);
            //desmatricular
            $s = $student->getSchoolYearStudentForSchoolYear($this->getObject()->getCareerSchoolYear()->getSchoolYear());
            if(! is_null($s))
            {
                $s->delete();
            }
            //seteo en sus course_subject_student_mark is_closed en TRUE;
            $student->setCourseSubjectStudentMarksForSchoolYear($this->getObject()->getCareerSchoolYear()->getSchoolYear(),true);
            sfContext::getInstance()->getUser()->setFlash('info',  'The item was updated successfully.');
        
        break;
        case StudentCareerSchoolYearStatus::WITHDRAWN_WITH_RESERVE:
            //Retirado con reserva de banco
            $start_date = sfContext::getInstance()->getRequest()->getParameter('student_career_school_year[start_date_reserve]');
   
            //Si no existe la reserva la creo.
            $student_reserve = $student->hasActiveReserve();

            if(is_null($student_reserve))
            { 
                if(is_null($start_date) || $start_date == '')
                {
                     sfContext::getInstance()->getUser()->setFlash('error','El campo Fecha de inicio de la reserva es obligatorio.');	
                }
                else
                {
                    $start_date = str_replace('/', '-', $start_date);
                    $start_date = date('Y-m-d', strtotime($start_date));

                    $student_reserve = new StudentReserveStatusRecord();
                    $student_reserve->setStudentId($student->getId());
                    $student_reserve->setStartDate(new DateTime($start_date));
                    StudentReserveStatusRecordPeer::doInsert($student_reserve);
                    
                    parent::doSave($con);

                    //desmatricular
                    $s = $student->getSchoolYearStudentForSchoolYear($this->getObject()->getSchoolYear());
                    if(! is_null($s))
                    {
                        $s->delete();
                    }

                    //seteo en sus course_subject_student_mark is_closed en TRUE;
                    $student->setCourseSubjectStudentMarksForSchoolYear($this->getObject()->getCareerSchoolYear()->getSchoolYear(),true);
                    sfContext::getInstance()->getUser()->setFlash('info','The item was updated successfully.' );
                }	

            }
            else
            {   //Ya existe la reserva solo modifico la fecha
                $start_date = str_replace('/', '-', $start_date);
                $start_date = date('Y-m-d', strtotime($start_date));	
                $student_reserve->setStartDate(new DateTime($start_date));
                $student_reserve->save();		
                sfContext::getInstance()->getUser()->setFlash('info','The item was updated successfully.' );
            }

        break;
        case StudentCareerSchoolYearStatus::FREE:
            //Libre
            $max_year = $this->getObject()->getCareerSchoolYear()->getCareer()->getMaxYear();
            //chequeo que sea el ultimo año.
            if($this->getObject()->getYear() == $max_year)
            {	
                //chequeo que deba materias.
                if($student->getCountStudentRepprovedCourseSubject() > 0)
                {
                    parent::doSave($con);

                    //si no esta activo, lo activo
                    if(!$student->getPerson()->getIsActive())
                    {
                        $student->getPerson()->setIsActive(true);
                        $student->getPerson()->save();	
                    }

                    //desmatricular
                    $s = $student->getSchoolYearStudentForSchoolYear($this->getObject()->getSchoolYear());
                    if(! is_null($s))
                    {
                        $s->delete();
                    }

                     sfContext::getInstance()->getUser()->setFlash('info','The item was updated successfully.');
                }
                else
                {
                     sfContext::getInstance()->getUser()->setFlash('error','El alumno no debe materias.');	
                }
            }
            else
            {
                 sfContext::getInstance()->getUser()->setFlash('error', 'El alumno debe estar en el ultimo año de la carrera.');
            }	

        break;
        case StudentCareerSchoolYearStatus::IN_COURSE:
            //En curso. Chequeo que el estado anterior sea Retirado con Reserva de Banco.
            if($this->getObject()->getStatus() != StudentCareerSchoolYearStatus::WITHDRAWN_WITH_RESERVE)
            {
                sfContext::getInstance()->getUser()->setFlash('error', 'El estado anterior debe ser Retirado con reserva de Banco.');
            }
            else
            {
                $end_date = sfContext::getInstance()->getRequest()->getParameter('student_career_school_year[end_date_reserve]');

                if(is_null($end_date) || $end_date =='')
                {
                    sfContext::getInstance()->getUser()->setFlash('error','El campo Fecha de fin de la reserva es obligatorio.');	
                }
                else
                {
                    //guardo la fecha de fin de la reserva.
                    $end_date = str_replace('/', '-', $end_date);
                    $end_date = date('Y-m-d', strtotime($end_date));

                    $student_reserve = $student->hasActiveReserve();
                    $student_reserve->setEndDate(new DateTime($end_date));
                    $student_reserve->save();

                    //tomo todos los css y abro los períodos restantes.
                    $csss = CourseSubjectStudentPeer::retrieveByCareerSchoolYearAndStudent($this->getObject()->getCareerSchoolYear(),$student);
                    foreach ($csss as $css):

                        $cp = $css->getCourseSubject()->getCourse()->getCurrentPeriod();
                        $c = new Criteria();
                        $c->add(CourseSubjectStudentMarkPeer::COURSE_SUBJECT_STUDENT_ID,$css->getId());
                        $c->add(CourseSubjectStudentMarkPeer::MARK_NUMBER,$cp,Criteria::GREATER_EQUAL);

                        $cssm = CourseSubjectStudentMarkPeer::doSelect($c);
                        foreach ($cssm as $m):
                            $m->setIsClosed(FALSE);
                            $m->save();
                        endforeach;

                    endforeach;
                    //cambio el estado
                    parent::doSave($con);
                    sfContext::getInstance()->getUser()->setFlash('info','The item was updated successfully.');		
                }

            }

        break;
        case StudentCareerSchoolYearStatus::APPROVED:

            if($this->getObject()->getStatus() == StudentCareerSchoolYearStatus::IN_COURSE || $this->getObject()->getStatus() == StudentCareerSchoolYearStatus::FREE)
            {

                if($this->getObject()->getStatus() == StudentCareerSchoolYearStatus::FREE)
                {
                    //no debe materias.
                    if($student->getCountStudentRepprovedCourseSubject() == 0)
                    {
                        parent::doSave($con);

                        //cambio el estado de la carrera.
                        $career = $this->getObject()->getCareerSchoolYear()->getCareer();
                        $career_student = CareerStudentPeer::retrieveByCareerAndStudent($career->getId(), $student->getId());
                        $career_student->setStatus(CareerStudentStatus::GRADUATE);
                        $current_school_year = SchoolYearPeer::retrieveCurrent();
                        $career_student->setGraduationSchoolYearId($current_school_year->getId());
                        $career_student->save(Propel::getConnection());
                        
                        $student->getPerson()->setIsActive(false);
                        $student->getPerson()->save();

                        sfContext::getInstance()->getUser()->setFlash('info','The item was updated successfully.');
                    }	
                    else
                    {
                        sfContext::getInstance()->getUser()->setFlash('error','El alumno tiene materias previas sin aprobar.');
                    }

                }
                else
                {
                    /*SOLO CHEQUEA QUE TENGA LAS MATERIAS CERRADAS*/
                    $course_subject_students = $student->getCourseSubjectStudentsForSchoolYear($this->getObject()->getCareerSchoolYear()->getSchoolYear());
                    $css = array_shift($course_subject_students);

                    //Si tengo alguna materia sin cerrar
                    if (!$css->areAllMarksClosed())
                    {
                        sfContext::getInstance()->getUser()->setFlash('error',"El alumno tiene cursadas sin cerrar.");
                    }
                    else
                    {
                        parent::doSave($con);
                        sfContext::getInstance()->getUser()->setFlash('info','The item was updated successfully.');
                    }
                }		
            }
            else
            {
                sfContext::getInstance()->getUser()->setFlash('error','El alumno debe estar cursando o estar Libre.');
            }

        break;
    }
    
   }
   catch (PropelException $e)
   {
      $this->getUser()->setFlash('error', 'The item has not been saved due to some errors.', false);
   }
  }
   
}
    
