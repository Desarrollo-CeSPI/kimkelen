<?php

class gmExporterHelper
{
  protected 
    $myConfiguration = null,
    $myContext       = null,
    $rowObjects      = null,
    $class           = null,
    $exporterInstance = null,
    $useSheets       = false,
    $sheetTitles     = array(),
    $type            = null;

  /**
   * 
   *
   * @param $configuration        generator's configuration
   * @param $objects              a set of objects to fetch information from
   * @param $options              an array of options to configure the exportation. The available options are:
   *                                + type:       see gmExporterTypes
   *                                + class:      an exporter class to use instead of the defaults defined in gmExporterTypes
   *                                + context:    an sfContext instance
   */
  public function __construct($configuration, $objects, $options = array())
  {
    $this->rowObjects       = $objects;
    $this->myConfiguration  = $configuration;

    $this->parseOptions($options);
  }

  /*
   * parseOptions
   *
   * Parses the options of the exporter helper.
   *
   * @param array $options
   */
  protected function parseOptions($options)
  {
    $this->type        = isset($options['type'])? $options['type'] : gmExporterTypes::EXPORT_TYPE_XLS;
    $this->myContext   = isset($options['context'])? $options['context'] : sfContext::getInstance();
    $this->useSheets   = isset($options['use_sheets'])? $options['use_sheets'] : false;
    $this->sheetTitles = isset($options['sheet_titles'])? $options['sheet_titles'] : array();
  }

  protected function getExporterSubclassPrefix()
  {
    return 'gm';
  }

  /**
   * Returns the exporter subclass name
   *
   * @return string exporter subclass name
   */
  protected function getExporterSubclassName()
  {
    return gmExporterTypes::getClassForType($this->type, $this->getExporterSubclassPrefix());
  }

  /**
   * Returns the exporter subclass instance
   *
   * @return gmExporter subclass
   */
  protected function getExporterSubclass()
  {
    $klass = $this->getExporterSubclassName();
    return new $klass();
  }

  /**
   * Returns a sfContext instance
   *
   * @return sfContext
   */
  protected function getContext()
  {
    return $this->myContext;
  }

  /**
   * Returns the generator's configuration instance
   *
   * @return The generator's configuration object (do not recall the class name and I shall not look it up!)
   */
  protected function getConfiguration()
  {
    return $this->myConfiguration;
  }

  /**
   * Return the objects that should be used to fetch data from
   *
   * @return array Object
   */
  protected function getRowObjects($sheet = null)
  {
    return $this->useSheets && !is_null($sheet)? $this->rowObjects[$sheet] : $this->rowObjects;
  }

  /**
   * Return an array of strings that will be used to render headers
   *
   * @return array An array of strings
   */
  protected function getHeaders()
  {
    return $this->getConfiguration()->getExportationHeaders();
  }

  protected function getTitle($pageNumber=null)
  {
    return $this->getConfiguration()->getExportationTitle();
  }

  /**
   * Use this to get an abstraction of the user's field configuration 
   *
   * @return an array of gmExporterFieldDecorators
   */
  protected function getFieldSelection()
  {
    $fields = array();
    foreach ($this->getConfiguration()->getExportationFieldSelection() as $field)
    {
      $fields[] = gmExporterFieldDecorator::getInstance($field, $this->getContext());
    }
    return $fields;
  }

  /**
   * Formats a row
   *
   * @param Object $object
   *
   * @return array an array with the corresponding object's row information
   */
  protected function decorateRowInformation($object)
  {
    $row = array();
    if (!empty($object))
    {
      foreach ($this->getFieldSelection() as $fieldDecorator)
      {
        $row[] = $fieldDecorator->render($object);
      }
    }
    return $row;
  }


  /**
   * Returns the row information ready to be passed to the exporter subclass
   *
   * @return array
   */
  public function getRowInformation($sheet = null)
  {
    if (!$this->useSheets)
    {
      $rowInformation = array();
      foreach ($this->getRowObjects() as $object)
      {
        $rowInformation[] = $this->decorateRowInformation($object);
      }
    }
    else
    {
      $rowInformation = array();
      foreach ($this->getRowObjects($sheet) as $object)
      {
        $rowInformation[] = $this->decorateRowInformation($object);
      }
    }
    return $rowInformation;
  }

  public function getSheetCount()
  {
    return $this->useSheets? count($this->rowObjects) : 1;
  }

  /**
   * This method builds the exporting object with all the data in it!
   */
  public function build($title = null, $headers = null, $rowInformation = null)
  {
    for ($i=0;$i<$this->getSheetCount();$i++)
    {
      $this->buildSheet($title, $headers, $rowInformation, $i);
    }
    $this->exporterInstance->setActiveSheetIndex(0);
  }

  public function getActiveSheet()
  {
    return is_null($this->exporterInstance)? 0 : $this->exporterInstance->getActiveSheet();
  }

  public function buildSheet($title = null, $headers = null, $rowInformation = null, $pageNumber = 0)
  {
    $title          = is_null($title)? $this->getTitle($pageNumber) : $title;
    $headers        = is_null($headers)? $this->getHeaders() : $headers;
    $rowInformation = is_null($rowInformation)? $this->getRowInformation($pageNumber) : $rowInformation;

    $this->exporterInstance = is_null($this->exporterInstance)? $this->getExporterSubclass() : $this->exporterInstance;

    $this->exporterInstance->createSheet($pageNumber);
    $this->exporterInstance->setActiveSheetIndex($pageNumber);
    $this->setSheetTitle($pageNumber);
    $this->exporterInstance->setTitle($title);
    $this->exporterInstance->setHeaders($headers);
    $this->exporterInstance->setRowInformation($rowInformation);

    $this->exporterInstance->resetRowCount();
    $this->exporterInstance->build();
  }

  public function setSheetTitle($number)
  {
    if (!is_null($title = $this->getSheetTitle($number)))
    {
      $this->exporterInstance->setSheetTitle($number, $title);
    }
  }

  public function getSheetTitle($number)
  {
    return isset($this->sheetTitles[$number])? $this->sheetTitles[$number] : null;
  }

  /**
   * This method saves the file in some location to be rendered afterwards
   *
   * @param $whereTo self explicatory path
   */
  public function saveFile($whereTo)
  {
    $this->exporterInstance->saveFile($whereTo);
    $this->savedFilePath = $whereTo;
  }

  /**
   *
   */
  public function deleteFile($path = null)
  {
    $path= is_null($path)? $this->savedFilePath : $path;
    unlink($path);
    $this->savedFilePath=null;
  }

  /**
   *
   */
  public function getFileContents($fromWhere = null)
  {
    $fromWhere = is_null($fromWhere)? $this->savedFilePath : $fromWhere;
    return file_get_contents($fromWhere);
  }

  /**
   *
   */
  public function freeMem()
  {
    foreach ($this->getRowObjects() as $o)
    {
      if (method_exists($o, 'clearAllReferences')) $o->clearAllReferences();
      unset($o);
    }
    $this->rowObjects = array();
  }
}
