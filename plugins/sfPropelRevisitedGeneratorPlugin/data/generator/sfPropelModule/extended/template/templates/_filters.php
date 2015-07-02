[?php include_stylesheets_for_form($form) ?]
[?php include_javascripts_for_form($form) ?]
[?php use_helper('Javascript') ?]

<div class="sf_admin_filter">
  [?php if ($form->hasGlobalErrors()): ?]
    [?php echo $form->renderGlobalErrors() ?]
  [?php endif; ?]

  <form action="[?php echo url_for('<?php echo $this->getUrlForAction('collection') ?>', array('action' => 'filter')) ?]" method="post">
    <table cellspacing="0">
      <thead>
        <tr>
          <th colspan="2">
            [?php echo link_to_function(__('Apply filters to list', array(), 'sf_admin'), "$('sf_admin_filters_body').toggle();", array('class' => 'sf_admin_filter_toggle')) ?]
          </th>
        </tr>
      </thead>
      <tfoot>
        <tr>
          <td colspan="2">
            [?php echo $form->renderHiddenFields() ?]
            [?php echo link_to(__('Reset', array(), 'sf_admin'), '<?php echo $this->getUrlForAction('collection') ?>', array('action' => 'filter'), array('query_string' => '_reset', 'method' => 'post')) ?]
            <input type="submit" value="[?php echo __('Filter', array(), 'sf_admin') ?]" />
          </td>
        </tr>
      </tfoot>
      <tbody id="sf_admin_filters_body"[?php !$sf_user->getAttribute('<?php echo $this->getModuleName() ?>.filtering', false, 'admin_module') and print 'style="display: none"' ?]>
        [?php foreach ($configuration->getFormFilterFields($form) as $name => $field): ?]
        [?php if ((isset($form[$name]) && $form[$name]->isHidden()) || (!isset($form[$name]) && $field->isReal())) continue ?]
          [?php include_partial('<?php echo $this->getModuleName() ?>/filters_field', array(
            'name'       => $name,
            'attributes' => $field->getConfig('attributes', array()),
            'label'      => $field->getConfig('label'),
            'help'       => $field->getConfig('help'),
            'form'       => $form,
            'field'      => $field,
            'class'      => 'sf_admin_form_row sf_admin_'.strtolower($field->getType()).' sf_admin_filter_field_'.$name,
          )) ?]
        [?php endforeach; ?]
      </tbody>
    </table>
  </form>
</div>
