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

class kimkelenFlavorTask extends sfBaseTask
{
  protected function configure()
  {
    $this->addArguments(array(
      new sfCommandArgument('flavor', sfCommandArgument::REQUIRED, 'The name of the flavor to set'),
    ));

    $this->namespace        = 'kimkelen';
    $this->name             = 'flavor';
    $this->briefDescription = '';
    $this->detailedDescription = <<<EOF
The [kimkelen:flavor|INFO] task sets a given [flavor|INFO] as the current one
for this instance of Kimkëlen.

  [php symfony kimkelen:initialize flavor|INFO]

Where [flavor|INFO] is the name of a valid flavor, found in the [flavors/|INFO] directory
inside the project root.

EOF;
  }

  protected function execute($arguments = array(), $options = array())
  {
    $root   = sfConfig::get('sf_root_dir');
    $flavor = $root . '/flavors/' . $arguments['flavor'];

    if (!is_dir($flavor))
    {
      $this->logSection('Error', 'The provided flavor does not exist in the flavors/ directory', null, 'ERROR');

      return false;
    }

    $web = sfConfig::get('sf_web_dir');
    $web_css = $web . '/css';
    $web_img = $web . '/images';
    $pm_pdf_kit_cfg = sfConfig::get('sf_apps_dir') . '/backend/config/pm_pdf_kit.yml';

    $files = array();
    foreach (array($web_css, $web_img, $pdf_cfg,$pm_pdf_kit_cfg) as $file)
    {
      if (file_exists($file))
      {
        $files[] = $file;
      }
    }

    if (!empty($files))
    {
      $this->logSection('Assets', 'Deleting the existing assets');
      $this->getFilesystem()->remove($files);
    }
    else
    {
      $this->logSection('Assets', 'No existing assets found');
    }

    $this->logSection('Assets', 'Linking the chosen flavor: ' . $arguments['flavor']);

    $flavor_css = $flavor . '/web/css';
    $flavor_img = $flavor . '/web/images';
    $pm_pdf_kit = $flavor . '/config/pm_pdf_kit.yml';

    $this->getFilesystem()->symlink($flavor_css, $web_css, true);
    $this->getFilesystem()->symlink($flavor_img, $web_img, true);
    $this->getFilesystem()->symlink($pm_pdf_kit, $pm_pdf_kit_cfg, true);


    $this->logSection('Config', 'Updating configuration');

    $cfg_dir = sfConfig::get('sf_config_dir');

    $configuration = array(
      'nc_flavor' => array(
        'flavors' => array(
          'root_dir' => 'flavors',
          'current'  => $arguments['flavor']
        )
      )
    );

    $updated = @file_put_contents($cfg_dir . '/nc_flavor.yml', sfYaml::dump($configuration));

    if ($updated === false)
    {
      $this->logSection('Config', 'Unable to update configuration', null, 'ERROR');
      $this->logSection('Config', "Please update your configuration file {$cfg_dir}/nc_flavor.yml with this contents:");
      $this->logBlock(sfYamlInline::dump($configuration), 'COMMENT');
    }
    else
    {
      $this->logSection('Flavor', 'Successfully updated flavor configuration file');
    }


    if ($updated === false)
    {
      $this->logSection('Config', 'Unable to update configuration', null, 'ERROR');
      $this->logBlock($school_behavior, 'COMMENT');
    }
    else
    {
      $this->logSection('Behavior', 'Successfully updated school_behavior configuration file');
    }

    $cc = new sfCacheClearTask($this->dispatcher, $this->formatter);
    $cc->run();

    $this->logSection('Done', ':)');
  }
}
