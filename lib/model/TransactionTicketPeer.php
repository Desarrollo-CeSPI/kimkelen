<?php 
/*
 * Kimkëlen - School Management Software
 * Copyright (C) 2013 CeSPI - UNLP <desarrollo@cespi.unlp.edu.ar>
 *
 * This file is part of Kimkëlen.
 *
 * Kimkëlen is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License v2.0 as published by
 * the Free Software Foundation.
 *
 * Kimkëlen is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with Kimkëlen.  If not, see <http://www.gnu.org/licenses/gpl-2.0.html>.
 */ ?>
<?php

class TransactionTicketPeer extends BaseTransactionTicketPeer
{
  const EXPIRATION_STR="+4 days";

  protected static function getTable($object)
  {
    $peer=get_class($object->getPeer());
    return constant("$peer::TABLE_NAME");
  }

  public static function createTicketFor($object, PropelPDO $con=null)
  {
    $ticket=new TransactionTicket();
    $ticket->setTable(self::getTable($object));
    $ticket->setTablePrimaryKey(serialize($object->getPrimaryKey()));
    $ticket->setCreatedAt(time());
    $ticket->setExpiresAt(strtotime(TransactionTicketPeer::EXPIRATION_STR,$ticket->getCreatedAt('U')));
    $ticket->setTicket($ticket->calculateTicket());
    $ticket->save($con);
    return $ticket;
  }

  public static function retrieveTicketFor($object)
  {
    $c=new Criteria();
    $c->addAnd(self::FIELD_TABLE,self::getTable($object));
    $c->addAnd(self::TABLE_PRIMARY_KEY,serialize($object->getPrimaryKey()));
    $c->addAnd(self::CREATED_AT,date('Y-m-d H:i:s'),Criteria::LESS_THAN);
    $c->addAnd(self::EXPIRES_AT,date('Y-m-d H:i:s'),Criteria::GREATER_THAN);
    $ret=self::doSelectOne($c);
    if (is_null($ret)){
      $ret=self::createTicketFor($object);    
    }
    return $ret;
  }
}