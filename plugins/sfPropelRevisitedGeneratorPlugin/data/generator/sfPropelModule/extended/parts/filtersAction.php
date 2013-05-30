  protected function getFilters()
  {
    return $this->getUser()->getAttribute('<?php echo $this->getModuleName() ?>.filters', $this->configuration->getFilterDefaults(), 'admin_module');
  }

  protected function setFilters(array $filters, $filtering = false)
  {
    $this->getUser()->setAttribute('<?php echo $this->getModuleName() ?>.filtering', $filtering, 'admin_module');
    
    return $this->getUser()->setAttribute('<?php echo $this->getModuleName() ?>.filters', $filters, 'admin_module');
  }
