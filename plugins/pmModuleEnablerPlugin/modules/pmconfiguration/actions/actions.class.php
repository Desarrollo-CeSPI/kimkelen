<?php

require_once dirname(__FILE__).'/../lib/pmconfigurationGeneratorConfiguration.class.php';
require_once dirname(__FILE__).'/../lib/pmconfigurationGeneratorHelper.class.php';

/**
 * pmconfiguration actions.
 *
 * @package    testing
 * @subpackage pmconfiguration
 * @author     Your name here
 * @version    SVN: $Id: actions.class.php 12474 2008-10-31 10:41:27Z fabien $
 */
class pmconfigurationActions extends autoPmconfigurationActions
{
  public function executeIndex(sfWebRequest $request)
  {
    $this->forward('pmconfiguration', 'edit');
  }

  public function executeNew(sfWebRequest $request)
  {
    $this->forward('pmconfiguration', 'edit');
  }

  public function executeEdit(sfWebRequest $request)
  {
    $this->pm_configuration = pmConfiguration::getInstance();
    $this->form = $this->configuration->getForm($this->pm_configuration);
  }

  public function executeShow(sfWebRequest $request)
  {
    $this->forward('pmconfiguration', 'edit');
  }
}
