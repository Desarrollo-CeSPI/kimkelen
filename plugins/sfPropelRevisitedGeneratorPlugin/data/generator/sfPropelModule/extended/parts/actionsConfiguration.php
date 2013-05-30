  public function getCredentials($action)
  {
    if (0 === strpos($action, '_'))
    {
      $action = substr($action, 1);
    }

    return isset($this->configuration['credentials'][$action]) ? $this->configuration['credentials'][$action] : array();
  }

  public function getActionsDefault()
  {
    return <?php echo $this->asPhp(isset($this->config['actions']) ? $this->config['actions'] : array()) ?>;
<?php unset($this->config['actions']) ?>
  }

  public function getFormActions()
  {
    return <?php echo $this->asPhp(isset($this->config['form']['actions']) ? $this->config['form']['actions'] : array('_delete' => null, '_list' => null, '_save' => null, '_save_and_add' => null)) ?>;
<?php unset($this->config['form']['actions']) ?>
  }

  public function getShowActions()
  {
    return <?php echo $this->asPhp(isset($this->config['show']['actions']) ? $this->config['show']['actions'] : array('_delete' => null, '_list' => null)) ?>;
<?php unset($this->config['show']['actions']) ?>
  }

  public function getNewActions()
  {
    return <?php echo $this->asPhp(isset($this->config['new']['actions']) ? $this->config['new']['actions'] : array()) ?>;
<?php unset($this->config['new']['actions']) ?>
  }

  public function getEditActions()
  {
    return <?php echo $this->asPhp(isset($this->config['edit']['actions']) ? $this->config['edit']['actions'] : array()) ?>;
<?php unset($this->config['edit']['actions']) ?>
  }

  public function getListObjectActions()
  {
    return <?php echo $this->asPhp(isset($this->config['list']['object_actions']) ? $this->config['list']['object_actions'] : array('_edit' => null, '_delete' => null, '_show' => null)) ?>;
<?php unset($this->config['list']['object_actions']) ?>
  }

  public function getListActions()
  {
    return <?php echo $this->asPhp(isset($this->config['list']['actions']) ? $this->config['list']['actions'] : array('_new' => null)) ?>;
<?php unset($this->config['list']['actions']) ?>
  }

  public function getListBatchActions()
  {
    return <?php echo $this->asPhp(isset($this->config['list']['batch_actions']) ? $this->config['list']['batch_actions'] : array('_delete' => null)) ?>;
<?php unset($this->config['list']['batch_actions']) ?>
  }

  public function getSortColumnNameForField($field, $modelClass)
  {
    if ( array_key_exists($field, $list_fields = array_merge($this->getFieldsList(),$this->getFieldsDefault())) )
    { 
      if (array_key_exists('column_name',$list_fields[$field]) )
      {
        return constant($list_fields[$field]['column_name']);
      }
    }
    // camelize lower case to be able to compare with BasePeer::TYPE_PHPNAME translate field name
    $peer = constant($modelClass.'::PEER');
    
    //return $peer::translateFieldName(sfInflector::camelize(strtolower($field)), BasePeer::TYPE_PHPNAME, BasePeer::TYPE_COLNAME);
    return call_user_func(array($peer, 'translateFieldName'), sfInflector::camelize(strtolower($field)), BasePeer::TYPE_PHPNAME, BasePeer::TYPE_COLNAME);
  }
