<?php

class BbaMatriculateStudentsTask extends sfBaseTask
{
  protected function createContextInstance($application = 'backend', $enviroment = 'dev', $debug = true)
  {
    $configuration = ProjectConfiguration::getApplicationConfiguration($application, $enviroment, $debug);
    sfContext::createInstance($configuration);
    sfContext::switchTo($application);

    $this->context = sfContext::getInstance();
    //include(sfConfigCache::getInstance()->checkConfig(sfConfig::get('sf_app_config_dir_name').'/nc_flavor.yml'));
  }

  protected function configure()
  {

    $this->addOptions(array(
      new sfCommandOption('application', null, sfCommandOption::PARAMETER_REQUIRED, 'The application name'),
      new sfCommandOption('env', null, sfCommandOption::PARAMETER_REQUIRED, 'The environment', 'dev'),
      new sfCommandOption('connection', null, sfCommandOption::PARAMETER_REQUIRED, 'The connection name', 'propel'),
      // add your own options here
    ));

    $this->namespace = '';
    $this->name = 'BbaMatriculateStudents';
    $this->briefDescription = '';
    $this->detailedDescription = <<<EOF
The [BbaMatriculateStudents|INFO] task does things.
Call it with:

  [php symfony BbaMatriculateStudents|INFO]
EOF;
  }

  protected function execute($arguments = array(), $options = array())
  {
    // initialize the database connection
    $databaseManager = new sfDatabaseManager($this->configuration);
    $connection = $databaseManager->getDatabase($options['connection'] ? $options['connection'] : null)->getConnection();

    $this->createContextInstance();

    $username = 'desarrollo';

    $user = sfContext::getInstance()->getUser();
    $sf_user = sfGuardUserPeer::retrieveByUsername($username);

    $user->signin($sf_user, false);


    $new_career_school_year_2014 = CareerSchoolYearPeer::retrieveByCareerAndSchoolYear(CareerPeer::retrieveByPK(8), SchoolYearPeer::retrieveCurrent());

    $old_career_school_year_2013 = CareerSchoolYearPeer::retrieveByPk(21);
    $new_career_school_year_2013 = CareerSchoolYearPeer::retrieveByPk(22);
    $old_career_school_year_2014 = CareerSchoolYearPeer::retrieveByCareerAndSchoolYear(CareerPeer::retrieveByPK(4), SchoolYearPeer::retrieveCurrent());
    $last_year_school_year = SchoolYearPeer::retrieveLastYearSchoolYear(SchoolYearPeer::retrieveCurrent());


    // ---------------------------------------------------------------------------------------------- //
    // Alumnos que promueven 6to deben seguir en el plan viejo
    $this->log('1 -Alumnos que promueven 6to deben seguir en el plan viejo');
    $c = new Criteria();
    $c->add(StudentCareerSchoolYearPeer::CAREER_SCHOOL_YEAR_ID, $old_career_school_year_2013->getId());
    $c->add(StudentCareerSchoolYearPeer::IS_PROCESSED, true);
    $c->add(StudentCareerSchoolYearPeer::YEAR, 6);
    $c->add(StudentCareerSchoolYearPeer::STATUS, StudentCareerSchoolYearStatus::APPROVED);

    $students_to_old_career_school_years = StudentCareerSchoolYearPeer::doSelect($c);


    try
    {
      $connection->beginTransaction();

      foreach ($students_to_old_career_school_years as $socsy)
      {
        $shift = $socsy->getStudent()->getShiftForSchoolYear($last_year_school_year);

        if (!$socsy->getStudent()->getIsRegistered($old_career_school_year_2014->getSchoolYear()))
        {
          $socsy->getStudent()->registerToSchoolYear($old_career_school_year_2014->getSchoolYear(), $shift, $connection);
        }
      }
      $connection->commit();
    }
    catch (PropelException $e)
    {
      $connection->rollBack();
      throw $e;
    }


    // ---------------------------------------------------------------------------------------------- //
    // 2 - Resto de los alumnos que no son del CBFE van al plan nuevo en el año que les corresponda

    $this->log('2 - Resto de los alumnos que no son del CBFE van al plan nuevo en el año que les corresponda');

    try
    {
      $connection->beginTransaction();


      // con este criteria voy a excluir a los que aprueban 6to y deben ir a 7mo del plan viejo
      $c = new Criteria();
      $c->add(StudentCareerSchoolYearPeer::CAREER_SCHOOL_YEAR_ID, $old_career_school_year_2013->getId());
      $c->add(StudentCareerSchoolYearPeer::IS_PROCESSED, true);
      $c->add(StudentCareerSchoolYearPeer::YEAR, 6);
      $c->add(StudentCareerSchoolYearPeer::STATUS, StudentCareerSchoolYearStatus::APPROVED);
      $c->clearSelectColumns();
      $c->addSelectColumn(StudentCareerSchoolYearPeer::STUDENT_ID);
      $stmt = StudentCareerSchoolYearPeer::doSelectStmt($c);
      $students_to_old_career_school_years_ids = $stmt->fetchAll(PDO::FETCH_COLUMN);


      // con este criteria voy a excluir a los que son del CBFE
      $c = new Criteria();
      $c->add(StudentCareerSchoolYearPeer::CAREER_SCHOOL_YEAR_ID, 23);
      $c->add(StudentCareerSchoolYearPeer::IS_PROCESSED, true);

      $c->clearSelectColumns();
      $c->addSelectColumn(StudentCareerSchoolYearPeer::STUDENT_ID);
      $stmt = StudentCareerSchoolYearPeer::doSelectStmt($c);
      $student_cbfe_ids = $stmt->fetchAll(PDO::FETCH_COLUMN);


      // al total le saco $students_to_old_career_school_years_ids y los
      // student_cbfe_ids

      $c = new Criteria();
      //$c = StudentCareerSchoolYearPeer::retrieveLastYearStudentNotGraduatedCriteria($new_career_school_year_2014);
      $c->add(StudentCareerSchoolYearPeer::YEAR, 7, Criteria::NOT_EQUAL);
      $c->addJoin(StudentCareerSchoolYearPeer::STUDENT_ID, StudentPeer::ID, Criteria::INNER_JOIN);
      $c->add(StudentPeer::ID, array_merge($students_to_old_career_school_years_ids, $student_cbfe_ids), Criteria::NOT_IN);

       $students = StudentPeer::doSelectActive($c);

        foreach ($students as $student)
        {

          $shift = $student->getShiftForSchoolYear($last_year_school_year);

          if (!$student->getIsRegistered($new_career_school_year_2014->getSchoolYear()) && ($shift))
          {



            $slcsy = $student->getLastStudentCareerSchoolYear();
            $slcs = $student->getLastCareerStudent();




            if ($slcsy->getStatus() == StudentCareerSchoolYearStatus::APPROVED)
            {

              $start_year = $slcsy->getYear() + 1;
            }
            else
            {
              $start_year = $slcsy->getYear();
            }




            if ($slcs->getCareerId() != $new_career_school_year_2014->getCareerId())
            {

              $student->registerToCareer($new_career_school_year_2014->getCareer(), null, null, $start_year, $connection);

              $sys = new SchoolYearStudent();
              $sys->setStudentId($student->getId());
              $sys->setSchoolYearId($new_career_school_year_2014->getSchoolYear()->getId());
              $sys->setShift($shift);

              $sys->save($connection);
              $this->verify($student, $new_career_school_year_2014, $connection);
            }
            else
            {


              $sys = new SchoolYearStudent();
              $sys->setStudentId($student->getId());
              $sys->setSchoolYearId($new_career_school_year_2014->getSchoolYear()->getId());
              $sys->setShift($shift);

              $sys->save($connection);

              $this->verify($student, $new_career_school_year_2014, $connection);
            }


            if (!is_null($shift))
            {
              $shift->clearAllReferences(true);
            }

            $student->clearAllReferences(true);
            unset($student);
            unset($shift);
          }

          StudentPeer::clearInstancePool();
          unset($students);
        }

        $connection->commit();

    }
    catch (PropelException $e)
    {
      $connection->rollBack();
      throw $e;
    }
  }

  public function verify($student, $new_career_school_year_2014, $connection)
  {
    //este metodo verifica si se creo mas de una tupla studentcareerschoolyear para el alumno en este
    //año lectivo, borra la que no es de la career que se recibio como parametro. Es un método parche que
    //hice porque se disparaban los hooks y quedaban inscriptos 2 veces si no.



    $scsys = StudentCareerSchoolYearPeer::retrieveCareerSchoolYearForStudentAndYear($student, $new_career_school_year_2014->getSchoolYear());
    foreach ($scsys as $scsy)
    {
      if ($scsy->getCareerSchoolYear()->getCareerId() != $new_career_school_year_2014->getCareerId())
      {
        $scsy->delete($connection);
      }
    }
  }

}