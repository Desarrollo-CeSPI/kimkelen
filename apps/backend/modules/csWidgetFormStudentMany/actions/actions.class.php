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
 * csWidgetFormStudentMany actions.
 *
 * @package    sistema de alumnos
 * @subpackage csWidgetFormStudentMany
 * @author     Your name here
 * @version    SVN: $Id: actions.class.php 12479 2008-10-31 10:54:40Z fabien $
 */
class csWidgetFormStudentManyActions extends sfActions
{
 /**
  * Ajax action for csWidgetFormStudentManyFilterCriteriaAllStudents widget .
  * It filters all students paged by letters
  *
  * @see csWidgetFormStudentManyFilterCriteriaAllStudents
  * @param sfRequest $request A request object
  */
  public function executeFilterAllStudents(sfWebRequest $request)
  {
    $widget_class = $request->getParameter('class');
    $name = $request->getParameter('name');
    $associated = json_decode($request->getParameter('associated'));
    $group = $request->getParameter('group');
    $options = unserialize(base64_decode($request->getParameter('class_options')));
    $options['current_group']=$group;
    $options['associated']=$associated;
    $widget= new $widget_class($options);
    $this->setLayout(null);
    $this->getResponse()->setContent($widget->render($name));
    return sfView::NONE;
  }

  public function executeAssociatedStudents(sfWebRequest $request)
  {
    $name = $request->getParameter('name');
    $associated = json_decode($request->getParameter('associated'));
    $fixed = json_decode($request->getParameter('fixed_values'));
    $this->setLayout(null);
    $this->getResponse()->setContent(csWidgetFormStudentMany::getAssociatedStudentsList($name, $associated,$fixed));
    return sfView::NONE;
  }

  public function executeUpdateFilterCriterias(sfWebRequest $request)
  {
    $widget_class=$request->getParameter('class');
    $options=unserialize(base64_decode($request->getParameter('options')));
    $name = $request->getParameter('name');

    $widget= new $widget_class($options);
    $this->setLayout(null);
    $this->getResponse()->setContent($widget->render($name));
    return sfView::NONE;
  }
}