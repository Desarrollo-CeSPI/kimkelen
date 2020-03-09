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
    
    public function countRecordDetailsForSheet($sheet=NULL)
    {
        $c = new Criteria();
        $c->addJoin(RecordDetailPeer::RECORD_ID, RecordPeer::ID);
        $c->add(RecordDetailPeer::RECORD_ID,$this->getId());
        if(! is_null($sheet))
        {
            $c->add(RecordDetailPeer::SHEET,$sheet);
        }
        $c->add(RecordPeer::STATUS, RecordStatus::ACTIVE);
        
        return RecordDetailPeer::doCount($c);
    }
    
    
    public function countRecordDetailsForSheetAndResult($sheet=NULL,$result)
    {
        $c = new Criteria();
        $c->addJoin(RecordDetailPeer::RECORD_ID, RecordPeer::ID);
        $c->add(RecordDetailPeer::RECORD_ID,$this->getId());
        if(! is_null($sheet))
        {
            $c->add(RecordDetailPeer::SHEET,$sheet);
        }
        $c->add(RecordPeer::STATUS, RecordStatus::ACTIVE);
        $c->add(RecordDetailPeer::RESULT,$result);
        
        return RecordDetailPeer::doCount($c);
    }
    
    public function getRecordSheet()
    {
        $c = new Criteria();
        $c->add(RecordSheetPeer::RECORD_ID, $this->getId());

        return RecordSheetPeer::doSelectOne($c);
    }
    
    public function getFullName()
    {

        switch($this->getRecordType())
        {
          case RecordType::COURSE:
             
              $cs = CourseSubjectPeer::retrieveByPK($this->getCourseOriginId());
              
              $name =  'Curso: '. (($cs->getCourse()->getIsPathway())? '(Trayectoria) ': '' ). $cs->getCourse()->getName() . " | " . $cs->getCourse()->getSchoolYear();
                
            break;
          case RecordType::EXAMINATION :
              
              $es = ExaminationSubjectPeer::retrieveByPK($this->getCourseOriginId());
              $name = 'Mesa: '. $es->getCareerSubjectSchoolYear();
            break;
          case RecordType::EXAMINATION_REPPROVED:
            $ers = ExaminationRepprovedSubjectPeer::retrieveByPK($this->getCourseOriginId()) ;
            $name = 'Mesa previa: '. $ers->getCareerSubject() ." | ". $ers->getExaminationRepproved()->getSchoolYear() ;
            
            break;
            
            
        }
        return "Acta N°" . $this->getId() ." - " . $name;
    }
    
}
