<?php

class ncClassFinder
{
  static protected
    $freshCache = false,
    $instance   = null;

  protected
    $overriden = array(),
    $classes   = array();

  protected function __construct()
  {
  }

  static public function getInstance()
  {
    if (!isset(self::$instance))
    {
      self::$instance = new ncClassFinder();
    }

    return self::$instance;
  }

  public function getPeerClasses()
  {
    return $this->classes;
  }

  public function setClassPath($class, $path)
  {
    $this->overriden[$class] = $path;

    $this->classes[$class] = $path;
  }

  public function getClassPath($class)
  {
    return isset($this->classes[$class]) ? $this->classes[$class] : null;
  }

  public function reloadClasses($force = false)
  {
    // only (re)load the autoloading cache once per request
    if (self::$freshCache)
    {
      return;
    }

    $configuration = sfProjectConfiguration::getActive();
    if (!$configuration || !$configuration instanceof sfApplicationConfiguration)
    {
      return;
    }

    self::$freshCache = true;
    if (file_exists($configuration->getConfigCache()->getCacheName('config/autoload.yml')))
    {
      self::$freshCache = false;
      if ($force)
      {
        unlink($configuration->getConfigCache()->getCacheName('config/autoload.yml'));
      }
    }

    $file = $configuration->getConfigCache()->checkConfig('config/autoload.yml');

    $this->classes = include($file);

    //If the user has specified provided one or more class paths
    foreach ($this->overriden as $class => $path)
    {
        $this->classes[$class] = $path;
    }

    //Remove non Peer classes from the array.
    foreach ($this->classes as $className => $path)
    {
      if ((substr($className, -4, 4) != 'Peer')
          || ((substr($className, -4, 4) == 'Peer') && (substr($className, 0, 4) == 'Base')))
      {
        unset($this->classes[$className]);
      }
    }
  }

  public function findClassName($tableName, $peerClass = null, $absolute = false)
  {
    $peerClass = is_null($peerClass)? $this->findPeerClassName($tableName) : $peerClass;
    if (!is_null($peerClass))
    {
      if (method_exists($peerClass, 'getOMClass'))
      {
        $className = explode(".", call_user_func(array($peerClass, 'getOMClass')));
        return is_array($className) && count($className) > 0? $className[count($className)-1] : null;
      }
    }
    return null;
  }

  public function findPeerClassName($tableName)
  {
    if (!$this->classes)
    {
      $this->reloadClasses();
    }

    foreach ($this->classes as $key => $class)
    {
      $tmp = explode(DIRECTORY_SEPARATOR, $key);
      $className = (is_array($tmp) && (count($tmp) > 0))
        ? $tmp[count($tmp)-1]
        : null;
      if (!is_null($className) && defined($className.'::TABLE_NAME'))
      {
        $classTableName = constant($className.'::TABLE_NAME');
        if (!is_null($classTableName) && $classTableName == $tableName)
        {
          return $className;
        }
      }
    }
    return null;
  }
}
