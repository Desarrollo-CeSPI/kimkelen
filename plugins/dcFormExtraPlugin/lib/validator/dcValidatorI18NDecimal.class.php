<?php

/**
 * dcValidatorI18NDecimal
 *
 * For use with dcWidgetFormMeioMaskedInput with a 'decimal' mask.
 *
 * @author ncuesta
 */
class dcValidatorI18NDecimal extends sfValidatorNumber
{
  protected function doClean($value)
  {
    if (preg_match_all('/[\.,]/', $value, $matches) > 1)
    {
      throw new sfValidatorError($this, 'Only one decimal separator is allowed');
    }

    // Translate to US-standard
    $pre_clean = preg_replace('/,/', '.',$value);

    $post_clean = parent::doClean($pre_clean);

    return $post_clean;
  }
}
