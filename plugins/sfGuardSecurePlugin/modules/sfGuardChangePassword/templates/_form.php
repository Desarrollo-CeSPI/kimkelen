<?php include_stylesheets_for_form($form) ?>
<?php include_javascripts_for_form($form) ?>

<div class="sf_admin_form">
  <?php echo form_tag('@sf_guard_change_password'); ?>
    <?php echo $form->renderHiddenFields() ?>
    <?php if ($form->hasGlobalErrors()): ?>
      <?php echo $form->renderGlobalErrors() ?>
    <?php endif; ?>

    <?php foreach (array('password', 'password_new', 'password_new_bis')  as $name): ?>
    <div class="sf_admin_form_row <?php $form[$name]->hasError() and print ' errors' ?>">
      <?php echo $form[$name]->renderError() ?>
      <div>
        <?php echo $form[$name]->renderLabel() ?>
        <div class="content">
          <?php echo $form[$name]->render() ?>
        </div>
      </div>
    </div>
    <?php endforeach ?>

    <ul class="sf_admin_actions">
      <li class="sf_admin_action_confirm"><input type="submit" value="<?php echo __('confirm')?>" name="_accept" /></li>
    </ul>

  </form>
</div>
