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
 * csWidgetFormSelectDoubleList is a customized version of sfWidgetFormSelectDoubleList
 * to avoid setting always same options to sfWidgetFormSelectDoubleList
 *
 * @author Christian A. Rodriguez <car at cespi.unlp.edu.ar>
 */
class csWidgetFormSelectDoubleList extends sfWidgetFormSelectDoubleList {

  protected function configure($options = array(), $attributes = array())
  {
        sfContext::getInstance()->getConfiguration()->loadHelpers(array('Asset','Tag', 'I18N'));

        parent::configure($options,$attributes);
        $this->addOption('class_select','ancho');
        $this->addOption('label_unassociated', __('Unassociated'));
        $this->addOption('label_associated', __('Associated'));

        $this->addOption('associate', image_tag('/sfFormExtraPlugin/images/next.png'));
        $this->addOption('unassociate', image_tag('/sfFormExtraPlugin/images/previous.png'));
  }



}
?>