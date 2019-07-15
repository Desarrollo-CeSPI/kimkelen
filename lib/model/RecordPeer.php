<?php

class RecordPeer extends BaseRecordPeer
{
    public static function retrieveByCourseOriginIdAndRecordType($course_origin_id, $record_type)
    {
        $c = new Criteria();
        $c->add(RecordPeer::RECORD_TYPE,$record_type);
        $c->add(RecordPeer::COURSE_ORIGIN_ID,$course_origin_id);
        
        return RecordPeer::doSelectOne($c);
    }
}
