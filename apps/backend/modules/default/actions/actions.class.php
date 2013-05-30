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
 * default actions.
 *
 * @package    asociador
 * @subpackage default
 * @author     Your name here
 * @version    SVN: $Id: actions.class.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
class defaultActions extends sfActions
{
  /**
   * Error page for page not found (404) error
   *
   */
  public function executeError404()
  {

  }

  protected function displayManual(sfWebRequest $request, $file_name)
  {
    $path = appConfig::getManualsPath() . DIRECTORY_SEPARATOR . $file_name;    
    $response = $this->getResponse();
    $response->setHttpHeader('Pragma', '');
    $response->setHttpHeader('Cache-Control', '');
    $data = file_get_contents($path);
    $response->setHttpHeader("Content-Type", "application/pdf");
    $response->setHttpHeader('Content-Disposition', "attachment; filename=\"$file_name\"");
    $response->setContent($data);

    return sfView::NONE;
  }

  public function executeDisplayAdministrationManual(sfWebRequest $request)
  {
    $file_name = 'ManualKimkelen.pdf';
    return $this->displayManual($request, $file_name);
  }

}