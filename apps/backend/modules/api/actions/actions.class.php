<?php

/**
 * api actions.
 *
 * @package    symfony
 * @subpackage api
 * @author     Corrons M. Emilia
 * @version    SVN: $Id: actions.class.php 12479 2008-10-31 10:54:40Z fabien $
 */
class apiActions extends sfActions
{

  public function executeIsStudent(sfWebRequest $request)
  {
    $student = $this->getRoute()->getObject();
    $this->student = $student->asArray();
  }
  
  public function executeInsertStudent(sfWebRequest $request)
  {
	 $s_lastname = $request->getParameter('apellido');
	 $s_firstname = $request->getParameter('nombres');
	 $s_identification_type = $request->getParameter('tipo_documento_id'); //ver Ids con SIPECU
	 $s_identification_number =$request->getParameter('nro_documento');
	 $s_sex =$request->getParameter('student_sexo'); //ver Ids
	 $s_phone =$request->getParameter('telefono_fijo');
	 $s_birthdate =$request->getParameter('fecha_nacimiento');
	 $s_birth_city =$request->getParameter('ciudad_nacimiento_id');
	 
	 $s_person = new Person();
	 $s_person->setLastname($s_lastname);
	 $s_person->setFirstname($s_firstname);
	 $s_person->setIdentificationType($s_identification_type);
	 $s_person->setIdentificationNumber($s_identification_number);
	 $s_person->setSex($s_sex);
	 $s_person->setPhone($s_phone);
	 $s_person->setBirthdate($s_birthdate);
	 $s_person->setIsActive(true);
	 $s_person->setBirthCity($s_birth_city);
	 
	 $student= new Student();
	 $student->setPerson($s_person);
	 $student->setHealthCoverageId($request->getParameter('obra_social_id'));
	 $student->setOriginSchoolId($request->getParameter('escuela_procedencia_numero'));
	 
	 die();
  }
}
