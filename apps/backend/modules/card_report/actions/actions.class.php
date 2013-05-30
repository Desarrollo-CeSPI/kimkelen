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
 * card_report actions.
 *
 * @package    sistema de alumnos
 * @subpackage card_report
 * @author     Your name here
 * @version    SVN: $Id$
 */
class card_reportActions extends sfActions
{
 /**
  * Executes index action
  *
  * @param sfRequest $request A request object
  */
  public function executeIndex(sfWebRequest $request)
  {
    $this->parameters = array(
      'YEAR'           => 2,
      'SCHOOL_YEAR_ID' => 1,
      'CAREER_ID'      => 6,
      'STUDENT_IDS'    => array(3, 5),
      'format'         => BIReport::FORMAT_HTML_STREAM
    );

    $params = array_merge($this->parameters, array('format' => BIReport::FORMAT_PDF));

    $this->report = new BIReport('Boletines/boletin1.prpt', $params);
  }
}