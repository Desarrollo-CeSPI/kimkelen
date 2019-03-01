<?php
class LogMedicalCertificatePeer extends BaseLogMedicalCertificatePeer
{
    public static function retrieveByMedicalCertificate($medical_certificate)
    {
        $c = new Criteria();
        $c->add(self::MEDICAL_CERTIFICATE_ID,$medical_certificate->getId());
        $c->addDescendingOrderByColumn(self::UPDATED_AT);
        return self::doSelect($c);
    }
}
