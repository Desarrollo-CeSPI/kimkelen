<?php

require_once(dirname(__FILE__).'/../dbInfo.php');

class sfPropelInsertSqlDiffTask extends sfPropelBaseTask
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
      
    $this->aliases = array('propel-insert-sql-diff');
    $this->namespace = 'propel';
    $this->name = 'insert-sql-diff';
    $this->briefDescription = 'Inserts SQL patch for the current model';

    $this->detailedDescription = <<<EOF
The [propel:insert-sql-diff|INFO] task will connect to database and execute diff.sql file, 
which contains difference beetween schema.yml and current database structure.
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
    
    $buildSql = new sfPropelBuildSqlDiffTask($this->dispatcher, $this->formatter);
    $buildSql->setCommandApplication($this->commandApplication);
    $buildSql->run(array(), $optionsCmdline);

    $filename = sfConfig::get('sf_data_dir')."/sql/{$options['connection']}.diff.sql";
    $this->logSection("sql-diff", "executing file $filename");
    $i = new dbInfo();
    $i->executeSql(file_get_contents($filename), Propel::getConnection($options['connection']));
  }
}

?>