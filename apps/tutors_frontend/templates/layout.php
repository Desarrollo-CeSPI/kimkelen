<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
  <head>
    <?php include_http_metas() ?>
    <?php include_metas() ?>
    <?php include_title() ?>
    <link rel="shortcut icon" href="/favicon.ico" />
    
    <?php use_stylesheet('/frontend/css/main.css', 'first') ?>
   	<?php use_stylesheet('/bootstrap/css/bootstrap.min.css', 'first') ?>
	<?php use_javascript('/frontend/js/scripts.js', 'last') ?>
	<?php use_javascript('/frontend/js/jquery-3.1.1.js') ?>
	<?php use_javascript('/frontend/js/scripts.js','last') ?>
    <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,400,400i,600,600i,700,700i,800i" rel="stylesheet">
    <script src="http://ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js"></script>
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
                <?php echo link_to(image_tag("kimkelen_logo.png", array('alt' => __('Kimkelen'))), '@homepage', array('title' => __('Inicio'))) ?>
              </div>
              
              <div class="collapse navbar-collapse user-data" id="bs-example-navbar-collapse-1">
                  <ul class="nav navbar-nav navbar-right">
                      <li class="dropdown">
                          <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">
                              <?php echo image_tag("/frontend/images/user.svg", array('alt' => __('User'))); ?>
                              <?php echo $sf_user->getUsername()?> <span class="caret"></span>
                          </a>
                          <ul class="dropdown-menu">
                              <li><a href="#">Cambiar contraseña</a></li>
                              <li role="separator" class="divider"></li>
                              <li><?php echo link_to(__('Logout'), '@sf_guard_signout')?></li>
                          </ul>
                      </li>
                  </ul>
              </div><!-- /.navbar-collapse -->
          </div><!-- /.container-fluid -->
      </nav>

  <!--
    <header class="green-header">
		<div class="header-image">
			<?php echo image_tag("/frontend/images/logo-kimkelen-blanco.png", array('alt' => __('Kimkelen'))); ?>
		</div>
	</header>
	<?php $students = TutorPeer::retrieveByUsername($sf_user->getUsername())->getStudentsArray(); ?>
	<div>
		<nav id="menu">
			<li id="nav_mobile"> Menú</li>

			<div id="oculto">
				<li><?php echo link_to('Inicio','@homepage') ?></li>
				<li>Estudiantes a cargo <span class="glyphicon glyphicon-triangle-bottom min" aria-hidden="true"></span>
					<ul class="list">
						<?php foreach ($students as $s) : ?>
							<li><?php echo link_to(__($s), 'student/index?student_id=' . $s->getId() )?></li></br>
						<?php endforeach ?>
	    			</ul>
				</li>
				<?php if ($sf_user->isAuthenticated()): ?>
				<li class="user"><?php echo $sf_user->getUsername()?> <span class="glyphicon glyphicon-triangle-bottom min" aria-hidden="true"></span> <?php echo image_tag("/frontend/images/user.png", array('alt' => __('User'))); ?>
					<ul class="list">
		        		<li><?php echo link_to(__('Salir'), '@sf_guard_signout')?></li>

		    		</ul>
				</li>
				<?php endif ?>
			</div>
		</nav>
	</div>
-->
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
