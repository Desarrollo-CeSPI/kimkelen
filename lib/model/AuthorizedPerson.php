<?php

class AuthorizedPerson extends BaseAuthorizedPerson
{
    
  public function __toString(){
    return $this->getPersonLastname().' '.$this->getPersonFirstname();
  }
  
  public function getPersonLastname()
  {
      return $this->getPerson()->getLastname();
  }

  public function getPersonFirstname()
  {
      return $this->getPerson()->getFirstname();
  }
  
  public function getPersonFullname()
  {
      return $this->getPerson()->getFullName();
  }
  
  public function getPersonFullIdentification()
  {
      return $this->getPerson()->getFullIdentification();
  }
  
  public function getPersonPhone()
  {
      return $this->getPerson()->getPhone();
  }
  
  public function getPersonAlternativePhone()
  {
      return $this->getPerson()->getAlternativePhone();
  }

  public function deleteStudents($con=null)
  {
    if (is_null($con))
      $con = Propel::getConnection();
    $con->beginTransaction();
    try
    {
      foreach ($this->getStudentAuthorizedPersons() as $student_ap)
      {
        $student_ap->delete($con);
      }
      $con->commit();
    }
    catch (Exception $e)
    {
      $con->rollBack();
    }
  }
  
   public function getStudentAuthorizedPersonsString()
  { 
    $students = array();
    foreach ($this->getStudentAuthorizedPersons() as $sap)
    {
      $students[] = $sap->getStudent();
    }

    return implode(',  ', $students);
  }
}

