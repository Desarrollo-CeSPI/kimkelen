<?php

/**
 * dcValidatorI18NInteger
 *
 * For use with dcWidgetFormMeioMaskedInput with a 'integer' mask.
 *
 * @author ncuesta
 */
class dcValidatorI18NInteger extends sfValidatorInteger
{
  protected function doClean($value)
  {
    // Translate to US-standard
    $pre_clean = preg_replace('/\./', '', $value);

    return parent::doClean($pre_clean);
  }
}