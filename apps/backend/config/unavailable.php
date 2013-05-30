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
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">

<?php $path = sfConfig::get('sf_relative_url_root', preg_replace('#/[^/]+\.php5?$#', '', isset($_SERVER['SCRIPT_NAME']) ? $_SERVER['SCRIPT_NAME'] : (isset($_SERVER['ORIG_SCRIPT_NAME']) ? $_SERVER['ORIG_SCRIPT_NAME'] : ''))) ?>

<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="es" lang="es">
  <head>

    <meta name="title" content="Kimkëlen" />
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>Sistema de gestión de alumnos Kimkëlen - <?php echo sfConfig::get('app_version_number') ?></title>

    <link rel="shortcut icon" href="/favicon.ico" />
    <link rel="stylesheet" type="text/css" media="screen" href="<?php echo $path ?>/css/style.css" />
    <link rel="stylesheet" type="text/css" media="screen" href="<?php echo $path ?>/css/main.css" />

  </head>
  <body>

    <div id="wrapper">
      <div id="header">
      </div>
      <div id="container">
      </div>

      <div id="content">
        <div id="sf_admin_container">
          <h2>El sitio está temporalmente deshabilitado</h2>
          <h3>Estamos realizando tareas de mantenimiento.</h3>

          <div id="sf_admin_content">
            <p>Por favor, intente nuevamente más tarde. Disculpe las molestias.</p>
          </div>
        </div>
      </div>

      <div id="footer">
        © <?php echo date('Y'); ?> | CeSPI-UNLP | <?php echo sfConfig::get('app_version_number') ?>
      </div>
    </div>
  </body>
</html>