<?php

class Book extends BaseBook
{
      public function __toString()
      {
          return $this->getName();
      }
      
      public function canBeDeleted()
      {
          $c = new Criteria();
          $c->add(RecordSheetPeer::BOOK_ID,$this->getId());
          
          return RecordSheetPeer::doSelect($c) == 0;
      }

}
