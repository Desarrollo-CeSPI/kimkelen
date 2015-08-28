<?php

/**
 * Pathway form.
 *
 * @package    symfony
 * @subpackage form
 * @author     Your name here
 */
class PathwayForm extends BasePathwayForm
{
  public function configure()
  {
	  sfContext::getInstance()->getConfiguration()->loadHelpers(array('I18N'));

	  $this->validatorSchema->setPostValidator(new sfValidatorCallback(array("callback" => array($this, "validateSchoolYear"))));
  }

	public function validateSchoolYear($validator, $values)
  {
	  $criteria = new Criteria();
	  $criteria->add(PathwayPeer::SCHOOL_YEAR_ID, $values['school_year_id']);
	  if (PathwayPeer::doCount($criteria) != 0)
	  {
		  throw new sfValidatorError($validator, __("Can't create two pathways for the same school year"));
	  }

	  return $values;
  }
}