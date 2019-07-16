<?php

class Book extends BaseBook
{
      public function __toString()
      {
          return $this->getName();
      }

}
