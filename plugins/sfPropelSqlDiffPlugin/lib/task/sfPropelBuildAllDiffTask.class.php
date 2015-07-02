<?php

require_once(dirname(__FILE__).'/../dbInfo.php');

class sfPropelBuildAllDiffTask extends sfPropelBaseTask
{
  /**
   * @see sfTask
   */
  protected function configure()
  {
    $this->addOptions(array(
      new sfCommandOption('application', null, sfCommandOption::PARAMETER_REQUIRED, 'The application name'),
      new sfCommandOption('env', null, sfCommandOption::PARAMETER_REQUIRED, 'The environment', 'dev'),
      new sfCommandOption('connection', null, sfCommandOption::PARAMETER_REQUIRED, 'The connection name', 'propel'),
    ));
          
    $this->aliases = array('propel-build-all-diff');
    $this->namespace = 'propel';
    $this->name = 'build-all-diff';
    $this->briefDescription = 'Generates Propel model, and updates database without losing data';

    $this->detailedDescription = <<<EOF
Generates Propel model, and updates database structure without losing data
 
The task is equivalent to:

  [./symfony propel:insert-sql-diff|INFO]
  [./symfony propel:build-model|INFO]
  [./symfony propel:build-forms|INFO]
  [./symfony propel:build-filters|INFO]
EOF;
  }

  /**
   * @see sfTask
   */
  protected function execute($arguments = array(), $options = array())
  {
    $optionsCmdline = array();
    if($options['application']) $optionsCmdline[] = '--application='.$options['application'];
    if($options['env']) $optionsCmdline[] = '--env='.$options['env'];
    if($options['connection']) $optionsCmdline[] = '--connection='.$options['connection'];
    
    $task = new sfPropelInsertSqlDiffTask($this->dispatcher, $this->formatter);
    $task->setCommandApplication($this->commandApplication);
    $task->run(array(), $optionsCmdline);

    $task = new sfPropelBuildModelTask($this->dispatcher, $this->formatter);
    $task->setCommandApplication($this->commandApplication);
    $task->run();
    
    $task = new sfPropelBuildFormsTask($this->dispatcher, $this->formatter);
    $task->setCommandApplication($this->commandApplication);
    $task->run();
    
    $task = new sfPropelBuildFiltersTask($this->dispatcher, $this->formatter);
    $task->setCommandApplication($this->commandApplication);
    $task->run();
  }
}

?>