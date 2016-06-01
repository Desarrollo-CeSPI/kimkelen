<?php

class StudentReserveStatusRecordPeer extends BaseStudentReserveStatusRecordPeer
{
	public static function retrieveByStudentId($student_id)
	{
		$c = new Criteria();
		$c->add(StudentReserveStatusRecordPeer::STUDENT_ID, $student_id);
		$c->add(StudentReserveStatusRecordPeer::END_DATE, null,Criteria::ISNULL);

		return StudentReserveStatusRecordPeer::doSelectOne($c);
		
	}
}
