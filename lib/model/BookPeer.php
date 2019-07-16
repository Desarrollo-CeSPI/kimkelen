<?php

class BookPeer extends BaseBookPeer
{
    public static function retrieveActives()
    {
        $c = new Criteria();
        $c->add(self::IS_ACTIVE, TRUE);
                
        return BookPeer::doSelect($c);
    }
}
