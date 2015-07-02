<?php

class gmExporterForm extends sfForm
{
  protected
    $criteria = null,
    $configuration = null,
    $pager    = null;

  public function __construct($defaults = array(), $options = array(), $CSRFSecret = null)
  {
    $this->configuration = $options['configuration'];
    $this->pager         = $options['pager'];
  
    parent::__construct($defaults, $options, $CSRFSecret);
  }

  public function bind(array $taintedValues = null, array $taintedFiles = null)
  {
    parent::bind($taintedValues,$taintedFiles);

    if ($this->isValid())
    {
      $this->pager->setMaxPerPage($this->configuration->getExportationPagerMaxPerPage($this->getExportationType()));
    }
  }

  public function getExportationCriteria()
  {
    return $this->pager->getCriteria();
  }

  public function getExportationType()
  {
    if ($this->isBound())
    {
      return $this->getValue('type');
    }
    else
    {
      return $this->getDefault('type');
    }
  }

  public function getExportationResults()
  {
    return $this->pager->getResults();
  }

  public function getExportationPager()
  {
    return $this->pager;
  }

  protected function getContext()
  {
    return is_null($this->getOption('context'))? sfContext::getInstance() : $this->getOption('context');
  }

  protected function getFields()
  {
    return $this->getOption('fields', array());
  }

  public function configure()
  {
    parent::configure();


    if ($this->getOption('allowUserTypeSelection'))
    {
      $this->setWidget('type', new sfWidgetFormChoice(array('choices' => gmExporterTypes::getChoices(true, $this->getContext()))));
      $this->setValidator('type', new sfValidatorChoice(array('choices' => gmExporterTypes::getTypes(), 'required' => true)));
    }
    else
    {
      $this->setWidget('type', new sfWidgetFormInputHidden());
      $this->setValidator('type', new sfValidatorString(array('required' => true)));
    }

    $this->setWidget('title', new sfWidgetFormInput());
    $this->setValidator('title', new sfValidatorString(array('required' => true)));

    foreach ($this->getFields() as $fieldDecorator)
    {
      $this->setWidget($fieldDecorator->getId(), new sfWidgetFormInputCheckbox());
      $this->setValidator($fieldDecorator->getId(), new sfValidatorBoolean());
      $this->getWidgetSchema()->setLabel($fieldDecorator->getId(), $fieldDecorator->getLabel());
      $this->setDefault($fieldDecorator->getId(), true);
    }

    $this->getWidgetSchema()->setNameFormat('exportation[%s]');

    $this->setDefault('title', $this->translate($this->getOption('title')));
    $this->setDefault('type', $this->getOption('type'));
  }

  protected function translate($text)
  {
    $this->getContext()->getConfiguration()->loadHelpers('I18N');

    return __($text);
  }

  public function getExportationFieldSelectionDecorators()
  {
    if ($this->isBound())
    {
      $selectedFields = array();
      foreach ($this->getFields() as $fieldDecorator)
      {
        if (isset($this->values[$fieldDecorator->getId()]) && $this->values[$fieldDecorator->getId()])
        {
          $selectedFields[] = $fieldDecorator;
        }
      }
      return $selectedFields;
    }
    else
    {
      throw new Exception('The form is not bounded');
    }
  }

  public function getExportationTitle()
  {
    return $this->values['title'];
  }
}
