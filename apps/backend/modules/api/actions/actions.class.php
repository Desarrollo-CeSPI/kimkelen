<?php

/**
 * api actions.
 *
 * @package    symfony
 * @subpackage api
 * @author     Corrons M. Emilia
 * @version    SVN: $Id: actions.class.php 12479 2008-10-31 10:54:40Z fabien $
 */
class FakeUser
{
	public function getUsername() {
		return "SIPECU";
	}
	
	public function shutdown()
	{
		//stub method
	}
}

class apiActions extends sfActions
{
  

  public function executeIsStudent(sfWebRequest $request)
  {
    $student = $this->getRoute()->getObject();
    $this->student = $student->asArray();
  }
  
  public function executeConfirmStudent(sfWebRequest $request)
  { 
	 sfContext::getInstance()->set("user", new FakeUser());
	 
	 //tomo las intancias de las librerias.
	 $i_identification_type =  BaseCustomOptionsHolder::getInstance('IdentificationType');
	 $i_sex_type = BaseCustomOptionsHolder::getInstance('SexType');
	 $i_nationality = BaseCustomOptionsHolder::getInstance('Nationality');
	 
	 $s_lastname = $this->getRequestParameter('apellido');// Es obligatorio
	 $s_firstname = $this->getRequestParameter('nombres');// Es obligatorio
	 $s_identification_type = $i_identification_type->getIdentificationType($this->getRequestParameter('tipo_documento_id'));
	 $s_identification_number =$this->getRequestParameter('nro_documento');
	 $s_sex = $i_sex_type->getSexType($this->getRequestParameter('sexo')); //Es obligatorio
	 $s_phone =$this->getRequestParameter('telefono_fijo');
	 $s_birthdate =$this->getRequestParameter('fecha_nacimiento');
	 $s_birth_city =$this->getRequestParameter('ciudad_nacimiento_id');
	 $s_health_coverage_id = $this->getRequestParameter('obra_social_id');
	 $s_origin_school_id = $request->getParameter('escuela_procedencia_numero');
	 
	 //domicilio
	 $s_city = $this->getRequestParameter('domicilio_ciudad_id');
	 $s_street = $this->getRequestParameter('domicilio_calle');
	 $s_number =$this->getRequestParameter('domicilio_numero');
	 $s_floor = $this->getRequestParameter('domicilio_piso');
	 $s_flat =$this->getRequestParameter('domicilio_departamento');
	 
	 //Chequeo tutor (madre)
	 $m_identification_type = $i_identification_type->getIdentificationType($this->getRequestParameter('madre_tipo_documento_id'));
	 $m_identification_number = $this->getRequestParameter('madre_nro_documento');
	 $m_firstname = $this->getRequestParameter('madre_nombres'); 
	 $m_lastname = $this->getRequestParameter('madre_apellido');
	 $m_occupation = $this->getRequestParameter('madre_actividad_id');
	 $m_occupation_category = $this->getRequestParameter('madre_ocupacion_id');
	 $m_study = $this->getRequestParameter('madre_estudios_id');
	 $m_email = $this->getRequestParameter('madre_email');
	 $m_phone = $this->getRequestParameter('madre_telefono_celular');
	 $m_birthdate =$this->getRequestParameter('madre_fecha_nacimiento');
	 $m_birth_city =$this->getRequestParameter('madre_ciudad_nacimiento_id');
	 $m_nationality = $i_nationality->getNationality($this->getRequestParameter('madre_nacionalidad_id'));
	 $m_is_alive = $this->getRequestParameter('madre_vive');
	 
	 //chequeo is_alive
	 if($m_is_alive == 'S'){
		$m_is_alive = true;
	 }elseif($m_is_alive == 'N'){
		$m_is_alive = false;
	 }
	 
	 //domicilio
	 $m_city = $this->getRequestParameter('madre_domicilio_ciudad_id');
	 $m_street =$this->getRequestParameter('madre_domicilio_calle');
	 $m_number =$this->getRequestParameter('madre_domicilio_numero');
	 $m_floor = $this->getRequestParameter('madre_domicilio_piso');
	 $m_flat =$this->getRequestParameter('madre_domicilio_departamento');
	 
	 //Chequeo tutor (padre)
	 $p_identification_type = $i_identification_type->getIdentificationType($this->getRequestParameter('padre_tipo_documento_id'));
	 $p_identification_number = $this->getRequestParameter('padre_nro_documento');
	 $p_firstname = $this->getRequestParameter('padre_nombres'); 
	 $p_lastname = $this->getRequestParameter('padre_apellido');
	 $p_occupation = $this->getRequestParameter('padre_actividad_id');
	 $p_occupation_category = $this->getRequestParameter('padre_ocupacion_id');
	 $p_study = $this->getRequestParameter('padre_estudios_id');
	 $p_email = $this->getRequestParameter('padre_email');
	 $p_phone = $this->getRequestParameter('padre_telefono_celular');
	 $p_birthdate =$this->getRequestParameter('padre_fecha_nacimiento');
	 $p_birth_city =$this->getRequestParameter('padre_ciudad_nacimiento_id');
	 $p_nationality = $i_nationality->getNationality($this->getRequestParameter('padre_nacionalidad_id'));
	 $p_is_alive = $this->getRequestParameter('padre_vive');
	 //chequeo is_alive
	 if($p_is_alive == 'S'){
		$p_is_alive = true;
	 }elseif($p_is_alive == 'N'){
		$p_is_alive = false;
	 }
	 	
	 //domicilio
	 $p_city = $this->getRequestParameter('padre_domicilio_ciudad_id');
	 $p_street =$this->getRequestParameter('padre_domicilio_calle');
	 $p_number =$this->getRequestParameter('padre_domicilio_numero');
	 $p_floor = $this->getRequestParameter('padre_domicilio_piso');
	 $p_flat =$this->getRequestParameter('padre_domicilio_departamento');
	 $data=array();
	 //chequeo campos obligatorios
	 if(is_null($s_identification_type) || is_null($s_identification_number) || is_null($s_lastname) || trim($s_lastname) == "" || is_null($s_firstname) || trim($s_firstname) =="" || is_null($s_sex)){
		
		throw new Exception('Missing data');
	 }
	 else
	 { 
		$con = Propel::getConnection();

		try
		{
			//chequeo que el alumno no haya sido ingresado en un año anterior (por lista de espera)
			$student = StudentPeer::retrieveByDocumentTypeAndNumber($s_identification_type,$s_identification_number);
		    $con->beginTransaction();

			if(is_null($student))
			{	//el alumno no existe. Creo la persona y el alumno
				
				$s_person = new Person();
				$s_person->setLastname($s_lastname);
				$s_person->setFirstname($s_firstname);
				$s_person->setSex($s_sex);
				$s_person->setIdentificationType($s_identification_type);
				$s_person->setIdentificationNumber($s_identification_number);
				
				$s_person->setPhone($s_phone);
				$s_person->setBirthdate($s_birthdate);
				$s_person->setIsActive(true);
				$s_person->setBirthCity($s_birth_city);
				
				$s_person->save(Propel::getConnection());
				
				$student= new Student();
				$student->setPerson($s_person); 
				$student->setGlobalFileNumber('888888');
				
				//chequeo que la escuela este en la BBDD
				$school = OriginSchoolPeer::retrieveByPk($s_origin_school_id);
				
				if(! is_null($school)){
					$student->setOriginSchoolId($s_origin_school_id);
				}
				
				$student->setHealthCoverageId($s_health_coverage_id);  
				$student->save(Propel::getConnection());
				
				/* Recupero department, state ,country*/
				if(!is_null($s_birth_city)){
					$city = CityPeer::retrieveByPk($s_birth_city);
					$student->getPerson()->setBirthCountry($city->getDepartment()->getState()->getCountry()->getId());
					$student->getPerson()->setBirthState($city->getDepartment()->getState()->getId());
					$student->getPerson()->setBirthDepartment($city->getDepartment()->getId());
				}
				//chequeo domicilio
				if( ! is_null($s_city) || ! is_null($s_street)  || ! is_null($s_number) || ! is_null($s_floor) || is_null($s_flat)){
					$a = new Address();
					$a->setCityId($s_city);
					$a->setStreet($s_street);
					$a->setNumber($s_number);
					$a->setFloor($s_floor);
					$a->setFlat($s_flat);
					
					$student->getPerson()->setAddress($a);
					$student->getPerson()->save(Propel::getConnection());	
					$data['message'] = "El alumno ha sido confirmado.";
				
				}
			
			}else{
				//seteo isActive
				$student->getPerson()->setIsActive(true);
				$student->save(Propel::getConnection());
				
				$data['message'] = "El alumno fue actualizado correctamente.";
			}
			
			//chequeo campos obligatorios
			if( ! is_null($m_identification_type) && ! is_null($m_identification_number) && ! is_null($m_lastname) &&  trim($m_lastname) != "" && ! is_null($m_firstname) && trim($m_firstname) != "")
			{
				//busco si ya existe.
				$m_tutor = TutorPeer::findByDocumentTypeAndNumber($m_identification_type,$m_identification_number);
				
				if(is_null($m_tutor))
				{
					//el tutor no existe. Lo creo
					$m_person = new Person();
					$m_person->setLastname($m_lastname);
					$m_person->setFirstname($m_firstname);
					$m_person->setIdentificationType($m_identification_type);
					$m_person->setIdentificationNumber($m_identification_number);
					$m_person->setSex(SexType::FEMALE);
					$m_person->setPhone($m_phone);
					$m_person->setEmail($m_email);
					$m_person->setBirthdate($m_birthdate);
					$m_person->setIsActive(true);
					$m_person->setBirthCity($m_birth_city);
					$m_person->save(Propel::getConnection());
					
					$m_tutor = new Tutor();
					$m_tutor->setPerson($m_person);
					$m_tutor->setOccupationId($m_occupation);
					$m_tutor->setOccupationCategoryId($m_occupation_category);
					$m_tutor->setStudyId($m_study);//coincide con la tabla sga_tipos_est_cur
					$m_tutor->setNationality($m_nationality);
					$m_tutor->setIsAlive($m_is_alive);
					$m_tutor->save(Propel::getConnection());		
					
					/* Recupero department, state ,country*/				
					if(!is_null($m_birth_city)){
						$m_city = CityPeer::retrieveByPk($m_birth_city);
						$m_tutor->getPerson()->setBirthCountry($m_city->getDepartment()->getState()->getCountry()->getId());
						$m_tutor->getPerson()->setBirthState($m_city->getDepartment()->getState()->getId());
						$m_tutor->getPerson()->setBirthDepartment($m_city->getDepartment()->getId());
					}

					//chequeo domicilio
					if( ! is_null($m_city) || ! is_null($m_street)  || ! is_null($m_number) || ! is_null($m_floor) || is_null($m_flat)){
						$a = new Address();
						$a->setCityId($m_birth_city);
						$a->setStreet($m_street);
						$a->setNumber($m_number);
						$a->setFloor($m_floor);
						$a->setFlat($m_flat);
						
						$m_tutor->getPerson()->setAddress($a);
						$m_tutor->getPerson()->save(Propel::getConnection());	
					}	
				}else{
				
					$data['info']= "El tutor con ".$i_identification_type->getStringFor($m_identification_type) . " " . $m_identification_number ;
				}
						
				$st = StudentTutorPeer::retrieveByStudentAndTutor($student,$m_tutor);
				if(is_null($st)){
					 //datos de tutor(madre) 
					 $student_tutor = new StudentTutor();
					 $student_tutor->setStudent($student);
					 $student_tutor->setTutor($m_tutor);
					 $student_tutor->save(Propel::getConnection()); 
					 $m_tutor->addStudentTutor($student_tutor);
					 $m_tutor->save(Propel::getConnection());
				}
				
			 
			}
			
			
			//chequeo campos obligatorios
			if( ! is_null($p_identification_type) && ! is_null($p_identification_number)  && ! is_null($p_lastname) &&  trim($p_lastname) != "" && ! is_null($p_firstname) && trim($p_firstname) != "")
			{
				//busco si ya existe.
				$tutor = TutorPeer::findByDocumentTypeAndNumber($p_identification_type,$p_identification_number);
				
				if(is_null($tutor))
				{
					//el tutor no existe. Lo creo
					$p_person = new Person();
					$p_person->setLastname($p_lastname);
					$p_person->setFirstname($p_firstname);
					$p_person->setIdentificationType($p_identification_type);
					$p_person->setIdentificationNumber($p_identification_number);
					$p_person->setSex(SexType::MALE);
					$p_person->setPhone($p_phone);
					$p_person->setEmail($p_email);
					$p_person->setBirthdate($p_birthdate);
					$p_person->setIsActive(true);
					$p_person->setBirthCity($p_birth_city);
					$p_person->save(Propel::getConnection());
					
					$tutor = new Tutor();
					$tutor->setPerson($p_person);
					$tutor->setOccupationId($p_occupation);
					$tutor->setOccupationCategoryId($p_occupation_category);
					$tutor->setStudyId($p_study);//coincide con la tabla sga_tipos_est_cur
					$tutor->setNationality($p_nationality);
					$tutor->save(Propel::getConnection());
					
					/* Recupero department, state ,country*/				
					if(!is_null($p_birth_city)){
						$p_city = CityPeer::retrieveByPk($p_birth_city);
						$tutor->getPerson()->setBirthCountry($p_city->getDepartment()->getState()->getCountry()->getId());
						$tutor->getPerson()->setBirthState($p_city->getDepartment()->getState()->getId());
						$tutor->getPerson()->setBirthDepartment($p_city->getDepartment()->getId());
					}
					//chequeo domicilio
					if( ! is_null($p_city) || ! is_null($p_street)  || ! is_null($p_number) || ! is_null($p_floor) || is_null($p_flat)){
						$a = new Address();
						$a->setCityId($p_birth_city);
						$a->setStreet($p_street);
						$a->setNumber($p_number);
						$a->setFloor($p_floor);
						$a->setFlat($p_flat);
						
						$tutor->getPerson()->setAddress($a);
						$tutor->getPerson()->save(Propel::getConnection());	
					}
					if(! is_null($data['info'])){
						$data['info']= $data['info']." ya existe en el sistema. Por favor actualice los datos.";
					
					}
				}else
				{	
					if(! is_null($data['info'])){
						$data['info']= "Los tutores con ". $i_identification_type->getStringFor($m_identification_type) . " " . $m_identification_number  ." y ".$i_identification_type->getStringFor($p_identification_type) . " " . $p_identification_number ." ya existen en el sistema. Por favor actualice los datos.";
					}else{
						$data['info']= "El tutor con ".$i_identification_type->getStringFor($p_identification_type) . " " . $p_identification_number ." ya existe en el sistema. Por favor actualice los datos.";	
					}
					
				}
					
				//datos de tutor(padre) 
				$st = StudentTutorPeer::retrieveByStudentAndTutor($student,$tutor);
				
				if(is_null($st)){
					 $student_tutor = new StudentTutor();
					 $student_tutor->setStudent($student);
					 $student_tutor->setTutor($tutor); 
					 
					 $student_tutor->save(Propel::getConnection());
					 $tutor->addStudentTutor($student_tutor);
					 $tutor->save(Propel::getConnection());
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
	  
	  $this->data=$data;
	  $this->getResponse()->setHttpHeader('Content-type','application/json');
	  $this->getResponse()->setContent(json_encode($data));
      $this->setLayout(false);
  
	}
        
        public function executeGetStudents(sfWebRequest $request)
        {
            $scsy = $this->getRoute()->getObject();
        }
 
}
