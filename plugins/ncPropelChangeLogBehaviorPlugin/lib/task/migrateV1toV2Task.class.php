<?php

class migrateV1toV2 extends sfBaseTask
{
  const MESSAGE_LONG = 80;

  protected function configure()
  {
    $this->addOptions(array(
      new sfCommandOption('go', null, sfCommandOption::PARAMETER_NONE, 'Enables writes to database'),
    ));

    $this->namespace        = 'ncPropelChangeLogBehavior';
    $this->name             = 'migrateV1toV2';
    $this->briefDescription = 'Migrates the database of version 0.1.x to the database 0.2.x';
    $this->detailedDescription = <<<EOF
Migrates the database of version 0.1.0 to the database 0.2.0.
This task does not destroy data, it only moves things around.
If there's a mix between old and new ncChangeLogEntry, this task only modify the old ones.
*** USE WITH EXTREME CAUTION ***

By default it won't save stuff. You have to use '--go' option if you want to save things.
EOF;
  }

  /**
   * This function accepts a string and echoes it with 
   * a fixed lenght. The string is completed with whitespaces
   * till the 'MESSAGE_LONG' constant is reached
   */
  protected function printFixedLenghtMessage($string)
  {
    $count = self::MESSAGE_LONG - strlen($string);
    for ($i=0;$i<$count;$i++)
    {
      $string .= " ";
    }
    if ($count < 0) $string .= " ";
    echo $string;
  }

  /**
   * Returns true if the details where
   * base64 encoded.
   */
  protected function checkEncoding($entry)
  {
    $mustDecode = false;

    if (!is_null($entry) > 0)
    {
      $detail64 = unserialize(base64_decode($entry->getChangesDetail()));
      if (is_array($detail64))
      {
        $mustDecode = true;
      }
    }
    return $mustDecode;
  }

  /**
   * Ask 'Are you sure' question
   */
  protected function makeBackupQuestion()
  {
    return $this->askConfirmation(array('This command may destroy data, please make a backup of your database.', 'Are you sure you want to proceed? (y/N)'), null, false);
  }

  /**
   * Tries to migrate one ncChangeLogEntry.
   */
  protected function migrateOne($e)
  {
    $className  = $e->getClassName();
    $primaryKey = $e->getObjectPk();
    $newDetail  = array('class' => $className, 'pk' => $primaryKey, 'raw' => array());
    $res        = false;
    $oldDetail  = $this->checkEncoding($e)? unserialize(base64_decode($e->getChangesDetail())) : unserialize($e->getChangesDetail());

    if ($e->isOperation(ncChangeLogEntryOperation::NC_CHANGE_LOG_ENTRY_OPERATION_UPDATE))
    {
      $newDetail['changes'] = array();
      if (is_array($oldDetail) && !isset($oldDetail['changes']))
      {
        foreach ($oldDetail as $key => $detail)
        {
          if (isset($detail['values']) && is_array($detail['values']))
          {
            $newDetail['changes'][$key] = array('old' => isset($detail['values']['old'])? $detail['values']['old'] : '',
                                                'new' => isset($detail['values']['new'])? $detail['values']['new'] : '',
                                                'field' => isset($detail['values']['field'])? $detail['values']['field'] : '');
            $newDetail['raw'] = (isset($detail['raw']))? $detail['raw'] : array();
          }
          if (isset($detail['raw']) && is_array($detail['raw']) && isset($detail['raw']['old']) && isset($detail['raw']['new']))
          {
            $newDetail['changes'][$key]['raw'] = array('old' => isset($detail['raw']['old'])? $detail['raw']['old'] : '',
                                                 'new' => isset($detail['raw']['new'])? $detail['raw']['new'] : '');
          }
        }
        $res = true;
      }
    }
    elseif (is_array($oldDetail) && isset($oldDetail['values']))
    {
      $res = true;
    }

    if ($res)
    {
      $e->setChangesDetail(base64_encode(serialize($newDetail)));
    }
    return $res;
  }

  /**
   * Register empty 'changelog' behaviors to avoid error message
   */
  protected function registerBehavior()
  {
    sfPropelBehavior::registerMethods('changelog', array());
    sfPropelBehavior::registerHooks('changelog', array());
  }

  protected function execute($arguments = array(), $options = array())
  {
    // initialize
    $databaseManager = new sfDatabaseManager($this->configuration);
    $connection = $databaseManager->getDatabase('propel')->getConnection();
    $this->registerBehavior();

    //Counters
    $migrated  = 0;
    $notNeeded = 0;
    $errors    = 0;
    if ($this->makeBackupQuestion())
    {
      echo "\n";
      //Query database
      $entries   = ncChangeLogEntryPeer::doSelect(new Criteria());
      foreach ($entries as $i => $e)
      {
        $this->printFixedLenghtMessage(str_replace(array('%%id%%', '%%class%%', '%%pk%%'),
                                                   array($e->getId(), $e->getClassName(), $e->getPrimaryKey()),
                                                   "Migratring entry '%%id%%' of class '%%class%%' with primary key '%%pk%%'..."));

        if ($this->migrateOne($e))
        {
          try
          {
            if ($options['go']) $e->save();
            $migrated++;
            echo "[   MIGRATED ]\n";
          }
          catch (Exception $e)
          {
            echo "[      ERROR ]\n\t\t".$e->getMessage()."\n";
            $errors++;
          }
        }
        else
        {
          $notNeeded++;
          echo   "[ NOT NEEDED ]\n";
        }
      }

      echo "\n\n*********************************
Migrated:                    $migrated
Errors:                      $errors
Do not need migration:       $notNeeded

Total:                       ".count($entries)."
*********************************\n\n";
      if (!$options['go'])
      {
        echo "\n*** DUMMY MODE ENABLED. To enable writes to database use the '--go' option. ***\n\n";
      }
    }
  }
}
