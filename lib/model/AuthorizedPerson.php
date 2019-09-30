<?php

class AuthorizedPerson extends BaseAuthorizedPerson
{
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
}

