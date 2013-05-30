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
 * csWidgetFormStudentMany represents a Students selector based on various filter criterias
 *
 * @author Christian A. Rodriguez <car at cespi.unlp.edu.ar>
 */
class csWidgetFormStudentMany extends sfWidgetForm {
  /**
   * Constructor.
   *
   * Available options:
   *
   *  * criteria:                 A default criteria for prefilter students
   *  * filter_criterias:         An array of csWidgetFormStudentManyFilterCriteria objects
   *  * unassociate_label:        String to show for unassociate button
   *  * associate_label:          String to show for associate buton
   *  * fixed_values:             An array of student ids that will not be allowed to be unset
   */

  protected function configure($options = array(), $attributes = array())
  {
    sfContext::getInstance()->getConfiguration()->loadHelpers(array('Asset','Tag','Javascript','Url'));
    parent::configure($options, $attributes);
    $this->addRequiredOption('criteria');
    $this->addOption('unassociate_label','Delete');
    $this->addOption('associate_label','Add');
    $this->addOption('associated_title','Selected');
    $this->addOption('unassociated_title','Select new values');
    $this->addOption('fixed_values',array());
    $this->addOption('fixed_values_help',"Highlighted students can't be deleted");
    $this->addOption('update_filter_criterias',url_for('csWidgetFormStudentMany/updateFilterCriterias'));
    $this->addOption('filter_criterias',array(new csWidgetFormStudentManyFilterCriteriaAllStudents()));
    $this->addOption('template', <<<EOF
<div class="cs_student_many">
  <div class="associated">
    <div class="title">%associated_title%</div>
    <ul class="associated_actions">
        %associated_students_actions%
    </ul>
    <span class="help">%fixed_help%</span>
    <ul id="%associated_students_container%" class="associated_container">
        %associated_students%
    </ul>
  </div>
  <div class="unassociated">
    <div class="title">%unassociated_title%</div>
    <div class="unassociated_container">
      %unassociated_criterias%
      <div id="%unassociated_criteria_container_id%">
      %unassociated_criteria_container%
      </div>
    </div>
  </div>
  <div style="clear: both"></div>
</div>
EOF
          );
    }

  /**
   * Returns a string with list items <li> of associated students
   *
   * @param $name Represents widget's name
   * @param array $student_ids  array of integer representing student ids
   * @return string
   */
  public static function getAssociatedStudentsList($name, $student_ids, $fixed_values=array())
  { $ret='';
    $criteria = new Criteria();
    $criteria->addAscendingOrderByColumn(PersonPeer::LASTNAME);
    $criteria->addAnd(StudentPeer::ID,$student_ids, Criteria::IN);
    $students = StudentPeer::doSelectJoinPerson($criteria);
    $name.="[]";
    foreach($students as $s)
    {
      $fixed=in_array($s->getId(),$fixed_values);
      $checkbox=$fixed?"&nbsp;":"<input type='checkbox' name='checkbox_$name' />";
      $fixed_class=$fixed?'fixed':'';
      $hidden_widget= new sfWidgetFormInputHidden();
      $hidden=$hidden_widget->render($name,$s->getId());
      $ret.="<li class='associated_student $fixed_class'><span>$checkbox</span>$s $hidden</li>";
    }
    return $ret;
  }

  /**
   * Return a string with unassociate action
   * @param string $name  Widget's name
   * @return string
   */
  public function getAssociatedStudentsActions($name)
  {
    sfContext::getInstance()->getConfiguration()->loadHelpers(array("I18N"));

    $container = "cs_student_many_unassociated_containter_".$this->generateId($name);

    $associated_container = "cs_student_many_associated_containter_".$this->generateId($name);
    $id=$this->generateId($name."_current_filter");
    $url = $this->getOption('update_filter_criterias');
    return sprintf('<li><a class="delete" href="#" onclick="%s">%s</a></li><li><a href="#" onclick="%s">%s</a></li>',
            "csWidgetFormStudentMany.deleteSelected('cs_student_many_associated_containter_".$this->generateId($name)."','$id','$container','$url'); return false;",
            __($this->getOption('unassociate_label')),
            "jQuery('#$associated_container input:checkbox').each(function() { jQuery(this).click(); }); return false;",
            __("Click all"));
  }
/**
 * Returns a list of filter criterias buttons. It's a kind of toolbar
 * if there is onlye one filter criteria, don't show toolbar
 *
 * @param $name Widget's name
 * @param csWidgetFormStudentManyFilterCriteria $current_fc Represents current selected filter criteria
 * @return string
 */
  protected function getFilterCriteriasTitle($name, csWidgetFormStudentManyFilterCriteria $current_fc)
  {
    if ( count($this->getOption('filter_criterias')) == 1) return '';

    $hidden_widget= new sfWidgetFormInputHidden();
    $id=$this->generateId($name."_current_filter");
    $value="class=".get_class($current_fc)."&options=".base64_encode(serialize($current_fc->getOptions()))."&name=$name";
    $ret = $hidden_widget->render($name."[current_filter]",$value,array('id'=>$id));

    $container_id = "cs_student_many_unassociated_containter_".$this->generateId($name);
    foreach( $this->getOption('filter_criterias') as $fc)
    {
      $class=get_class($fc);
      $options=base64_encode(serialize($fc->getOptions()));
      $params="class=$class&options=$options&name=$name";
      $url = $this->getOption('update_filter_criterias');
      $content=sprintf('<a href="#" onclick="%s">%s</a>',
            "csWidgetFormStudentMany.changeCurrentFilter('$id','$container_id','$url','$params'); return false;",
            $fc->getOption('title'));
      $ret.="<li>$content</li>";
    }
    return "<ul class='filter_criterias'>$ret</ul>";
  }

  /**
   * Return current filter criteria. The one is currently in use
   *
   * @param string $name  Widget's name
   * @param array $widget_values  Widget's values
   * @return csWidgetFormStudentManyFilterCriteria
   */
  protected function getCurrentFilterCriteria($name, $widget_values)
  {

    $criterias = $this->getOption('filter_criterias');
    

    if ( !isset ($widget_values['current_filter']))
    {
      $ret =array_shift($criterias);
      if (is_null($ret) ) throw new LogicException (get_class($this)." Exception: filter_criterias option must be an array with at least one element!");
      return $ret;
    }
    preg_match('/class=([a-z]+)&/i', $widget_values['current_filter'], $matches);
    $current = isset($matches[1])?$matches[1]:'undef';
    foreach($criterias as $c)
    {
      if ( get_class($c) == $current )
              return $c;
    }
    $ret = array_shift($criterias);
    if (is_null($ret) ) throw new LogicException (get_class($this)." Exception: filter_criterias option must be an array with at least one element!");
    return $ret;
  }

  /**
   * Traverse every filter criterias initializing options needed for integration
   *
   * @param string $name Widget's name
   */
  private function updateFilterCriteriasOptions($name)
  {
    foreach( $this->getOption('filter_criterias') as $fc)
    {
      $fc->addOption('criteria',$this->getOption('criteria'));
      $fc->addOption('associate_label',$this->getOption('associate_label'));
      $fc->addOption('fixed_values',$this->getOption('fixed_values'));
      $fc->addOption('associated_container',"cs_student_many_associated_containter_".$this->generateId($name));
      $fc->addOption('associated_name',$name);
      $fc->addOption('update_container',"cs_student_many_unassociated_containter_".$this->generateId($name));
    }
  }

  public function getJavaScripts() {
    $fc_js = array_map(create_function('$f','$f->getJavascripts();'),$this->getOption('filter_criterias'));
    return array_merge(parent::getJavascripts(),array('/csWidgetFormStudentMany/js/csWidgetFormStudentMany.js'),$fc_js);
  }

  public function getStylesheets() {
    $fc_css = array_map(create_function('$f','$f->getStylesheets();'),$this->getOption('filter_criterias'));
    return array_merge(parent::getStylesheets(),array('/csWidgetFormStudentMany/css/csWidgetFormStudentMany.css'=>'all'),$fc_css);
  }

  public function render($name, $value = null, $attributes = array(), $errors = array())
  {
    sfContext::getInstance()->getConfiguration()->loadHelpers(array("I18N"));

    $this->updateFilterCriteriasOptions($name);

    if (is_null($value))
    {
      $value = array();
    }

    $filter_criteria = $this->getCurrentFilterCriteria($name, $value);


    $are_fixed = count(array_intersect($value,$this->getOption('fixed_values')))>0;

    return strtr($this->getOption('template'), array(
      "%associated_students_container%"   => "cs_student_many_associated_containter_".$this->generateId($name),
      "%associated_students%"             => self::getAssociatedStudentsList($name, $value, $this->getOption('fixed_values')),
      "%associated_students_actions%"     => $this->getAssociatedStudentsActions($name),
      "%unassociated_criterias%"          => $this->getFilterCriteriasTitle($name,$filter_criteria),
      "%unassociated_criteria_container_id%" => "cs_student_many_unassociated_containter_".$this->generateId($name),
      "%unassociated_criteria_container%" => $filter_criteria->render("cs_student_many_unassociated_$name", $value ),
      "%associated_title%"                => __($this->getOption('associated_title')),
      "%unassociated_title%"              => __($this->getOption('unassociated_title')),
      "%fixed_help%"                      => $are_fixed?$this->getOption('fixed_values_help'):'',
    ));
  }




}
?>