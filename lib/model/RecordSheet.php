<?php

class RecordSheet extends BaseRecordSheet
{
    
    public function __toString()
    {
          return  strval($this->getSheet());
      }
}
