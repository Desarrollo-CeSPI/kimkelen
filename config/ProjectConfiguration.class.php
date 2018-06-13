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

# FROZEN_SF_LIB_DIR: /usr/share/php/symfony/1.2/lib

# FROZEN_SF_LIB_DIR: /usr/share/php/symfony/1.2/lib
require_once dirname(__FILE__).'/../lib/symfony/autoload/sfCoreAutoload.class.php';
sfCoreAutoload::register();

require_once dirname(__FILE__).'/../lib/config_handlers/ncFlavorConfigHandler.class.php';
require_once dirname(__FILE__).'/../lib/config_handlers/ncFlavorViewConfigHandler.class.php';
require_once dirname(__FILE__).'/../lib/config_handlers/ncFlavorFrontendViewConfigHandler.class.php';
require_once dirname(__FILE__).'/../lib/config_handlers/ncFlavorAutoload.class.php';


class ProjectConfiguration extends sfProjectConfiguration
{
  public function setup()
  {
    // for compatibility / remove and enable only the plugins you want
    $this->enableAllPluginsExcept(array('sfDoctrinePlugin', 'sfCompat10Plugin'));
  }
}
