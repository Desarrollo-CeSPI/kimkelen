<?php

class gmExporterHelperUser extends gmExporterHelper
{
  protected 
    $exportationForm = null;

  /**
   * 
   *
   * @param $configuration        generator's configuration
   * @param $objects              a set of objects to fetch information from
   * @param $options              an array of options to configure the exportation. The available options are:
   *                                + type:       xls|csv|html
   */
  public function __construct($configuration, $objects, $options = array(), $exportationForm = null)
  {
    parent::__construct($configuration, $objects, $options);

    if (is_null($exportationForm))
    {
      throw new Exception('gmExporterHelperUser requires an exportationForm instance');
    }

    $this->exportationForm = $exportationForm;
  }

  protected function getHeaders()
  {
    $headers = array();
    foreach ($this->exportationForm->getExportationFieldSelectionDecorators() as $fieldDecorator)
    {
      $headers[] = $fieldDecorator->getLabel();
    }
    return $headers;
  }

  protected function getTitle($pageNumber=null)
  {
    $title = $this->exportationForm->getExportationTitle();
    return empty($title)? parent::getTitle() : $title;
  }

  /**
   * Use this to get an abstraction of the user's field configuration 
   *
   * @return an array of gmExporterFieldDecorators
   */
  protected function getFieldSelection()
  {
    return $this->exportationForm->getExportationFieldSelectionDecorators();
  }
}
