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

/**
 * csWidgetFormStudentManyFilterCriteriaAllStudents represents All stundets criteria
 *
 * @author Christian A. Rodriguez <car at cespi.unlp.edu.ar>
 */
class csWidgetFormStudentManyFilterCriteriaAllStudents extends csWidgetFormStudentManyFilterCriteria
{
  private $groups;
  protected function configure($options = array(), $attributes = array()) {
    parent::configure($options, $attributes);
    $this->addOption('title','All students');
    $this->addOption('group_by',  PersonPeer::LASTNAME );
    $this->addOption('group_letters_count',  2 );
    $this->addOption('url',url_for('csWidgetFormStudentMany/filterAllStudents'));
    $this->addOption('current_group',false);
    $this->addOption('no_group_text','Please click on some group of letters to filter students');
  }

  protected function initGroups()
  { $this->groups = array();

    for($current ='', $count =0, $letter='A'; $letter != chr(ord('Z')+1) ; $letter=chr(ord($letter)+1))
    {
       $current.=$letter;
       if ( (++$count % $this->getOption('group_letters_count'))==0)
       {
        $this->groups[]=$current;
        $current="";
        
       }
    }
    if (strlen($current)>0)
    {
      $last=$current;
    }
    else
    {
      $last=array_pop($this->groups);
    }
    if (strlen($last)==1 && ($this->getOption('group_letters_count') != 1))
    {
      $this->groups[count($this->groups)-1].=$last;
    }
    else
    {
      $this->groups[]=$last;
    }
  }


  protected function getGroupLinks($name)
  { $ret='<ul>';
    foreach($this->groups as $g)
    {
      $ret.= ( $this->getOption('current_group') && ($this->getOption('current_group')==$g) )?
        sprintf('<li>%s</li>',$g)
        :
        sprintf('<li><a href="#" onclick="%s">%s</a></li>',
            $this->getUpdateContainerAction($name, $this->getOption('url'),array('group'=>$g)),$g);
    }
    sfContext::getInstance()->getConfiguration()->loadHelpers(array("I18N"));
    $ret .= sprintf(
      "<li><a href=\"#\" onclick=\"%s\">%s</a></li>",
      $this->getUpdateContainerAction($name, $this->getOption("url"), array("group" => "all")),
      __("All students")
    );
    $ret.='</ul>';
    return $ret;
  }

  public function getCriteria($g)
  {
    $c=$this->getOption('criteria');
    
    $field = $this->getOption('group_by');
    $c->addAscendingOrderByColumn($field);
    
    if ($g != "all")
    {
      $criterion=null;
      for($i=0;$i<strlen($g);$i++)
      { 
        $letter = substr($g,$i,1);
        $cron= $c->getNewCriterion($field,"$letter%",  Criteria::LIKE);

        if ( is_null($criterion) )
          $criterion=$cron;
        else
          $criterion->addOr($cron);
      }
      $c->add($criterion);
    }
    
    $associated = $this->getOption('associated');
    $associated = !is_array($associated)?array():$associated;
    $c->addAnd(StudentPeer::ID,$associated,  Criteria::NOT_IN);
    return $c;
  }

  public function render($name, $value = null, $attributes = array(), $errors = array()) 
  {
    sfContext::getInstance()->getConfiguration()->loadHelpers(array("I18N"));
    $this->initGroups();
    $groups = $this->getGroupLinks($name);
    if ($g=$this->getOption('current_group'))
    {
      $w=new sfWidgetFormPropelChoiceMany( array('model'=>'Student','peer_method'=>'doSelectJoinPerson','criteria'=>$this->getCriteria($g)), array_merge(array("size"=>10),$attributes));
      $choices=$this->getUnassociatedStudentsActions($name).$w->render($name);
    }
    else
    {
      $choices="<span class='help'>".__($this->getOption('no_group_text'))."</span>";
    }
    return $groups.$choices;
  }


}
?>