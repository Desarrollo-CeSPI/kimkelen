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
       
       /* $this->setWidget('sheet', new sfWidgetFormReadOnly(array(
            
            'plain'          => false,
            'value_callback' => array('RecordSheetPeer', 'retrieveByPK')
          )));
        */
        $this->setWidget('book_id', new sfWidgetFormInputHidden());
        $this->setWidget('sheet', new sfWidgetFormInputHidden());
        
        $this->setValidator('physical_sheet',new sfValidatorInteger(array('required' => true)));
        $this->setValidator('book_id', new sfValidatorPropelChoice(array('model' => 'Book', 'column' => 'id', 'required' => false)));
          $sf_formatter_revisited = new sfWidgetFormSchemaFormatterRevisited($this);
          $this->getWidgetSchema()->addFormFormatter('Revisited', $sf_formatter_revisited);
          $this->getWidgetSchema()->setFormFormatterName('Revisited');
          
        $this->getWidget('book_id')->setAttribute('class', 'book_sheet');
        $this->getWidget('book_id')->setLabel('Book');
        $this->getWidget('physical_sheet')->setLabel("Hoja " . $this->getObject()->getSheet() . " - Folio físico");
        
        $this->validatorSchema->setPostValidator(new sfValidatorCallback(array('callback' => array($this, 'checkSheetBook'))));
        
  }
  
  public function checkSheetBook($validator, $values)
  {
     $c = new Criteria();
     $c->addJoin(RecordSheetPeer::RECORD_ID,RecordPeer::ID);
     $c->add(RecordPeer::STATUS, RecordStatus::ACTIVE);
     $c->add(RecordSheetPeer::PHYSICAL_SHEET,$values['physical_sheet']);
     $c->addAnd(RecordSheetPeer::BOOK_ID,$values['book_id']);
     $c->add(RecordSheetPeer::ID,$values['id'],Criteria::NOT_EQUAL);

     
     $result = RecordSheetPeer::doCount($c);
 
      if ($result > 0 )
      {
        $error = new sfValidatorError($validator, 'El folio físico ya se encuentra asignado');
        throw new sfValidatorErrorSchema($validator, array('date' => $error));
      }
      
      return $values;
  }
}
