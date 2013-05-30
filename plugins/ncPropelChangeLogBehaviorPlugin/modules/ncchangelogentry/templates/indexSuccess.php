<?php use_helper('I18N', 'Javascript') ?>
<?php use_javascript('/sfProtoculousPlugin/js/prototype.js', 'last') ?>
<?php use_javascript('/sfProtoculousPlugin/js/effects.js', 'last') ?>

<div id="nc_change_log_container">
  <h1><?php echo __('Changelog of "%object%" of class "%class%"', array('%object%' => $object->__toString(), '%class%' => $class_name ), 'nc_change_log_behavior') ?></h1>

  <div id="nc_change_log_content">
    <?php include_partial('ncchangelogentry/list', array('id' => null, 'nc_change_log_entries' => $nc_change_log_entries)) ?>
    <?php if (count($related_nc_change_log_entries) > 0): ?>
      <h1><?php echo __('Changes in foreign objects referenced by object "%%object%%" of class "%%class%%"', array('%%class%%' => $class_name, '%%object%%' => $object->__toString()), 'nc_change_log_behavior')?></h1>
      <div class="nc_change_log_related_entries">
        <?php foreach ($related_nc_change_log_entries as $relatedFieldName => $entries): ?>
          <?php $relatedFieldName = sfInflector::underscore($relatedFieldName) ?>
          <h2>
            <?php echo link_to_function('['.__('show', null, 'nc_change_log_behavior').']', "Effect.Appear('$relatedFieldName'); $('${relatedFieldName}_toggler').hide()", array('id' => $relatedFieldName."_toggler", 'class' => 'nc_change_log_toggler')) ?>
            <?php echo __('Changelog of field "%field%"', array('%field%' => $nc_change_log_entries[0]->translate(sfInflector::underscore($relatedFieldName))), 'nc_change_log_behavior') ?>
          </h2>
          <?php include_partial('ncchangelogentry/list', array('id' => $relatedFieldName, 'nc_change_log_entries' => $entries)) ?>
        <?php endforeach ?>
      </div>
    <?php endif ?>

    <?php if (count($nn_related_nc_change_log_entries) > 0): ?>
      <h1><?php echo __('Changes in objects that reference to object "%%object%%" of class "%%class%%"', array('%%class%%' => $class_name, '%%object%%' => $object->__toString()), 'nc_change_log_behavior')?></h2>
      <div class="nc_change_log_related_entries">
        <div class="nc_change_log_related_entries">
          <?php $i=0; foreach ($nn_related_nc_change_log_entries as $referencingTableName => $elements): ?>
            <?php $elements = $elements instanceOf sfOutputEscaper? $elements->getRawValue() : $elements; ?>
            <?php $indexes = array_keys($elements) ?>
            <h2><?php count($elements) > 0 and print $elements[$indexes[0]][0]->renderClassName() ?></h2>
            <div class="nc_change_log_related_entries">
              <?php foreach ($elements as $title => $entries): ?>
                <?php $id = $referencingTableName.'_'.$i ?>
                <h3>
                  <?php echo link_to_function('['.__('show', null, 'nc_change_log_behavior').']', "Effect.Appear('$id'); $('${id}_toggler').hide()", array('id' => $id."_toggler", 'class' => 'nc_change_log_toggler')) ?>
                  <?php echo __('Changelog of referencing object "%object%"', array('%object%' => $title), 'nc_change_log_behavior') ?>
                </h3>
                <?php include_partial('ncchangelogentry/list', array('id' => $id, 'nc_change_log_entries' => $entries)) ?>
                <?php $i++; ?>
              <?php endforeach ?>
            </div>
          <?php endforeach ?>
        </div>
      </div>
    <?php endif ?>
  </div>
</div>
