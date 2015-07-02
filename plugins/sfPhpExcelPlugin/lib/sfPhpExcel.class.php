<?php

class sfPhpExcel extends PHPExcel
{
  public function __construct()
  {
    parent::__construct();
    
    $this->getProperties()->setCreator(sfConfig::get('ex_meta_creator'));
    $this->getProperties()->setTitle(sfConfig::get('ex_meta_title'));
    $this->getProperties()->setSubject(sfConfig::get('ex_meta_subject'));
    $this->getProperties()->setDescription(sfConfig::get('ex_meta_description'));
    $this->getProperties()->setKeywords(sfConfig::get('ex_meta_keyword'));
    $this->getProperties()->setCategory(sfConfig::get('ex_meta_category'));
    $this->setActiveSheetIndex(0);
    
  }
}