<?php

/**
 * ncChangeLogEntry form base class.
 *
 * @package    ##PROJECT_NAME##
 * @subpackage form
 * @author     ##AUTHOR_NAME##
 */
class BasencChangeLogEntryForm extends BaseFormPropel
{
  public function setup()
  {
    $this->setWidgets(array(
      'id'             => new sfWidgetFormInputHidden(),
      'class_name'     => new sfWidgetFormInput(),
      'object_pk'      => new sfWidgetFormInput(),
      'changes_detail' => new sfWidgetFormTextarea(),
      'username'       => new sfWidgetFormInput(),
      'operation_type' => new sfWidgetFormInput(),
      'created_at'     => new sfWidgetFormDateTime(),
    ));

    $this->setValidators(array(
      'id'             => new sfValidatorPropelChoice(array('model' => 'ncChangeLogEntry', 'column' => 'id', 'required' => false)),
      'class_name'     => new sfValidatorString(array('max_length' => 255)),
      'object_pk'      => new sfValidatorInteger(),
      'changes_detail' => new sfValidatorString(),
      'username'       => new sfValidatorString(array('max_length' => 255, 'required' => false)),
      'operation_type' => new sfValidatorInteger(),
      'created_at'     => new sfValidatorDateTime(array('required' => false)),
    ));

    $this->widgetSchema->setNameFormat('nc_change_log_entry[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    parent::setup();
  }

  public function getModelName()
  {
    return 'ncChangeLogEntry';
  }


}
