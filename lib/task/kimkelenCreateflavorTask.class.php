<?php

class kimkelenCreateflavorTask extends sfBaseTask
{
  protected function configure()
  {
    $this->addArguments(array(
      new sfCommandArgument('name', sfCommandArgument::REQUIRED, 'The name of the flavor to create'),
    ));

    $this->namespace = 'kimkelen';
    $this->name = 'create-flavor';
    $this->briefDescription = 'Creates a new flavor';
    $this->detailedDescription = <<<EOF
The [kimkelen:flavor-create|INFO] task creates a Kimkëlen flavor named as one given

  [php symfony kimkelen:create-flavor|INFO]

Where [create-flavor|INFO] is the name of a new flavor, which will be created in [flavors/|INFO] directory
inside the project root.

EOF;
  }

  protected function execute($arguments = array(), $options = array())
  {
    $flavor_name = $arguments['name'];
    $root = sfConfig::get('sf_root_dir');
    $new_flavor = $root . '/flavors/' . $flavor_name;

    if (is_dir($new_flavor))
    {
      $this->logSection('Error', 'The provided flavor already exists in flavors/ directory', null, 'ERROR');

      return false;
    }
    else
    {
      //creates directories

      $this->getFilesystem()->mkdirs($new_flavor . '/i18n/');
      $this->getFilesystem()->mkdirs($new_flavor . '/lib/behavior/factory');
      $this->getFilesystem()->mkdirs($new_flavor . '/modules/default/templates');
      $this->getFilesystem()->mkdirs($new_flavor . '/modules/mainBackend/templates');
      $this->getFilesystem()->mkdirs($new_flavor . '/modules/mainFrontend/templates');
      $this->getFilesystem()->mkdirs($new_flavor . '/modules/sfGuardAuth/templates');

      $this->getFilesystem()->mkdirs($new_flavor . '/web/css');
      $this->getFilesystem()->mkdirs($new_flavor . '/web/images');

      //creates files
      $content = <<<HED
default:
  http_metas:
    content-type: text/html

  metas:
    title:         Kimkëlen
    description:   Sistema de gestión de alumnos
    keywords:      Kimkelen, alumnos
    language:      es_AR

  stylesheets:    [main.css, smoothness/jquery-ui-1.7.2.custom.css, /pmJSCookMenuPlugin/css/ThemePanel/theme.css]

  javascripts:    [jquery.js, jquery-ui.js, jquery.cookie.js, no-conflict.js, /sfProtoculousPlugin/js/prototype.js, main.js, jquery.notification.js, i18n/ui.datepicker-es.js, /pmJSCookMenuPlugin/js/JSCookMenu.js, /pmJSCookMenuPlugin/js/ThemePanel/theme.js]

  has_layout:     on
  layout:         layout
HED;

      file_put_contents($new_flavor . '/config/view.yml', $content);

      $form_factory_name = ucfirst($flavor_name) . 'FormFactory';
      $content = <<<HED
<?php

/**
 *  $form_factory_name form factory class.
 *
 */

class $form_factory_name extends BaseFormFactory
{
  }
HED;

      file_put_contents($new_flavor . '/lib/behavior/factory/' . ucfirst($flavor_name) . 'FormFactory.class.php', $content);

      $school_behavior_factory_name = ucfirst($flavor_name) . 'SchoolBehaviourFactory';
      $content = <<<HED
<?php

/**
 *
 */
class $school_behavior_factory_name extends SchoolBehaviourFactory
{
}
HED;

      file_put_contents($new_flavor . '/lib/behavior/factory/' . ucfirst($flavor_name) . 'SchoolBehaviourFactory.class.php', $content);

      $evaluator_behaviour_name = ucfirst($flavor_name) . 'EvaluatorBehaviour';
      $content = <<<HED
<?php

/**
 * Copy and rename this class if you want to extend and customize
 */
class $evaluator_behaviour_name extends BaseEvaluatorBehaviour
{
}
HED;

      file_put_contents($new_flavor . '/lib/behavior/' . ucfirst($flavor_name) . 'EvaluatorBehaviour.class.php', $content);

      $school_behaviour_name = ucfirst($flavor_name) . 'SchoolBehaviour';
      $content = <<<HED
<?php

/**
 * Copy and rename this class if you want to extend and customize
 */
class $school_behaviour_name extends BaseSchoolBehaviour
{
}
HED;

      file_put_contents($new_flavor . '/lib/behavior/' . ucfirst($flavor_name) . 'SchoolBehaviour.class.php', $content);


$content = <<<HED
<?php use_helper('I18N', 'JavascriptBase') ?>
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
  <head>
    <?php include_http_metas() ?>
    <?php include_metas() ?>
    <?php include_title() ?>
    <script type="text/javascript">
    </script>
    <link rel="shortcut icon" href="/favicon.ico" />
  </head>

  <body onLoad="setFontSize()">
    <?php include_partial('global/flashes') ?>
    <div id="wrapper">
      <div id="header">
        <div class="logo">
          <?php echo link_to(image_tag("logo-kimkelen.png", array('alt' => __('Sistema Alumnos - CeSPI'))), '@homepage', array('title' => __('Ir al inicio'))) ?>
        </div>

        <div class="navigation">
            <div class="top_navigation">
            </div>
            <div class="user">
            </div>

            <div class="version">
              <?php echo link_to_function(image_tag('zoom_plus.png', array('alt' => 'A+', 'title' => __('Agrandar tamaño de letra'))), 'zoomIn()', array('style' => 'padding-right:8px;')) ?>
              <?php echo link_to_function(image_tag('zoom_minus.png', array('alt' => 'A-', 'title' => __('Achicar tamaño de letra'))), 'zoomOut()', array('style' => 'padding-right:8px;')) ?>
            </div>

        </div><!-- end navigation -->
        <div style="clear: both; height: 1px; font-size: 1px">&nbsp;</div>
      </div> <!-- end header -->

      <div id="menu-div">
        <div class="content">
          <div class="search-content" >
            <form action="<?php echo url_for('search') ?>" method="post">
              <input type="text" name="query" id="query"/>
              <input type="submit" value="<?php echo __('Search') ?>"  class="search"/>
            </form>
          </div>
        </div>

      </div><!-- end menu-div -->
      <div id="content">

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
HED;

       file_put_contents($new_flavor . '/templates/layout.php', $content);

      $this->logSection('Flavor', "Successfully created new flavor");
    }
  }
}