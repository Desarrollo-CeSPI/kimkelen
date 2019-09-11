<?php

class RecordSheetPeer extends BaseRecordSheetPeer
{
    public static function retrieveByPshysicalSheetAndBook($physical_sheet,$book_id)
    {
        $c = new Criteria();
        $c->addJoin(RecordSheetPeer::RECORD_ID,RecordPeer::ID);
        $c->add(RecordPeer::STATUS, RecordStatus::ACTIVE);
        $c->add(RecordSheetPeer::PHYSICAL_SHEET,$physical_sheet);
        $c->addAnd(RecordSheetPeer::BOOK_ID,$book_id);
        
        return RecordSheetPeer::doSelect($c);
    }
     
}
