<?php
class MedicalCertificate extends BaseMedicalCertificate
{
  public static function getDocumentDirectory()
  {
     return sfConfig::get('sf_data_dir') . DIRECTORY_SEPARATOR . 'person-file-data';
  }
  
  public function getDocumentFullPath()
  {
    return self::getDocumentDirectory().DIRECTORY_SEPARATOR.$this->getCertificate();
  }
  
  public function deleteCertificate()
  {
    $this->deletePhysicalCertificate($this->getDocumentFullPath());
    $this->setCertificate('');
    $this->save();
  }
  
  public function delete(PropelPDO $con = null)
	{
    $document_path = $this->getDocumentFullPath();
    parent::delete($con);
    $this->deletePhysicalCertificate($document_path);
  }
  public function deletePhysicalCertificate($document_path)
  {
    if(file_exists($document_path))
      unlink($document_path);
  }
  
  public function canEdit()
  {
      if ( !is_null($this->getCertificateStatusId()) && 
         (($this->getCertificateStatusId() == MedicalCertificateStatus::VALIDATED && !$this->getTheoricClass() )
              || $this->getCertificateStatusId() == MedicalCertificateStatus::NOT_VALIDATED ) )
              
              return false;
      else
          return true;
  }
}