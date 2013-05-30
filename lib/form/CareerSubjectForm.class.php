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
 * CareerSubject form.
 *
 * @package    conservatorio
 * @subpackage form
 * @author     Your name here
 * @version    SVN: $Id: sfPropelFormTemplate.php 10377 2008-07-21 07:10:32Z dwhittle $
 */
class CareerSubjectForm extends BaseCareerSubjectForm
{
  public function configure()
  {

    unset( $this['is_option'],$this['created_at']);


     /* Career as static */
    $this->setWidget('career_id', new sfWidgetFormReadOnly(array(
      'plain'          => false,
      'value_callback' => array('CareerPeer', 'retrieveByPk')
    )));

    /* Subject year's widget and validator */
    // get possible years for this career subject
    if ( $this->getObject()->getIsCorrelative() )
    { //If there are career subjects that has this career subject as correlative
      $this->setWidget('year', new sfWidgetFormReadOnly(array('plain'          => false)));
      $this->getWidgetSchema()->setHelp('year','No es posible cambiar el año de la carrera mientras existan materias que nos declaren como correlativa');
    }
    else
    {
      $years        = $this->getObject()->getCareer()->getYearsRange(true);
      $this->setWidget('year', new sfWidgetFormChoice(array(
        'choices' => $years
      )));
      $this->setValidator('year', new sfValidatorNumber(array('min' => $this->getObject()->getCareer()->getMinYear(),'max'=>$this->getObject()->getCareer()->getMaxYear())));
    }

    /* Subject widget */
    if ($this->isNew()||$this instanceOf CareerSubjectOptionForm)
    {
      $this->getWidget('subject_id')->setOption('add_empty', 'Seleccione una materia');
      $this->getWidget('subject_id')->setOption('peer_method', 'doSelectOrdered');
    }
    else
    {
      $this->setWidget('subject_id', new sfWidgetFormReadOnly(array(
        'plain'          => false,
        'value_callback' => array('SubjectPeer','retrieveByPk')
      )));
    }

    /* Subject orientation widget and validator */
    if ( $this->getObject()->getIsCorrelative() )
    {
      //If there are career subjects that has this career subject as correlative
      $this->setWidget('orientation_id', new sfWidgetFormReadOnly(array('plain'          => false,'empty_value'=>'N/A')));
      
      $this->getWidgetSchema()->setHelp('orientation_id','No es posible cambiar la orientación mientras existan materias que nos declaren como correlativa');
    }
    else
    {
      $this->getWidgetSchema()->setHelp('orientation_id', 'Hace que la materia pertenezca a una orientación');
    }
    
    /* Subject sub orientation widget and validator */
    if ($this->getObject()->getIsCorrelative())
    {
      // If there are career subjects that has this career subject as correlative
      $this->setWidget('sub_orientation_id', new sfWidgetFormReadOnly(array(
        'plain' => false,
        'empty_value' => 'N/A'
      )));
      
      $this->getWidgetSchema()->setHelp('sub_orientation_id', 'No es posible cambiar la sub orientación mientras existan materias que nos declaren como correlativa');
    }
    else
    {
      $widget = new sfWidgetFormPropelChoice(array(
        'model' => 'SubOrientation',
        'add_empty' => true
      ));
      
      $this->setWidget('sub_orientation_id', new dcWidgetAjaxDependencePropel(array(
        'dependant_widget' => $widget,
        'observe_widget_id' => 'career_subject_orientation_id',
        'related_column' => 'orientation_id'
      )));
      $this->getWidgetSchema()->setHelp('sub_orientation_id', 'Hace que la materia pertenezca a una sub orientación');
    }
        

    /* Widget Helps */    

    $this->getWidgetSchema()->setHelp('type', 'La duración de la materia no puede cambiarse si la materia es una opción de una optativa.');

    $this->getValidatorSchema()->getPostValidator()->setMessage('invalid', 'Ya existe otra materia igual para el año seleccionado');
  }

}