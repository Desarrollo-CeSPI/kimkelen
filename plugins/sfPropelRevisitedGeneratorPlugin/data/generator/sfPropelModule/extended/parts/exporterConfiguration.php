  public function getExportationHelperClass()
  {
    return '<?php echo isset($this->config['exportation']['helperClass'])? $this->config['exportation']['helperClass'] : $this->getModuleName()."ExporterHelper" ?>';
  }
<?php unset($this->config['exportation']['helperClass']) ?>

  public function getExportationAjaxIndicatorPath()
  {
    return '<?php echo isset($this->config['exportation']['ajaxIndicatorPath'])? $this->config['exportation']['ajaxIndicatorPath'] : '/gmGeneratorPlugin/images/ajax-loader.gif' ?>';
  }
<?php unset($this->config['exportation']['ajaxIndicatorPath']) ?>

  public function getExportationHelperUserClass()
  {
    return '<?php echo isset($this->config['exportation']['helperUserClass'])? $this->config['exportation']['helperUserClass'] : $this->getModuleName()."ExporterHelperUser" ?>';
  }
<?php unset($this->config['exportation']['helperUserClass']) ?>

  public function getExportationForm($defaults = array(), $options = array())
  {
    $formClass = '<?php echo isset($this->config['exportation']['userExportationForm'])? $this->config['exportation']['userExportationForm'] : $this->getModuleName().'ExporterForm' ?>';
    return new $formClass(array(), array_merge(array('fields' => $this->getExportationFieldSelectionDecorators(), 'title' => $this->getExportationTitle(), 'type' => $this->getExportationType(), 'allowUserTypeSelection' => $this->getExportationAllowUserTypeSelection()), $options));
  }

  public function getExportationAllowUserTypeSelection()
  {
    return <?php echo (isset($this->config['exportation']['allowUserTypeSelection']) && $this->config['exportation']['allowUserTypeSelection'])? 'true' : 'false' ?>;
  }
<?php unset($this->config['exportation']['allowUserTypeSelection']) ?>

  public function getExportationFieldSelection()
  {
    return <?php echo $this->asPhp(isset($this->config['exportation']['fieldSelection'])? $this->config['exportation']['fieldSelection'] : array(array('label' => 'Object', 'decorator' => 'pass'))) ?>;
  }
<?php unset($this->config['exportation']['fieldSelection']) ?>

  public function getExportationFieldSelectionDecorators()
  {
    $fields = array();
    foreach ($this->getExportationFieldSelection() as $id => $f)
    {
      $fields[$id] = gmExporterFieldDecorator::getInstance($f);
    }
    return $fields;
  }
<?php unset($this->config['exportation']['fieldSelection']) ?>

  public function getExportationHeaders()
  {
    $headers = array();
    foreach ($this->getExportationFieldSelection() as $f)
    {
      $f = gmExporterFieldDecorator::getInstance($f);
      $headers[] = $f->getLabel();
    }
    return $headers;
  }

  public function getExportationTitle()
  {
    return "<?php echo isset($this->config['exportation']['title'])? $this->config['exportation']['title'] : 'Report' ?>";
  }
<?php unset($this->config['exportation']['title']) ?>

  public function getExportationDefaultType()
  {
    return gmExporterTypes::EXPORT_TYPE_XLS;
  }

  public function getExportationType()
  {
    <?php if (isset($this->config['exportation']['type'])): ?>
    return "<?php echo $this->config['exportation']['type'] ?>";
    <?php else: ?>
    return $this->getExportationDefaultType();
    <?php endif ?>
  }
<?php unset($this->config['exportation']['type']) ?>

  public function getExportationSavePath()
  {
    return "/tmp/";
  }

  public function getExportationFileExtension($type = null)
  {
    $type = is_null($type)? $this->getExportationType() : $type;
    return gmExporterTypes::getFileExtension($type);
  }

  public function getExportationFileName($extension = null)
  {
    $extension = is_null($extension)? $this->getExportationFileExtension() : $extension;
    return 'report.'.$extension;
  }

  public function getExportationMimeType($type = null)
  {
    $type = is_null($type)? $this->getExportationType() : $type;
    return gmExporterTypes::getMimeType($type);
  }
