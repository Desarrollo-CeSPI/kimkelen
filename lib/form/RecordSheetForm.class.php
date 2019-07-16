<?php

/**
 * RecordSheet form.
 *
 * @package    symfony
 * @subpackage form
 * @author     Your name here
 */
class RecordSheetForm extends BaseRecordSheetForm
{
  public function configure()
  {
       unset(
        $this['record_id']
           
        );
       
        $this->setWidget('sheet', new sfWidgetFormReadOnly(array(
            'plain'          => false,
            'value_callback' => array('RecordSheetPeer', 'retrieveByPK')
          )));
        
        $this->setValidator('physical_sheet',new sfValidatorInteger(array('required' => true)));
        $this->setValidator('book_id',new sfValidatorPropelChoice(array('model' => 'Book', 'column' => 'id', 'required' => true)));
          $sf_formatter_revisited = new sfWidgetFormSchemaFormatterRevisited($this);
          $this->getWidgetSchema()->addFormFormatter('Revisited', $sf_formatter_revisited);
          $this->getWidgetSchema()->setFormFormatterName('Revisited');
          
        $this->getWidget('book_id')->setAttribute('class', 'book_sheet');
        $this->getWidget('book_id')->setLabel('Book');
        
  }
}
