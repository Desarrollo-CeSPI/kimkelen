<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">

<head>
  <?php include_http_metas() ?>
  <?php include_metas() ?>
  <?php include_title() ?>
  <link rel="shortcut icon" href="/favicon.ico" />
</head>

<body>

  <nav class="navbar navbar-default navbar-fixed-top">
    <div class="container-fluid">
      <div class="navbar-header">
      <!--
        <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1" aria-expanded="false">
          <span class="sr-only">Toggle navigation</span>
          <span class="icon-bar"></span>
          <span class="icon-bar"></span>
          <span class="icon-bar"></span>
        </button>
      -->
        <?php echo link_to(image_tag("frontend/kimkelen_logo.png", array('alt' => __('Kimkelen'))), '@homepage', array('title' => __('Inicio'))) ?>
      </div>
      <div class="collapse navbar-collapse user-data" id="bs-example-navbar-collapse-1">
        <ul class="nav navbar-nav navbar-right">
          <li class="dropdown">
            <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">
              <?php echo image_tag("frontend/user.svg", array('alt' => __('User'))); ?>
              <?php echo $sf_user->getUsername()?> <span class="caret"></span>
            </a>
            <ul class="dropdown-menu">
              <li><?php echo link_to(__('Change password'), '@change_password')?></li>
              <li role="separator" class="divider"></li>
              <li><?php echo link_to(__('Logout'), '@sf_guard_signout')?></li>
            </ul>
          </li>
        </ul>
      </div><!-- /.navbar-collapse -->
    </div><!-- /.container-fluid -->
  </nav>

  <div class="container absolute">
    <?php echo $sf_content ?>
  </div>

  <footer>
    <div class="logo_footer">
      <?php echo link_to(image_tag("logo-kimkelen-footer.png", array('alt' => __('Kimkelen'))), '@homepage', array('title' => __('Inicio'))) ?>
    </div>
    © <?php echo date('Y') ?>| CeSPI - UNLP | <?php echo __('v%%number%%', array('%%number%%' => sfConfig::get('app_version_number', 1))) ?>
  </footer>

</body>
</html>
