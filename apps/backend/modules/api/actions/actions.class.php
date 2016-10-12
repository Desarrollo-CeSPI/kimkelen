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
  
  public function executeConfirmStudent(sfWebRequest $request)
  { 
	 //tomo las intancias de las librerias.
	 $i_identification_type =  BaseCustomOptionsHolder::getInstance('IdentificationType');
	 $i_sex_type = BaseCustomOptionsHolder::getInstance('SexType');
	 
	 $s_lastname = $this->getRequestParameter('apellido');// Es obligatorio
	 $s_firstname = $this->getRequestParameter('nombres');// Es obligatorio
	 $s_identification_type = $i_identification_type->getIdentificationType($this->getRequestParameter('tipo_documento_id'));
	 $s_identification_number =$this->getRequestParameter('nro_documento');
	 $s_sex = $i_sex_type->getSexType($this->getRequestParameter('sexo')); //Es obligatorio
	 $s_phone =$this->getRequestParameter('telefono_fijo');
	 $s_birthdate =$this->getRequestParameter('fecha_nacimiento');
	 $s_birth_city =$this->getRequestParameter('ciudad_nacimiento_id');
	 $s_health_coverage_id = $this->getRequestParameter('ciudad_nacimiento_id');
	 
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
	 $m_occupation = $this->getRequestParameter('madre_ocupacion_id');
	 $m_study = $this->getRequestParameter('madre_estudios_id');
	 //$m_is_alive = $this->getRequestParameter('madre_vive');
	 $m_email = $this->getRequestParameter('madre_email');
	 $m_phone = $this->getRequestParameter('madre_telefono_celular');
	 $m_birthdate =$this->getRequestParameter('madre_fecha_nacimiento');
	 $m_birth_city =$this->getRequestParameter('madre_ciudad_nacimiento_id');
	
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
	 $p_occupation =$this->getRequestParameter('padre_ocupacion_id');
	 $p_study = $this->getRequestParameter('padre_estudios_id');
	 //$p_is_alive = $this->getRequestParameter('padre_vive');
	 $p_email = $this->getRequestParameter('padre_email');
	 $p_phone = $this->getRequestParameter('padre_telefono_celular');
	 $p_birthdate =$this->getRequestParameter('padre_fecha_nacimiento');
	 $p_birth_city =$this->getRequestParameter('padre_ciudad_nacimiento_id');
		
	 //domicilio
	 $p_city = $this->getRequestParameter('padre_domicilio_ciudad_id');
	 $p_street =$this->getRequestParameter('padre_domicilio_calle');
	 $p_number =$this->getRequestParameter('padre_domicilio_numero');
	 $p_floor = $this->getRequestParameter('padre_domicilio_piso');
	 $p_flat =$this->getRequestParameter('padre_domicilio_departamento');
	
	 //chequeo campos obligatorios
	 if(is_null($s_identification_type) || is_null($s_identification_number) || is_null($s_lastname) || trim($s_lastname) == "" || is_null($s_firstname) || trim($s_firstname) =="" || is_null($s_sex)){
		
		throw new Exception('Faltan datos del alumno');
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
				$s_person->setIdentificationType($s_identification_type);
				$s_person->setIdentificationNumber($s_identification_number);
				$s_person->setSex($s_sex);
				$s_person->setPhone($s_phone);
				$s_person->setBirthdate($s_birthdate);
				$s_person->setIsActive(true);
				$s_person->setBirthCity($s_birth_city);
				$s_person->save(Propel::getConnection());
				
				$student= new Student();
				$student->setPerson($s_person); 
				$student->setGlobalFileNumber('888888');//Nro de legajo??
				$student->setHealthCoverageId($request->getParameter('obra_social_id'));
				$student->setOriginSchoolId($request->getParameter('escuela_procedencia_numero'));
				$student->setHealthCoverageId($s_health_coverage_id);  
				$student->save(Propel::getConnection());
				
				$data = array('message' => "Los datos fueron almacenados correctamente.");
			
			}else{
				//seteo isActive	
				$student->getPerson()->setLastname($s_lastname);
				$student->getPerson()->setFirstname($s_firstname);
				$student->getPerson()->setSex($s_sex);
				$student->getPerson()->setPhone($s_phone);
				$student->getPerson()->setBirthdate($s_birthdate);
				$student->getPerson()->setBirthCity($s_birth_city);
				$student->getPerson()->setIsActive(true);
				$student->save(Propel::getConnection());
				
				$data = array('message' => "El alumno ya fue confirmado.");
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
			}
			
			
			
			//chequeo campos obligatorios
			if( ! is_null($m_identification_type) && ! is_null($m_identification_number) && ! is_null($m_lastname) &&  trim($m_lastname) != "" && ! is_null($m_firstname) && trim($m_firstname) != "")
			{
				//busco si ya existe.
				$tutor = TutorPeer::findByDocumentTypeAndNumber($m_identification_type,$m_idenfication_number);
				
				if(is_null($tutor))
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
					
					$tutor = new Tutor();
					$tutor->setPerson($m_person);
					$tutor->setOccupationId($m_occupation); //coincide con la tabla sga_act_economica, pero hay otra categ_ocup; ver cual de las dos se usa.
					$tutor->setStudyId($m_study);//coincide con la tabla sga_tipos_est_cur
					$tutor->save(Propel::getConnection());		
					//$tutor->setIsAlive(true);
						
				}
				else
				{
					$tutor->getPerson()->setLastname($m_lastname);
					$tutor->getPerson()->setFirstname($m_firstname);
					$tutor->getPerson()->setSex($m_sex);
					$tutor->getPerson()->setPhone($m_phone);
					$tutor->getPerson()->setBirthdate($m_birthdate);
					$tutor->getPerson()->setBirthCity($m_birth_city);
					$tutor->getPerson()->setIsActive(true);
					$tutor->setOccupationId($m_occupation); //coincide con la tabla sga_act_economica, pero hay otra categ_ocup; ver cual de las dos se usa.
					$tutor->setStudyId($m_study);//coincide con la tabla sga_tipos_est_cur
					$tutor->save(Propel::getConnection());		
				
				}
				
				//chequeo domicilio
				if( ! is_null($m_city) || ! is_null($m_street)  || ! is_null($m_number) || ! is_null($m_floor) || is_null($m_flat)){
					$a = new Address();
					$a->setCityId($m_city);
					$a->setStreet($m_street);
					$a->setNumber($m_number);
					$a->setFloor($m_floor);
					$a->setFlat($m_flat);
					
					$tutor->getPerson()->setAddress($a);
					$tutor->getPerson()->save(Propel::getConnection());	
				}
				
				 //datos de tutor(madre) 
				 $student_tutor = new StudentTutor();
				 $student_tutor->setStudent($student);
				 $student_tutor->setTutor($tutor);
				 $student_tutor->save(Propel::getConnection()); 
				 $tutor->addStudentTutor($student_tutor);
				 $tutor->save(Propel::getConnection());
			 
			}
			
			
			
			//chequeo campos obligatorios
			if( ! is_null($p_identification_type) && ! is_null($p_identification_number)  && ! is_null($p_lastname) &&  trim($p_lastname) != "" && ! is_null($p_firstname) && trim($p_firstname) != "")
			{
				//busco si ya existe.
				$tutor = TutorPeer::findByDocumentTypeAndNumber($p_identification_type,$p_idenfication_number);
				
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
					$tutor->setOccupationId($p_occupation); //coincide con la tabla sga_act_economica, pero hay otra categ_ocup; ver cual de las dos se usa.
					$tutor->setStudyId($p_study);//coincide con la tabla sga_tipos_est_cur
					$tutor->save(Propel::getConnection());
					//$tutor->setIsAlive(true);
			
				}
				else
				{
					$tutor->getPerson()->setLastname($p_lastname);
					$tutor->getPerson()->setFirstname($p_firstname);
					$tutor->getPerson()->setSex(SexType::MALE);
					$tutor->getPerson()->setPhone($p_phone);
					$tutor->getPerson()->setBirthdate($p_birthdate);
					$tutor->getPerson()->setBirthCity($p_birth_city);
					$tutor->getPerson()->setIsActive(true);
					$tutor->setOccupationId($p_occupation); //coincide con la tabla sga_act_economica, pero hay otra categ_ocup; ver cual de las dos se usa.
					$tutor->setStudyId($p_study);//coincide con la tabla sga_tipos_est_cur
					$tutor->save(Propel::getConnection());		
					
				}
				//chequeo domicilio
				if( ! is_null($p_city) || ! is_null($p_street)  || ! is_null($p_number) || ! is_null($p_floor) || is_null($p_flat)){
					$a = new Address();
					$a->setCityId($p_city);
					$a->setStreet($p_street);
					$a->setNumber($p_number);
					$a->setFloor($p_floor);
					$a->setFlat($p_flat);
					
					$tutor->getPerson()->setAddress($a);
					$tutor->getPerson()->save(Propel::getConnection());	
				}
					
				 //datos de tutor(padre) 
				 $student_tutor = new StudentTutor();
				 $student_tutor->setStudent($student);
				 $student_tutor->setTutor($tutor); 
				 
				 $student_tutor->save(Propel::getConnection());
				 $tutor->addStudentTutor($student_tutor);
				 $tutor->save(Propel::getConnection());

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
 
}
