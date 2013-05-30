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
<?php use_helper('Javascript', 'Object','I18N','Form') ?>
<?php use_stylesheet('/sfPropelRevisitedGeneratorPlugin/css/global.css') ?>
<?php use_stylesheet('/sfPropelRevisitedGeneratorPlugin/css/extended.css') ?>

<div id="sf_admin_container">
  <h1>Respaldo de los datos del sistema</h1>

  <div id="sf_admin_content">
    <p><strong>Advertencia:</strong> La generación de un archivo de respaldo puede demorar unos minutos dependiendo de la cantidad de datos que existan en el sistema.</p>

    <?php $url = url_for("mainBackend/downloadBackup")?>

    <div style="width: 30%;margin:40px; padding:10px;border: 1px solid #305c0c; -moz-border-radius:5px;">
      <div id="download-link">
        <?php echo link_to_remote('Generar archivo de respaldo',array(
          'url'       =>  url_for('mainBackend/generateBackup'),
          'loading'   =>  '$("ajax-indicator").show(); $("download-link").hide()',
          'complete'  =>  '$("ajax-indicator").hide(); $("download-link").show(); $("myIFrm").src= "'.$url.'";'
        ))?>
      </div>

      <div id="ajax-indicator" style="display:none">
        <?php # echo image_tag('ajax-loader.gif', array('style'=>'float:left'));?>
        <p style="padding-top:10px;padding-left:40px;">Generando archivo de respaldo...<p>
      </div>
    </div>
    <iframe id="myIFrm" src="" style="visibility:hidden; float:right;">
    </iframe>

  </div>
</div>