[?php

require_once(dirname(__FILE__).'/../lib/Base<?php echo ucfirst($this->moduleName) ?>GeneratorConfiguration.class.php');
require_once(dirname(__FILE__).'/../lib/Base<?php echo ucfirst($this->moduleName) ?>GeneratorHelper.class.php');
require_once(dirname(__FILE__).'/../lib/exporterHelper.php');
require_once(dirname(__FILE__).'/../lib/exporterHelperUser.php');
require_once(dirname(__FILE__).'/../lib/exporterXls.php');
require_once(dirname(__FILE__).'/../lib/exporterCsv.php');
require_once(dirname(__FILE__).'/../lib/exporterForm.php');


/**
 * <?php echo $this->getModuleName() ?> actions.
 *
 * @package    ##PROJECT_NAME##
 * @subpackage <?php echo $this->getModuleName()."\n" ?>
 * @author     ##AUTHOR_NAME##
 * @version    SVN: $Id: actions.class.php 12493 2008-10-31 14:43:26Z fabien $
 */
class <?php echo $this->getGeneratedModuleName() ?>Actions extends sfActions
{
  public function preExecute()
  {
    $this->configuration = new <?php echo $this->getModuleName() ?>GeneratorConfiguration();

    if (!$this->getUser()->hasCredential($this->configuration->getCredentials($this->getActionName())))
    {
      $this->forward(sfConfig::get('sf_secure_module'), sfConfig::get('sf_secure_action'));
    }

    if ($condition = $this->configuration->getCondition($this->getActionName()))
    {
      if ($this->getActionName() == 'new')
      {
        $this->forward404Unless($this->getUser()->$condition());
      }
      else
      {
        $this-><?php echo $this->getSingularName() ?> = $this->getRoute()->getObject();
        $this->forward404Unless($this-><?php echo $this->getSingularName() ?>->$condition());
      }
    }

    $this->dispatcher->notify(new sfEvent($this, 'admin.pre_execute', array('configuration' => $this->configuration)));

    $this->helper = new <?php echo $this->getModuleName() ?>GeneratorHelper();
  }

<?php include dirname(__FILE__).'/../../parts/indexAction.php' ?>

<?php //if ($this->configuration->hasFilterForm()): ?>
<?php include dirname(__FILE__).'/../../parts/filterAction.php' ?>
<?php //endif; ?>

<?php include dirname(__FILE__).'/../../parts/newAction.php' ?>

<?php include dirname(__FILE__).'/../../parts/createAction.php' ?>

<?php include dirname(__FILE__).'/../../parts/editAction.php' ?>

<?php include dirname(__FILE__).'/../../parts/showAction.php' ?>

<?php include dirname(__FILE__).'/../../parts/updateAction.php' ?>

<?php include dirname(__FILE__).'/../../parts/deleteAction.php' ?>

<?php if ($this->configuration->getValue('list.batch_actions')): ?>
<?php include dirname(__FILE__).'/../../parts/batchAction.php' ?>
<?php endif; ?>

<?php if ($this->configuration->getValue('list.batch_actions')): ?>
<?php include dirname(__FILE__).'/../../parts/allBatchAction.php' ?>
<?php endif; ?>

<?php include dirname(__FILE__).'/../../parts/processFormAction.php' ?>

<?php //if ($this->configuration->hasFilterForm()): ?>
<?php include dirname(__FILE__).'/../../parts/filtersAction.php' ?>
<?php //endif; ?>

<?php include dirname(__FILE__).'/../../parts/paginationAction.php' ?>

<?php include dirname(__FILE__).'/../../parts/sortingAction.php' ?>

<?php if ($this->configuration->isExportationEnabled()): ?>
  <?php include dirname(__FILE__).'/../../parts/exporterAction.php' ?>
  <?php include dirname(__FILE__).'/../../parts/exporterPaginationAction.php' ?>
<?php endif ?>


}
