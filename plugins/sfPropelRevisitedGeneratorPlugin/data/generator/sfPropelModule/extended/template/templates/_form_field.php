[?php if ($field->isPartial()): ?]
  [?php include_partial('<?php echo $this->getModuleName() ?>/'.$name, array('form' => $form, 'attributes' => $attributes instanceof sfOutputEscaper ? $attributes->getRawValue() : $attributes)) ?]
[?php elseif ($field->isComponent()): ?]
  [?php include_component('<?php echo $this->getModuleName() ?>', $name, array('form' => $form, 'attributes' => $attributes instanceof sfOutputEscaper ? $attributes->getRawValue() : $attributes)) ?]
[?php else: ?]
<div class="[?php echo $class ?][?php $form[$name]->hasError() and print ' errors' ?]">
    [?php echo $form[$name]->renderError() ?]
    <div>
      [?php echo $form[$name]->renderLabel($label, array('class' => ($form->getValidator($name) && $form->getValidator($name)->getOption('required')) ? 'required' : '')) ?]

      [?php $attributes = $attributes instanceof sfOutputEscaper ? $attributes->getRawValue() : $attributes ?]
      [?php if ($form->getValidator($name) && $form->getValidator($name)->getOption('required')): ?]
        [?php $attributes['class'] = (isset($attributes['class']) ? $attributes['class'] . ' required' : 'required') ?]
      [?php endif; ?]
      
      [?php echo $form[$name]->render($attributes) ?]

      [?php if ($help || $help = $form[$name]->renderHelp()): ?]
        <div class="help">[?php echo __($help, array(), '<?php echo $this->getI18nCatalogue() ?>') ?]</div>
      [?php endif; ?]
    </div>
    <div style="margin-top: 1px; clear: both"></div>
  </div>
[?php endif; ?]

[?php //($form->getValidator($name) && $form->getValidator($name)->getOption('required')) and print ' required' ?]