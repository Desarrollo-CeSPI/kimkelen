  protected function getExportationPager($type = null, $page = null)
  {
    $pager = $this->configuration->getExportationPager('<?php echo $this->getModelClass() ?>', $type);
    $pager->setCriteria($this->buildExportationCriteria());
    $pager->setPage($this->getExportationPage());
    $pager->setPeerMethod($this->configuration->getPeerMethod());
    $pager->setPeerCountMethod($this->configuration->getPeerCountMethod());

    if (!is_null($page)) $pager->setPage($page);
    $pager->init();

    return $pager;
  }

  protected function setExportationPage($page)
  {
    $this->getUser()->setAttribute('<?php echo $this->getModuleName() ?>.exportation_page', $page, 'admin_module');
  }

  protected function getExportationPage()
  {
    return $this->getUser()->getAttribute('<?php echo $this->getModuleName() ?>.exportation_page', 1, 'admin_module');
  }

  protected function buildExportationCriteria()
  {
    return $this->buildCriteria();
  }
