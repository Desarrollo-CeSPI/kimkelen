<?php

require_once(sfConfig::get('sf_lib_dir').'/filter/base/BaseFormFilterPropel.class.php');

/**
 * pmModule filter form base class.
 *
 * @package    ##PROJECT_NAME##
 * @subpackage filter
 * @author     ##AUTHOR_NAME##
 */
class BasepmModuleFormFilter extends BaseFormFilterPropel
{
  public function setup()
  {
    $this->setWidgets(array(
      'name'                => new sfWidgetFormFilterInput(),
      'is_enabled'          => new sfWidgetFormChoice(array('choices' => array('' => 'yes or no', 1 => 'yes', 0 => 'no'))),
      'pm_configuration_id' => new sfWidgetFormPropelChoice(array('model' => 'pmConfiguration', 'add_empty' => true)),
    ));

    $this->setValidators(array(
      'name'                => new sfValidatorPass(array('required' => false)),
      'is_enabled'          => new sfValidatorChoice(array('required' => false, 'choices' => array('', 1, 0))),
      'pm_configuration_id' => new sfValidatorPropelChoice(array('required' => false, 'model' => 'pmConfiguration', 'column' => 'id')),
    ));

    $this->widgetSchema->setNameFormat('pm_module_filters[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    parent::setup();
  }

  public function getModelName()
  {
    return 'pmModule';
  }

  public function getFields()
  {
    return array(
      'id'                  => 'Number',
      'name'                => 'Text',
      'is_enabled'          => 'Boolean',
      'pm_configuration_id' => 'ForeignKey',
    );
  }
}
