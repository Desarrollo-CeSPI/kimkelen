<?php

abstract class gmExporter
{
  const
    FILE_EXTENSION      = null,
    MIME_TYPE           = null;

  protected
    $myContext          = null,
    $fileContentFlags   = 0;

  public function __construct($context = null)
  {
    $this->myContext = $context;
  }

  /**
   * Returns an instance of sfContext
   */
  public function getContext()
  {
    return is_null($this->myContext)? sfContext::getInstance() : $this->myContext;
  }

  /**
   * This function should save the file in the desired location
   */
  abstract public function saveFile($whereTo);

  /**
   * This function should do the exportation
   */
  abstract public function build();

  /**
   * Should return the file extension to be used
   */
  static public function getFileExtension()
  {
    return self::FILE_EXTENSION;
  }

  /**
   * Should return the mime type to be used
   */
  static public function getMimeType()
  {
    return self::MIME_TYPE;
  }

  /**
   * Returns the number of columns that the header row will have.
   *
   * @return integer
   */
  public function getHeaderCount()
  {
    return count($this->getHeaders());
  }

  /**
   * Sets the headers that will be rendered.
   * 
   * @param $headers array of gmExporterHeader
   */
  public function setHeaders($headers)
  {
    $this->headers = $headers;
  }

  /**
   * Returns the headers that will be rendered.
   *
   * @return array of gmExporterHeader
   */
  public function getHeaders()
  {
    return $this->headers;
  }

  /**
   * Sets the fields that will be rendered.
   * 
   * @param $fields array of gmExporterFields
   */
  public function setFields($fields)
  {
    $this->fields = $fields;
  }

  /**
   * Returns the fields that will be rendered.
   *
   * @return array of gmExporterFields
   */
  public function getFields()
  {
    return $fields;
  }

  public function setTitle($title)
  {
    $this->title = $title;
  }

  /**
   * Returns the title of the document.
   *
   * @param $default a default value to be used is the title has not been set
   */
  public function getTitle($default = null)
  {
    return is_null($this->title)? $default : $this->title;
  }

  /**
   * Returns the common title  for all documents
   * (located above the title and is the same for 
   * all exported documents(
   *
   * @return String or false
   */
  public function getCommonTitle()
  {
    return gmGeneratorConfiguration::getExportationCommonTitle();
  }

  /**
   * If enabled, gets date to all documents
   */
  public function getCommonDate()
  {
    if (gmGeneratorConfiguration::getExportationAppendCommonDate())
    {
      return date(gmGeneratorConfiguration::getExportationDateFormat());
    }
    return false;
  }

  /**
   * Returns the content of the created file
   *
   * @param $fileName full path to the generated file
   *
   * @return String
   */
  public function getFileContent($fileName)
  {
    return file_get_contents($fileName, $this->fileContentFlags);
  }


  /**
   * Translates an string
   *
   * @param String $text String to be translated
   *
   * @return String
   */
  protected function translate($text)
  {
    $this->getContext()->getConfiguration()->loadHelpers('I18N');
    return __($text);
  }
}
