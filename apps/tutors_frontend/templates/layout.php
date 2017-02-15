<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
  <head>
    <?php include_http_metas() ?>
    <?php include_metas() ?>
    <?php include_title() ?>

    <link rel="shortcut icon" href="/favicon.ico" />
    

  </head>
  <body>
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

    <div class="container absolute">
    	<?php echo $sf_content ?>
    </div>
    <footer>
    	© <?php echo date('Y') ?>| CeSPI - UNLP | <?php echo __('v%%number%%', array('%%number%%' => sfConfig::get('app_version_number', 1))) ?>
	</footer>
  </body>
</html>
