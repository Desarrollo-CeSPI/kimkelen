<?php

class RecordSheet extends BaseRecordSheet
{
    
    public function __toString()
    {
          return  strval($this->getSheet());
      }

    public function getPhysicalSheetByStudent($student)
    {
         $c = new Criteria();
         $c->add(RecordDetailPeer::RECORD_ID,$this->getRecordId());
         $c->add(RecordDetailPeer::STUDENT_ID,$student->getId());
 
         $rd =  RecordDetailPeer::doSelectOne($c);
        
         $c = new Criteria();
         $c->add(RecordSheetPeer::RECORD_ID,$this->getRecordId());
         $c->add(RecordSheetPeer::SHEET, $rd->getSheet());

         $rs = RecordSheetPeer::doSelectOne($c);
        
         return (!is_null($rs))? $rs->getPhysicalSheet() :"";
    }
}
