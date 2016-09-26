<?php

class LetterMarkAveragePeer extends BaseLetterMarkAveragePeer
{

	public static function getLetterMarkAverageByCourseSubjectStudent($course_subject_student)
  {
    
		for ($i = 1; $i <= $course_subject_student->getConfiguration()->getCourseMarks(); $i++) 
	  {
	    $marks[$i] = LetterMarkPeer::getLetterMarkByValue($course_subject_student->getMarkFor($i)->getMark());
	  }

  	$criteria = new Criteria();
  	
  	 
    if (array_key_exists(1, $marks) && ! is_null($marks[1])) {
  	  $criteria->add(LetterMarkAveragePeer::LETTER_MARK_1, $marks[1]->getId());
    } else {
      $criteria->add(LetterMarkAveragePeer::LETTER_MARK_1, null, Criteria::ISNULL);
    }

    if (array_key_exists(2, $marks) && !is_null($marks[2])) {
  	  $criteria->add(LetterMarkAveragePeer::LETTER_MARK_2, $marks[2]->getId());
    } else {
      $criteria->add(LetterMarkAveragePeer::LETTER_MARK_2, null, Criteria::ISNULL);
    }
  	   if (array_key_exists(3, $marks) && !is_null($marks[3])) {
      $criteria->add(LetterMarkAveragePeer::LETTER_MARK_3, $marks[3]->getId());
    } else {
      $criteria->add(LetterMarkAveragePeer::LETTER_MARK_3, null, Criteria::ISNULL);
    }

  	$result = LetterMarkAveragePeer::doSelectOne($criteria);
    
  	return $result;
  }

}



// probar con constant("self::MARK_$index”) //
