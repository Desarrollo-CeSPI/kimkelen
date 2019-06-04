<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */


?>

<?php use_helper('Javascript', 'Object','I18N','Form', 'Asset') ?>
<?php use_stylesheet('/sfPropelRevisitedGeneratorPlugin/css/global.css') ?>
<?php use_stylesheet('/sfPropelRevisitedGeneratorPlugin/css/extended.css') ?>

<?php include_stylesheets_for_form($form) ?>
<?php include_javascripts_for_form($form) ?>

<div id="sf_admin_container">
  <h1><?php echo __('Generate profile tutor for preceptor %preceptor%', array("%preceptor%" => $preceptor->getPerson()->getFullName())) ?></h1>
  <div id="sf_admin_content">
    <form action="<?php echo url_for('@personal_aggregate_tutor') . '?id='.$preceptor->getId()?>" method="post">

      <input type="hidden" name="preceptor_id" value="<?php echo $preceptor->getId() ?>" />
      <fieldset>
           <?php echo $form ?>
        
      </fieldset>
      
      <ul class="sf_admin_actions">
          <li><?php echo link_to(__('Back'), "@personal", array('class' => 'sf_admin_action_go_back')) ?></li>
          <li><input type="submit" value="<?php echo __('Save') ?>" /></li>
      </ul>
    </form>
  </div>
</div>

