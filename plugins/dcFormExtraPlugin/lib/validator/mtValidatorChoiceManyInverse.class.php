<?php

/**
 * Checks that the value is not in the 'choices' array.
 * Saves query time to the database when dealing with a large number of choices.
 *
 * Kind of dangerous since it allows almost anything.
 */
class mtValidatorChoiceManyInverse extends sfValidatorChoiceMany
{
  protected function doClean($value)
  {
    $choices = $this->getOption('choices');
    if ($choices instanceof sfCallable)
    {
      $choices = $choices->call();
    }

    if ($this->getOption('multiple'))
    {
      if (!is_array($value))
      {
        $value = array($value);
      }

      foreach ($value as $v)
      {
        if (sfValidatorChoice::inChoices($v, $choices))
        {
          throw new sfValidatorError($this, 'invalid', array('value' => $v));
        }
      }
    }
    else
    {
      if (sfValidatorChoice::inChoices($value, $choices))
      {
        throw new sfValidatorError($this, 'invalid', array('value' => $value));
      }
    }
    return $value;
  }
}
