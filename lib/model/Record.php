<?php

class Record extends BaseRecord
{
    public function getRecordDetailsForSheet($sheet)
    {
        $c = new Criteria();
        $c->addJoin(RecordDetailPeer::RECORD_ID, RecordPeer::ID);
        $c->add(RecordDetailPeer::RECORD_ID,$this->getId());
        $c->add(RecordDetailPeer::SHEET,$sheet);
        $c->add(RecordPeer::STATUS, RecordStatus::ACTIVE);
        
        return RecordDetailPeer::doSelect($c);
    }
    
    public function countRecordDetailsForSheet($sheet)
    {
        $c = new Criteria();
        $c->addJoin(RecordDetailPeer::RECORD_ID, RecordPeer::ID);
        $c->add(RecordDetailPeer::RECORD_ID,$this->getId());
        $c->add(RecordDetailPeer::SHEET,$sheet);
        $c->add(RecordPeer::STATUS, RecordStatus::ACTIVE);
        
        return RecordDetailPeer::doCount($c);
    }
    
    
    public function countRecordDetailsForSheetAndResult($sheet,$result)
    {
        $c = new Criteria();
        $c->addJoin(RecordDetailPeer::RECORD_ID, RecordPeer::ID);
        $c->add(RecordDetailPeer::RECORD_ID,$this->getId());
        $c->add(RecordDetailPeer::SHEET,$sheet);
        $c->add(RecordPeer::STATUS, RecordStatus::ACTIVE);
        $c->add(RecordDetailPeer::RESULT,$result);
        
        return RecordDetailPeer::doCount($c);
    }
    
}
