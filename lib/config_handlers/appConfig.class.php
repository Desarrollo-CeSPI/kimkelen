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
 * appConfig
 *
 * @author Desarrollo CeSPI
 */
class appConfig
{
/**
   * Get the absolute path to the manuals directory.
   *
   * @return string
   */
  static public function getManualsPath()
  {
    $uploads_path = array();

    $uploads_path[] = sfConfig::get('sf_root_dir');

    $uploads_path[] = 'doc';

    return implode(DIRECTORY_SEPARATOR, $uploads_path);
  }

}