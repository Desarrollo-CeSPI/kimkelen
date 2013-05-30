<?php

class dcValidatorPropelUnique extends sfValidatorPropelUnique
{
  public function __construct($options = array(), $messages = array())
  {
    $this->addOption('criteria', new Criteria());
    $this->addOption('comparator', Criteria::EQUAL);
    parent::__construct($options, $messages);
  }

  protected function doClean($values)
  {
    if (!is_array($values))
    {
      throw new InvalidArgumentException('You must pass an array parameter to the clean() method (this validator can only be used as a post validator).');
    }

    if (!is_array($this->getOption('column')))
    {
      $this->setOption('column', array($this->getOption('column')));
    }

    if (!is_array($field = $this->getOption('field')))
    {
      $this->setOption('field', $field ? array($field) : array());
    }
    $fields = $this->getOption('field');

    $criteria = $this->getOption('criteria');
    foreach ($this->getOption('column') as $i => $column)
    {
      $name = isset($fields[$i]) ? $fields[$i] : $column;
      if (!array_key_exists($name, $values))
      {
        // one of the column has be removed from the form
        return $values;
      }

      $colName = call_user_func(array(constant($this->getOption('model').'::PEER'), 'translateFieldName'), $column, BasePeer::TYPE_FIELDNAME, BasePeer::TYPE_COLNAME);

      $criteria->add($colName, $values[$name], $this->getOption('comparator'));
    }

    $object = call_user_func(array(constant($this->getOption('model').'::PEER'), 'doSelectOne'), $criteria, $this->getOption('connection'));

    // if no object or if we're updating the object, it's ok
    if (is_null($object) || $this->isUpdate($object, $values))
    {
      return $values;
    }

    $error = new sfValidatorError($this, 'invalid', array('column' => implode(', ', $this->getOption('column'))));

    if ($this->getOption('throw_global_error'))
    {
      throw $error;
    }

    $columns = $this->getOption('column');

    throw new sfValidatorErrorSchema($this, array($columns[0] => $error));
  }

  /**
   * Returns whether the object is being updated.
   *
   * @param BaseObject  $object   A Propel object
   * @param array       $values   An array of values
   *
   * @return Boolean     true if the object is being updated, false otherwise
   */
  protected function isUpdate(BaseObject $object, $values)
  {
    // check each primary key column
    foreach ($this->getPrimaryKeys() as $column)
    {
      $columnPhpName = call_user_func(array(constant($this->getOption('model').'::PEER'), 'translateFieldName'), $column, BasePeer::TYPE_FIELDNAME, BasePeer::TYPE_PHPNAME);
      $method = 'get'.$columnPhpName;
      if (!isset($values[$column]) or $object->$method() != $values[$column])
      {
        return false;
      }
    }

    return true;
  }

}
