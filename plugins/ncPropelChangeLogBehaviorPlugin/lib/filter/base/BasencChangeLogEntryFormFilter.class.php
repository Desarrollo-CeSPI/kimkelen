<?php

require_once(sfConfig::get('sf_lib_dir').'/filter/base/BaseFormFilterPropel.class.php');

/**
 * ncChangeLogEntry filter form base class.
 *
 * @package    ##PROJECT_NAME##
 * @subpackage filter
 * @author     ##AUTHOR_NAME##
 */
class BasencChangeLogEntryFormFilter extends BaseFormFilterPropel
{
  public function setup()
  {
    $this->setWidgets(array(
      'class_name'     => new sfWidgetFormFilterInput(),
      'object_pk'      => new sfWidgetFormFilterInput(),
      'changes_detail' => new sfWidgetFormFilterInput(),
      'username'       => new sfWidgetFormFilterInput(),
      'operation_type' => new sfWidgetFormFilterInput(),
      'created_at'     => new sfWidgetFormFilterDate(array('from_date' => new sfWidgetFormDate(), 'to_date' => new sfWidgetFormDate(), 'with_empty' => true)),
    ));

    $this->setValidators(array(
      'class_name'     => new sfValidatorPass(array('required' => false)),
      'object_pk'      => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
      'changes_detail' => new sfValidatorPass(array('required' => false)),
      'username'       => new sfValidatorPass(array('required' => false)),
      'operation_type' => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
      'created_at'     => new sfValidatorDateRange(array('required' => false, 'from_date' => new sfValidatorDate(array('required' => false)), 'to_date' => new sfValidatorDate(array('required' => false)))),
    ));

    $this->widgetSchema->setNameFormat('nc_change_log_entry_filters[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    parent::setup();
  }

  public function getModelName()
  {
    return 'ncChangeLogEntry';
  }

  public function getFields()
  {
    return array(
      'id'             => 'Number',
      'class_name'     => 'Text',
      'object_pk'      => 'Number',
      'changes_detail' => 'Text',
      'username'       => 'Text',
      'operation_type' => 'Number',
      'created_at'     => 'Date',
    );
  }
}
