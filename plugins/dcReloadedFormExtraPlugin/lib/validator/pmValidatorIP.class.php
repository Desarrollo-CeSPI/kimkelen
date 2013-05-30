<?php

/**
 * pmValidatorIP validates IPs.
 *
 * @author Patricio Mac Adden <pmacadden@cespi.unlp.edu.ar>
 */
class pmValidatorIP extends sfValidatorRegex
{
  const REGEX_IP = '/^(?:25[0-5]|2[0-4]\d|1\d\d|[1-9]\d|\d)(?:[.](?:25[0-5]|2[0-4]\d|1\d\d|[1-9]\d|\d)){3}$/';

  /**
   * @see sfValidatorRegex
   */
  protected function configure($options = array(), $messages = array())
  {
    parent::configure($options, $messages);

    $this->setOption('pattern', self::REGEX_IP);
  }
}
