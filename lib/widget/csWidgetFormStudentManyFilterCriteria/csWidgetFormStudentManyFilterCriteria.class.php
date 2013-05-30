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
 * csWidgetFormStudentManyFilterCriteria is a Filter criteria for students
 * Examples are:
 *  * All students
 *  * Division students
 *
 *
 * @author Christian A. Rodriguez <car at cespi.unlp.edu.ar>
 */
abstract class csWidgetFormStudentManyFilterCriteria extends sfWidgetForm {

  protected function configure($options = array(), $attributes = array()) {
    sfContext::getInstance()->getConfiguration()->loadHelpers(array('Asset','Tag','Javascript','Url'));
    parent::configure($options, $attributes);
    $this->addRequiredOption('title');
    $this->addOption('criteria',new Criteria());
    $this->addOption('update_container');
    $this->addOption('fixed_values', array());
    $this->addOption('associated');
    $this->addOption('associated_container');
    $this->addOption('associated_name');
    $this->addOption('associated_student_url',url_for('csWidgetFormStudentMany/associatedStudents'));
    $this->addOption('associate_label','Add');
  }

  protected function getUnassociatedStudentsActions($name)
  {
    sfContext::getInstance()->getConfiguration()->loadHelpers(array("I18N"));
    
    $fixed_values = json_encode($this->getOption('fixed_values'));
    return sprintf('<ul><li class="add"><a href="#" onclick="%s">%s</a></li></ul>',
            'csWidgetFormStudentMany.addSelected(\''.$this->generateId($name).'\',\''.__($this->getOption('associated_name')).'\',\''.$this->getOption('associated_container').'\',\''.$this->getOption('associated_student_url').'\',\''.$fixed_values.'\'); return false;',
            __($this->getOption('associate_label')));
  }



  protected function getUpdateContainerAction($name, $url, $params)
  {
    $update_id = $this->getOption('update_container');
    $widget_options = base64_encode(serialize($this->getOptions()));
    $class = get_class($this);
    $coded_params = '';
    foreach($params as $k=>$v)
    {
      $coded_params.="&$k=$v";
    }
    $params = "name=$name&class=$class&class_options=$widget_options".$coded_params;
    $associated_container = $this->getOption('associated_container');
    return "csWidgetFormStudentMany.updateContainer('$update_id','$url','$params','$associated_container'); return false";
    
  }

}
?>