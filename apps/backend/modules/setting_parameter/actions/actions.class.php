<?php

require_once dirname(__FILE__).'/../lib/setting_parameterGeneratorConfiguration.class.php';
require_once dirname(__FILE__).'/../lib/setting_parameterGeneratorHelper.class.php';

/**
 * setting_parameter actions.
 *
 * @package    symfony
 * @subpackage setting_parameter
 * @author     Your name here
 * @version    SVN: $Id: actions.class.php 12474 2008-10-31 10:41:27Z fabien $
 */
class setting_parameterActions extends autoSetting_parameterActions
{
    public function executeNew(sfWebRequest $request)
    {
        $this->redirect('@setting_parameter');
    }
    
    public function executeDelete(sfWebRequest $request)
    {
        $this->redirect('@setting_parameter');
    }
    
    public function executeShow(sfWebRequest $request)
    {
        $this->redirect('@setting_parameter');
    }
}
