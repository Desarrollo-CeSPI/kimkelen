<?php

class RecordDetailPeer extends BaseRecordDetailPeer
{
    public static function retrieveByRecordAndStudent($record,$student)
    {
        $c = new Criteria();
        $c->add(self::STUDENT_ID,$student->getId());
        $c->add(self::RECORD_ID,$record->getId());
        
        return RecordDetailPeer::doSelectOne($c);
    }
}
