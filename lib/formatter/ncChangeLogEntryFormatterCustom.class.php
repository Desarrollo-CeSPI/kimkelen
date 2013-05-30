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
 * ncChangeLogEntryFormatterCustom
 *
 * Custom changelog entry formatter.
 *
 * @author José Nahuel Cuesta Luengo <ncuesta@cespi.unlp.edu.ar>
 */
class ncChangeLogEntryFormatterCustom extends ncChangeLogEntryFormatter
{
  protected $custom_template = '<strong>%date% (%username%):</strong> %changes%';

  public function formatInsertion(ncChangeLogAdapter $adapter)
  {
    return strtr($this->custom_template, array(
      '%date%'     => $adapter->renderCreatedAt(),
      '%username%' => $adapter->renderUsername(),
      '%changes%'  => parent::formatInsertion($adapter)
    ));
  }

  public function formatUpdate(ncChangeLogAdapter $adapter)
  {
    return strtr($this->custom_template, array(
      '%date%'     => $adapter->renderCreatedAt(),
      '%username%' => $adapter->renderUsername(),
      '%changes%'  => parent::formatUpdate($adapter)
    ));
  }

  public function formatDeletion(ncChangeLogAdapter $adapter)
  {
    return strtr($this->custom_template, array(
      '%date%'     => $adapter->renderCreatedAt(),
      '%username%' => $adapter->renderUsername(),
      '%changes%'  => parent::formatDeletion($adapter)
    ));
  }

}