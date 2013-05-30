<?php

class sfRevisitedModelGeneratorConfiguration extends sfModelGeneratorConfiguration
{
  protected function compile()
  {
    $config = $this->getConfig();

    // inheritance rules:
    // new|edit < form < default
    // list < default
    // filter < default
    $this->configuration = array(
      'list'   => array(
        'fields'         => array(),
        'layout'         => $this->getListLayout(),
        'title'          => $this->getListTitle(),
        'actions'        => $this->getListActions(),
        'object_actions' => $this->getListObjectActions(),
      ),
      'filter' => array(
        'fields'  => array(),
      ),
      'form'   => array(
        'fields'  => array(),
      ),
      'new'    => array(
        'fields'  => array(),
        'title'   => $this->getNewTitle(),
        'actions' => $this->getNewActions() ? $this->getNewActions() : $this->getFormActions(),
      ),
      'edit'   => array(
        'fields'  => array(),
        'title'   => $this->getEditTitle(),
        'actions' => $this->getEditActions() ? $this->getEditActions() : $this->getFormActions(),
      ),
      'show'   => array(
        'fields'  => array(),
        'title'   => $this->getShowTitle(),
        'actions' => $this->getShowActions(),
      ),
    );

    foreach (array_keys($config['default']) as $field)
    {
      $formConfig = array_merge($config['default'][$field], isset($config['form'][$field]) ? $config['form'][$field] : array());
      $this->configuration['list']['fields'][$field]   = new sfModelRevisitedGeneratorConfigurationField($field, array_merge(array('label' => sfInflector::humanize(sfInflector::underscore($field))), $config['default'][$field], isset($config['list'][$field]) ? $config['list'][$field] : array()));
      $this->configuration['filter']['fields'][$field] = new sfModelRevisitedGeneratorConfigurationField($field, array_merge($config['default'][$field], isset($config['filter'][$field]) ? $config['filter'][$field] : array()));
      $this->configuration['new']['fields'][$field]    = new sfModelRevisitedGeneratorConfigurationField($field, array_merge($formConfig, isset($config['new'][$field]) ? $config['new'][$field] : array()));
      $this->configuration['edit']['fields'][$field]   = new sfModelRevisitedGeneratorConfigurationField($field, array_merge($formConfig, isset($config['edit'][$field]) ? $config['edit'][$field] : array()));
      $this->configuration['show']['fields'][$field]   = new sfModelRevisitedGeneratorConfigurationField($field, array_merge($formConfig, isset($config['show'][$field]) ? $config['show'][$field] : array()));
    }

    // "virtual" fields for list
    foreach ($this->getListDisplay() as $field)
    {
      list($field, $flag) = sfModelRevisitedGeneratorConfigurationField::splitFieldWithFlag($field);

      $this->configuration['list']['fields'][$field] = new sfModelRevisitedGeneratorConfigurationField($field, array_merge(
        array('type' => 'Text', 'label' => sfInflector::humanize(sfInflector::underscore($field))),
        isset($config['default'][$field]) ? $config['default'][$field] : array(),
        isset($config['list'][$field]) ? $config['list'][$field] : array(),
        array('flag' => $flag)
      ));
    }

    // form actions
    foreach (array('edit', 'new', 'show') as $context)
    {
      foreach ($this->configuration[$context]['actions'] as $action => $parameters)
      {
        $this->configuration[$context]['actions'][$action] = $this->fixActionParameters($action, $parameters);
      }
    }

    // list actions
    foreach ($this->configuration['list']['actions'] as $action => $parameters)
    {
      $this->configuration['list']['actions'][$action] = $this->fixActionParameters($action, $parameters);
    }

    // list batch actions
    $this->configuration['list']['batch_actions'] = array();
    foreach ($this->getListBatchActions() as $action => $parameters)
    {
      $parameters = $this->fixActionParameters($action, $parameters);

      $action = 'batch'.ucfirst(0 === strpos($action, '_') ? substr($action, 1) : $action);

      $this->configuration['list']['batch_actions'][$action] = $parameters;
    }

    // list object actions
    foreach ($this->configuration['list']['object_actions'] as $action => $parameters)
    {
      $this->configuration['list']['object_actions'][$action] = $this->fixActionParameters($action, $parameters);
    }

    // list field configuration
    $this->configuration['list']['display'] = array();
    foreach ($this->getListDisplay() as $name)
    {
      list($name, $flag) = sfModelRevisitedGeneratorConfigurationField::splitFieldWithFlag($name);
      if (!isset($this->configuration['list']['fields'][$name]))
      {
        throw new InvalidArgumentException(sprintf('The field "%s" does not exist.', $name));
      }
      $field = $this->configuration['list']['fields'][$name];
      $field->setFlag($flag);
      $this->configuration['list']['display'][$name] = $field;
    }

    // list params configuration
    $this->configuration['list']['params'] = $this->getListParams();
    preg_match_all('/%%([^%]+)%%/', $this->getListParams(), $matches, PREG_PATTERN_ORDER);
    foreach ($matches[1] as $name)
    {
      list($name, $flag) = sfModelRevisitedGeneratorConfigurationField::splitFieldWithFlag($name);
      if (!isset($this->configuration['list']['fields'][$name]))
      {
        $this->configuration['list']['fields'][$name] = new sfModelRevisitedGeneratorConfigurationField($name, array_merge(
          array('type' => 'Text', 'label' => sfInflector::humanize(sfInflector::underscore($name))),
          isset($config['default'][$name]) ? $config['default'][$name] : array(),
          isset($config['list'][$name]) ? $config['list'][$name] : array(),
          array('flag' => $flag)
        ));
      }
      else
      {
        $this->configuration['list']['fields'][$name]->setFlag($flag);
      }

      $this->configuration['list']['params'] = str_replace('%%'.$flag.$name.'%%', '%%'.$name.'%%', $this->configuration['list']['params']);
    }
    // "virtual" fields for show
    $show_display_temp = $this->getShowDisplay();
    if(isset($show_display_temp[0])){
      $show_display['NONE'] = $show_display_temp;
    }
    else
      $show_display = $show_display_temp;
    
    foreach ($show_display as $category => $fields)
    {
      foreach($fields as $field)
      {
        list($field, $flag) = sfModelRevisitedGeneratorConfigurationField::splitFieldWithFlag($field);
        
        $this->configuration['show']['fields'][$category][$field] = new sfModelRevisitedGeneratorConfigurationField($field, array_merge(
          array('type' => 'Text', 'label' => sfInflector::humanize(sfInflector::underscore($field))),
          isset($config['default'][$field]) ? $config['default'][$field] : array(),
          isset($config['show'][$field]) ? $config['show'][$field] : array(),
          array('flag' => $flag)
        ));
      }
    }
    
    // show field configuration
    $show_display_temp = $this->getShowDisplay();
    if(isset($show_display_temp[0])){
      $show_display['NONE'] = $show_display_temp;
    }
    else
      $show_display = $show_display_temp;
      
    $this->configuration['show']['display'] = array();
    
    foreach ($show_display as $category => $fields)
    {
      foreach($fields as $field)
      {
        list($field, $flag) = sfModelRevisitedGeneratorConfigurationField::splitFieldWithFlag($field);
        if (!isset($this->configuration['show']['fields'][$category][$field]))
        {
          throw new InvalidArgumentException(sprintf('The field "%s" does not exist.', $field));
        }
        $field_temp = $this->configuration['show']['fields'][$category][$field];
        $field_temp->setFlag($flag);
        $this->configuration['show']['display'][$category][$field] = $field_temp;
      }
    }
    
    // action credentials
    $this->configuration['credentials'] = array(
      'list'   => array(),
      'new'    => array(),
      'create' => array(),
      'edit'   => array(),
      'update' => array(),
      'delete' => array(),
      'show'   => array(),
    );
    foreach ($this->getActionsDefault() as $action => $params)
    {
      if (0 === strpos($action, '_'))
      {
        $action = substr($action, 1);
      }

      $this->configuration['credentials'][$action] = isset($params['credentials']) ? $params['credentials'] : array();
      $this->configuration['credentials']['batch'.ucfirst($action)] = isset($params['credentials']) ? $params['credentials'] : array();
    }
    $this->configuration['credentials']['create'] = $this->configuration['credentials']['new'];
    $this->configuration['credentials']['update'] = $this->configuration['credentials']['edit'];
  }

  protected function getConfig()
  {
    return array(
      'default' => $this->getFieldsDefault(),
      'list'    => $this->getFieldsList(),
      'filter'  => $this->getFieldsFilter(),
      'form'    => $this->getFieldsForm(),
      'new'     => $this->getFieldsNew(),
      'edit'    => $this->getFieldsEdit(),
      'show'    => $this->getFieldsShow(),
    );
  }

 public function getFormFields(sfForm $form, $context)
 {
    $config = $this->getConfig();

    $overloaded=false;
    $specific_method = sprintf('get%sDisplay', ucfirst($context));
    $general_method = 'getFormFieldsDisplay';
    
    $method = method_exists($form, $specific_method)?$specific_method:(method_exists($form, $general_method)?$general_method:null);
    if ( !is_null($method))
    { 
      $fieldsets= $form->$method();
      $overloaded = true;
    }
    elseif (!$fieldsets = $this->$specific_method())
    {
      $fieldsets = $this->getFormDisplay();
      
    }

    if ( ($fieldsets && !$overloaded)|| ( isset($fieldsets)&&$overloaded) )
    { 
      $fields = array();

      // with fieldsets?
      if (!is_array(reset($fieldsets)))
      {
        $fieldsets = array('NONE' => $fieldsets);
      }

      foreach ($fieldsets as $fieldset => $names)
      {
        if (!$names)
        {
          continue;
        }

        $fields[$fieldset] = array();

        foreach ($names as $name)
        {
          list($name, $flag) = sfModelGeneratorConfigurationField::splitFieldWithFlag($name);
          if (!isset($this->configuration[$context]['fields'][$name]))
          {
            $this->configuration[$context]['fields'][$name] = new sfModelGeneratorConfigurationField($name, array_merge(
              isset($config['default'][$name]) ? $config['default'][$name] : array(),
              isset($config['form'][$name]) ? $config['form'][$name] : array(),
              isset($config[$context][$name]) ? $config[$context][$name] : array(),
              array('is_real' => false, 'type' => 'Text', 'flag' => $flag)
            ));
          }

          $field = $this->configuration[$context]['fields'][$name];
          $field->setFlag($flag);
          $fields[$fieldset][$name] = $field;
        }
      }

      return $fields;
    }

    $fields = array();
    foreach ($form->getWidgetSchema()->getPositions() as $name)
    {
      $fields[$name] = new sfModelGeneratorConfigurationField($name, array_merge(
        array('type' => 'Text'),
        isset($config['default'][$name]) ? $config['default'][$name] : array(),
        isset($config['form'][$name]) ? $config['form'][$name] : array(),
        isset($config[$context][$name]) ? $config[$context][$name] : array(),
        array('is_real' => false)
      ));
    }

    return array('NONE' => $fields);
  }


  
}
