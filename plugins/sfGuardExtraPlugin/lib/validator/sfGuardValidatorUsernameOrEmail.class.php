<?php

/*
 * This file is part of the symfony package.
 * (c) Fabien Potencier <fabien.potencier@symfony-project.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 *
 * @package    symfony
 * @subpackage plugin
 * @author     Gordon Franke <gfranke@savedcite.com>
 * @version    SVN: $Id: sfGuardValidatorUsernameOrEmail.class.php 30765 2010-08-26 13:10:39Z garak $
 */
class sfGuardValidatorUsernameOrEmail extends sfValidatorBase
{
  public function configure($options = array(), $messages = array())
  {
  }

  protected function doClean($value)
  {
    $clean = (string) $value;

    // user exists?
    if (!is_null(sfGuardUserPeer::retrieveByUsernameOrEmail($clean)))
    {
    	return $value;
    }

    throw new sfValidatorError($this, 'invalid', array('value' => $value));
  }
}
