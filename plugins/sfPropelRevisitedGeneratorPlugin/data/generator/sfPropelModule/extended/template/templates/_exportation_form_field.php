<div class="[?php echo $class ?][?php $form[$name]->hasError() and print ' errors' ?]">
  [?php $escapedField = $form[$name] instanceOf sfOutputEscaper? $form[$name]->getRawValue() : $form[$name] ?]
  [?php if (!$escapedField instanceOf sfFormFieldSchema) echo $form[$name]->renderError() ?]

  <div [?php $escapedField instanceOf sfFormFieldSchema and print 'id="embedded_'.$name.'"'?]>
    [?php echo $form[$name]->renderLabel(null, array('class' => ($form->getValidator($name) && $form->getValidator($name)->getOption('required')) ? 'required' : '')) ?]

    <div [?php $escapedField instanceOf sfFormFieldSchema and print 'id="embedded_internal_'.$name.'"'?]>
      [?php echo $form[$name]->render() ?]
    </div>

    [?php if ($help || $help = $form[$name]->renderHelp()): ?]
      <div class="help">[?php echo __($help, array(), '<?php echo $this->getI18nCatalogue() ?>') ?]</div>
    [?php endif; ?]
  </div>
  <div style="margin-top: 1px; clear: both"></div>
</div>
