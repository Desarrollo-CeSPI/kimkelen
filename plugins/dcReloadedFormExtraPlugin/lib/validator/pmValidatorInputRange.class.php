<?php

/**
 * sfValidatorInputRange validates a range of inputs.
 *
 * @package    symfony
 * @subpackage validator
 * @author     Patricio Mac Adden <pmacadden@cespi.unlp.edu.ar>
 */
class pmValidatorInputRange extends sfValidatorBase
{
  /**
   * Configures the current validator.
   *
   * Available options:
   *
   *  * from_validator:   The from date validator (required)
   *  * to_validator:     The to date validator (required)
   *  * from_field:       The name of the "from" date field (optional, default: from)
   *  * to_field:         The name of the "to" date field (optional, default: to)
   *
   * @param array $options    An array of options
   * @param array $messages   An array of error messages
   *
   * @see sfValidatorBase
   */
  protected function configure($options = array(), $messages = array())
  {
    $this->setMessage('invalid', 'The range is not valid.');

    $this->addRequiredOption('from_validator');
    $this->addRequiredOption('to_validator');
    $this->addOption('from_field', 'from');
    $this->addOption('to_field', 'to');
  }

  /**
   * @see sfValidatorBase
   */
  protected function doClean($value)
  {
    $fromField = $this->getOption('from_field');
    $toField   = $this->getOption('to_field');

    $value[$fromField] = $this->getOption('from_validator')->clean(isset($value[$fromField]) ? $value[$fromField] : null);
    $value[$toField]   = $this->getOption('to_validator')->clean(isset($value[$toField]) ? $value[$toField] : null);

    if ($value[$fromField] && $value[$toField])
    {
      $v = new sfValidatorSchemaCompare($fromField, sfValidatorSchemaCompare::LESS_THAN_EQUAL, $toField, array('throw_global_error' => true), array('invalid' => $this->getMessage('invalid')));
      $v->clean($value);
    }

    return $value;
  }
}
