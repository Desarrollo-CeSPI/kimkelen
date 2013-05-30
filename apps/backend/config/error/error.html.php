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

<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta name="title" content="symfony project" />
<meta name="robots" content="index, follow" />
<meta name="description" content="symfony project" />
<meta name="keywords" content="symfony, project" />
<meta name="language" content="en" />
<title>Kimkelen</title>

<link rel="shortcut icon" href="/favicon.ico" />
<link rel="stylesheet" type="text/css" media="screen" href="<?php echo $path ?>/css/main.css" />

</head>
<body>
<div class="sfTContainer" style="text-align: center;">
  <div class="sfTMessageContainer sfTAlert">
    <img alt="Error inesperado en el servidor" class="sfTMessageIcon" src="<?php echo $path ?>/sf/sf_default/images/icons/tools48.png" height="48" width="48" />
    <div class="sfTMessageWrap">
      <h1>Ha ocurrido un error</h1>
      <h3 style="width:98% !important; margin-left: 0px;">El servidor retornó "<?php echo $code ?> <?php echo $text ?>".</h3>
    </div>
  </div>

  <div class="sfTMessageInfo">
    <div><h4>El sistema ha sufrido un error que podría comprometer el funcionamiento correcto de la aplicación</h4></div>
    <div>Por favor, envía un email a sus administradores de sistema informando que estabas haciendo cuando esto ocurrió. Lo vamos a solucionar tan pronto como podamos.
    Pedimos disculpas por los inconvenientes.</div>

    <br/>
    <div>
      <div class="sfTIconList">
        <div class="sfTLinkMessage"><a href="javascript:history.go(-1)">Volver a la página anterior</a></div>
      </div>
    </div>
  </div>
</div>
</body>
</html>