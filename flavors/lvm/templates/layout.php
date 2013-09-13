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
<?php use_helper('I18N', 'JavascriptBase') ?>
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
  <head>
    <?php include_http_metas() ?>
    <?php include_metas() ?>
    <?php include_title() ?>
    <script type="text/javascript">
      var theme = 'ThemePanel';
      var myThemePanelBase = '<?php echo $sf_request->getRelativeUrlRoot();?>' + '/pmJSCookMenuPlugin/images/' + theme +'/';
      var cmBase = myThemePanelBase;
    </script>
    <link rel="shortcut icon" href="<?php echo image_path("favicon.ico") ?>" />
  </head>

  <body onLoad="setFontSize()">
    <?php include_partial('global/flashes') ?>
    <div id="wrapper">
      <div id="header">
       <div class="logo">
          <?php echo link_to(image_tag("logolvm.gif", array('alt' => __('Liceo Víctor Mercante - UNLP'))), '@homepage', array('title' => __('Ir al inicio'))) ?>
        </div>

        <div class="navigation">
          <?php if ($sf_user->isAuthenticated()): ?>
            <div class="top_navigation">
              <?php echo link_to(__('salir'), '@sf_guard_signout') ?>
            </div>

            <div class="user">
              <?php echo __('Usted ha ingresado como  %%username%%',
                array('%%username%%' => $sf_user->getUsername())) ?>
            </div>

            <?php if (count($sf_user->getGroups()) > 1): ?>
              <div id="change-role">
                <?php $form = new ChangeRoleForm(array(), array('actual_user' => $sf_user)) ?>
                <form action="<?php echo url_for('mainBackend/changeRole') ?>" method="post">
                  <table>
                    <tr>
                      <td><?php echo __("Actual role"); ?>:</td>
                      <td><strong><?php echo $sf_user->getLoginRole(); ?></strong></td>
                    </tr>
                      <tr>
                        <td><?php echo __('Log in with another role') ?>:</td>
                        <td><?php echo $form['roles']->render() ?>
                        <input type="submit" value="<?php echo __('Change') ?>" /></td>
                      </tr>
                  </table>
                </form>
              </div>
            <?php endif ?>


            <div class="version">
              <?php echo link_to_function(image_tag('zoom_plus.png', array('alt' => 'A+', 'title' => __('Agrandar tamaño de letra'))),'zoomIn()', array('style'=>'padding-right:8px;'))?>
              <?php echo link_to_function(image_tag('zoom_minus.png', array('alt' => 'A-', 'title' => __('Achicar tamaño de letra'))),'zoomOut()', array('style'=>'padding-right:8px;'))?>
            </div>
          <?php endif?>
        </div><!-- end navigation -->
        <div style="clear: both; height: 1px; font-size: 1px">&nbsp;</div>
      </div> <!-- end header -->

      <?php if (sfConfig::get('app_testing')):?>
        <div style="position:absolute; left: 300px; top: 0px; font-size:14px; ">
          <center><div style="margin: 4px; width: 200px; background-color: yellow; border: solid 1px red; color: red; padding:4px; text-align: center; "> Versión de prueba </div></center>
        </div>
      <?php endif?>

      <div id="menu-div">
        <div class="content">
          <?php if ($sf_user->isAuthenticated()): ?>
            <?php $menu = pmJSCookMenu::createFromYaml(SchoolBehaviourFactory::getInstance()->getMenuYaml()) ?>
            <?php echo $menu->render() ?>
          <?php endif?>
          <div class="search-content">
            <form action="<?php echo url_for('search') ?>" method="post">
              <input type="text" name="query" id="query"/>
              <input type="submit" value="<?php echo __('Search')?>"  class="search"/>
            </form>
          </div>
        </div>
      </div><!-- end menu-div -->

      <div id="content">
        <?php echo $sf_content ?>
      </div><!-- end content-CONTENT -->

      <div id="footer">
        <div class="logo_footer">
          <?php echo link_to(image_tag("logo-kimkelen-footer.png", array('alt' => __('Kimkelen'))), '@homepage', array('title' => __('Ir al inicio'))) ?>
        </div>
          © <?php echo date('Y'); ?> | CeSPI-UNLP | <?php echo sfConfig::get('app_version_number') ?>
      </div><!-- end footer -->
    </div> <!-- end wrapper -->
  </body>
</html>