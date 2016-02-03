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
  	$criteria->add(LetterMarkAveragePeer::LETTER_MARK_1, $marks[1]->getId());

    if (array_key_exists($i, $marks)) {
  	  $criteria->add(LetterMarkAveragePeer::LETTER_MARK_2, $marks[2]->getId());
    } else {
      $criteria->add(LetterMarkAveragePeer::LETTER_MARK_2, null, Criteria::ISNULL);
    }
  	   if (array_key_exists($i, $marks)) {
      $criteria->add(LetterMarkAveragePeer::LETTER_MARK_3, $marks[2]->getId());
    } else {
      $criteria->add(LetterMarkAveragePeer::LETTER_MARK_3, null, Criteria::ISNULL);
    }
  	$result = LetterMarkAveragePeer::doSelectOne($criteria);

  	return $result;
  }

}
