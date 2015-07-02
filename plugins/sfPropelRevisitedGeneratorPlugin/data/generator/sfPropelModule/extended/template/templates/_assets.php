<?php if (isset($this->params['css'])): ?>
[?php use_stylesheet('<?php echo $this->params['css'] ?>', 'first') ?]
<?php else: ?>
[?php use_stylesheet('/sfPropelRevisitedGeneratorPlugin/css/global.css', 'first') ?]
[?php use_stylesheet('/sfPropelRevisitedGeneratorPlugin/css/extended.css', 'first') ?]
[?php use_stylesheet('/sfPropelRevisitedGeneratorPlugin/css/exportation.css', 'first') ?]
<?php endif; ?>
[?php use_javascript('/sfPropelRevisitedGeneratorPlugin/js/exportation.js', 'last') ?]
