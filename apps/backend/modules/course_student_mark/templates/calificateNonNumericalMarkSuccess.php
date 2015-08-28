<?php use_stylesheet('/sfPropelRevisitedGeneratorPlugin/css/global.css', 'first') ?>
<?php use_stylesheet('/sfPropelRevisitedGeneratorPlugin/css/extended.css', 'first') ?>
<?php include_stylesheets_for_form($form) ?>
<?php include_javascripts_for_form($form) ?>

<div id="sf_admin_container">
  <h1><?php echo __('Choose student to calificate without numerical mark from %course%', array('%course%' => $course)) ?></h1>
  <div class="sf_admin_form">
    <form action="<?php echo url_for('@save_calificate_non_numerical_mark') ?>" method="post">
      <?php echo $form->renderHiddenFields() ?>
      <?php if ($form->hasGlobalErrors()): ?>
        <?php echo $form->renderGlobalErrors() ?>
      <?php endif; ?>
      <?php echo $form; ?>

      <ul class="sf_admin_actions">
        <li class ="sf_admin_action_list"><?php echo link_to(__('Back'), $back_url); ?></li>
        <li ><input type="submit" value="<?php echo __('Save', array(), 'sf_admin') ?>" /></li>
      </ul>
    </form>
  </div>
</div>