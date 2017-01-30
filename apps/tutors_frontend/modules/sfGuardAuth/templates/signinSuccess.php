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
<?php use_stylesheet('/bootstrap/css/bootstrap.min.css', 'first') ?>
<?php use_stylesheet('/frontend/css/frontend-login.css', 'first') ?>
<div class="container">
	<div class="row">
	    <div class ="col-md-4"></div>
	    <div class ="col-md-4">
	    	<div class="container-image">
	    			<?php echo image_tag("/frontend/images/logo-kimkelen-blanco.png", array('alt' => __('Kimkelen'))); ?>
	    	</div>
	    	<div class="container-login">
	    		<form class="form-signin" action="<?php echo url_for('@sf_guard_signin') ?>" method="post">
				      <?php echo $form ?>
			        <button class="btn btn btn-success pull-right green" type="submit"> <?php echo __('Sign in') ?></button> 
	    		</form>
	    	</div>
	    	<div class="container-footer">
	    		© 2009 - <?php echo date('Y') ?>| CeSPI - UNLP | <?php echo __('versión %%number%%', array('%%number%%' => sfConfig::get('app_version_number', 1))) ?>
			   	<div class="logo_footer">
			       	<?php echo image_tag("/frontend/images/logo-kimkelen-footer-blanco.png", array('alt' => __('Kimkelen'))); ?>
			    </div>
		 	</div>	
	    </div>
	    <div class ="col-md-4"></div>
    </div>
</div>
