<?php

class SettingParameterPeer extends BaseSettingParameterPeer
{
    public static function retrieveByName($name)
    {
        $c = new Criteria();
        $c->add(SettingParameterPeer::NAME,$name);
        
        return SettingParameterPeer::doSelectOne($c);
    }
}
