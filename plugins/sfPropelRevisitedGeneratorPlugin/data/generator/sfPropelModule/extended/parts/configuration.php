[?php

/**
 * <?php echo $this->getModuleName() ?> module configuration.
 *
 * @package    ##PROJECT_NAME##
 * @subpackage <?php echo $this->getModuleName()."\n" ?>
 * @author     ##AUTHOR_NAME##
 * @version    SVN: $Id: configuration.php 12831 2008-11-09 14:33:38Z fabien $
 */
class Base<?php echo ucfirst($this->getModuleName()) ?>GeneratorConfiguration extends sfRevisitedModelGeneratorConfiguration
{
<?php include dirname(__FILE__).'/actionsConfiguration.php' ?>

<?php include dirname(__FILE__).'/fieldsConfiguration.php' ?>

<?php include dirname(__FILE__).'/slotActionsConfiguration.php' ?>

<?php if (isset($this->config['exportation']['enabled']) && $this->config['exportation']['enabled']): ?>
  <?php include dirname(__FILE__).'/exporterConfiguration.php' ?>
  <?php include dirname(__FILE__).'/exporterPaginationConfiguration.php' ?>
<?php else: ?>
  <?php unset($this->config['exportation']) ?>
<?php endif ?>



  public function isExportationEnabled()
  {
    return <?php echo (isset($this->config['exportation']['enabled']) && $this->config['exportation']['enabled']? 'true' : 'false') ?>;
<?php unset($this->config['exportation']['enabled']) ?>
  }


  public function getForm($object = null)
  {
    $class = $this->getFormClass();

    return new $class($object, $this->getFormOptions());
  }

  /**
   * Gets the form class name.
   *
   * @return string The form class name
   */
  public function getFormClass()
  {
    return '<?php echo isset($this->config['form']['class']) ? $this->config['form']['class'] : $this->getModelClass().'Form' ?>';
<?php unset($this->config['form']['class']) ?>
  }

  public function getFormOptions()
  {
    return array();
  }

  public function hasFilterForm()
  {
    return <?php echo !isset($this->config['filter']['class']) || false !== $this->config['filter']['class'] ? 'true' : 'false' ?>;
  }

  /**
   * Gets the filter form class name
   *
   * @return string The filter form class name associated with this generator
   */
  public function getFilterFormClass()
  {
    return '<?php echo isset($this->config['filter']['class']) && !in_array($this->config['filter']['class'], array(null, true, false), true) ? $this->config['filter']['class'] : $this->getModelClass().'FormFilter' ?>';
<?php unset($this->config['filter']['class']) ?>
  }

<?php include dirname(__FILE__).'/filtersConfiguration.php' ?>

<?php include dirname(__FILE__).'/paginationConfiguration.php' ?>

<?php include dirname(__FILE__).'/sortingConfiguration.php' ?>

  public function getPeerMethod()
  {
    return '<?php echo isset($this->config['list']['peer_method']) ? $this->config['list']['peer_method'] : 'doSelect' ?>';
<?php unset($this->config['list']['peer_method']) ?>
  }

  public function getPeerCountMethod()
  {
    return '<?php echo isset($this->config['list']['peer_count_method']) ? $this->config['list']['peer_count_method'] : 'doCount' ?>';
<?php unset($this->config['list']['peer_count_method']) ?>
  }

  public function getConnection()
  {
    return null;
  }

  public function getCondition($action)
  {
    $condition = null;
    // first check standard main actions ("_create" for example)
    if (isset($this->configuration['list']['actions']['_'.$action]['condition']))
    {
      $condition = $this->configuration['list']['actions']['_'.$action]['condition'];
    }
    // then check non standard main actions
    if (isset($this->configuration['list']['actions'][$action]['condition']))
    {
      $condition = $this->configuration['list']['actions'][$action]['condition'];
    }
    // then check standard object actions (started with "_", "_edit" for example)
    if (isset($this->configuration['list']['object_actions']['_'.$action]['condition']))
    {
      $condition = $this->configuration['list']['object_actions']['_'.$action]['condition'];
    }
    // finally check non standard object actions
    if (isset($this->configuration['list']['object_actions'][$action]['condition']))
    {
      $condition = $this->configuration['list']['object_actions'][$action]['condition'];
    }

    return $condition;
  }
}
