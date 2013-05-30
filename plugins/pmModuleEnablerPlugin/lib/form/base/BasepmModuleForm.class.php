<?php

/**
 * pmModule form base class.
 *
 * @package    ##PROJECT_NAME##
 * @subpackage form
 * @author     ##AUTHOR_NAME##
 */
class BasepmModuleForm extends BaseFormPropel
{
  public function setup()
  {
    $this->setWidgets(array(
      'id'                  => new sfWidgetFormInputHidden(),
      'name'                => new sfWidgetFormInput(),
      'is_enabled'          => new sfWidgetFormInputCheckbox(),
      'pm_configuration_id' => new sfWidgetFormPropelChoice(array('model' => 'pmConfiguration', 'add_empty' => false)),
    ));

    $this->setValidators(array(
      'id'                  => new sfValidatorPropelChoice(array('model' => 'pmModule', 'column' => 'id', 'required' => false)),
      'name'                => new sfValidatorString(array('max_length' => 256)),
      'is_enabled'          => new sfValidatorBoolean(array('required' => false)),
      'pm_configuration_id' => new sfValidatorPropelChoice(array('model' => 'pmConfiguration', 'column' => 'id')),
    ));

    $this->validatorSchema->setPostValidator(
      new sfValidatorPropelUnique(array('model' => 'pmModule', 'column' => array('name')))
    );

    $this->widgetSchema->setNameFormat('pm_module[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    parent::setup();
  }

  public function getModelName()
  {
    return 'pmModule';
  }


}
