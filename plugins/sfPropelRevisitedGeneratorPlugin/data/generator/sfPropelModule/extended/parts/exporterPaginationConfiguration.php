  public function getExportationPager($model = null, $type = null)
  {
    $class = $this->getExportationPagerClass();

    return new $class($model, $this->getExportationPagerMaxPerPage($type));
  }

  public function getExportationPagerClass()
  {
    return '<?php echo isset($this->config['exportation']['pager_class']) ? $this->config['exportation']['pager_class'] : 'sfPropelPager' ?>';
<?php unset($this->config['exportation']['pager_class']) ?>
  }

  public function getExportationPagerMaxPerPage($type = 'default')
  {
    <?php if (isset($this->config['exportation']['max_per_page']) && is_numeric($this->config['exportation']['max_per_page'])): ?>
    $maxPerPageOpts = array('default' => <?php echo $this->config['exportation']['max_per_page'] ?>);
    <?php elseif (isset($this->config['exportation']['max_per_page']) && is_array($this->config['exportation']['max_per_page'])): ?>
    $maxPerPageOpts = <?php echo $this->asPhp(array_merge(array('default' => 1000), $this->config['exportation']['max_per_page'])) ?>;
    <?php else: ?>
    $maxPerPageOpts = array('default' => 1000);
    <?php endif ?>

    return isset($maxPerPageOpts[$type])? $maxPerPageOpts[$type] : $maxPerPageOpts['default'];
<?php unset($this->config['exportation']['max_per_page']) ?>
  }
