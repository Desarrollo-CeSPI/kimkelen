<?php

/*
 * This file is part of the symfony package.
 * (c) Fabien Potencier <fabien.potencier@symfony-project.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * sfValidatorNumber validates a number (integer or float). It also converts the input value to a float.
 *
 * @package    symfony
 * @subpackage validator
 * @author     oweitman
 * @version    SVN: $Id$
 */
class sfValidatorI18nNumber extends sfValidatorNumber
{
  /**
   * Configures the current validator.
   *
   * Available options:
   *
   *  * culture: culture of the number
   *
   * @param array $options   An array of options
   * @param array $messages  An array of error messages
   *
   * @see sfValidatorBase
   * @see sfValidatorNumber
   *    */
  protected function configure($options = array(), $messages = array())
  {
    parent::configure($options,$messages);
    
    $this->addOption('culture',$this->_current_language());
    $this->addMessage('format', 'Input has a wrong format: %value%');
    
  }

  /**
   * @see sfValidatorBase
   */
  protected function doClean($value)
  {
    try 
    {
      $value = sfNumberFormatPlus::getNumber($value, $this->getOption('culture'));
    }
    catch (Exception $e)
    {
      //print_r($e);
      throw new sfValidatorError($this, 'format', array('value' => $value));
    }
    return parent::doClean($value);
  }

  function _current_language()
  {
    try 
    {
      return sfContext::getInstance()->getUser()->getCulture();
    }
    catch (Exception $e)
    {
      return sfCultureInfo::getInstance()->getName();
    }
  }
}