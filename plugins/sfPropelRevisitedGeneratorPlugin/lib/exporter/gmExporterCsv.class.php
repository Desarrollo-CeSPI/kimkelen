<?php

class gmExporterCsv extends gmExporter
{
  const
    FILE_EXTENSION    = 'csv',
    MIME_TYPE         = 'text/csv';

  protected
    $rowInformation   = array(),
    $myContext        = null,
    $textInRam        = "";

  public function setRowInformation($rows)
  {
    $this->rowInformation = $rows;
  }

  static public function getFileExtension()
  {
    return self::FILE_EXTENSION;
  }

  static public function getMimeType()
  {
    return self::MIME_TYPE;
  }

  public function getRowInformation()
  {
    return $this->rowInformation;
  }

  public function build()
  {
    ini_set("max_execution_time",0); 
    $this->buildCsvObject();
    $this->buildTitle();
    $this->buildHeaders($this->getHeaders());
    $this->buildRows($this->getRowInformation());
  }

  public function saveFile($whereTo)
  {
    ini_set("max_execution_time",0); 
    $handle = fopen($whereTo, "w");
    if ($handle !== false)
    {
      fwrite($handle, $this->textInRam);
      fclose($handle);
    }
  }

  protected function buildCsvObject()
  {
    $this->textInRam = '';
  }

  protected function buildTitle()
  {
  }

  protected function buildHeaders($headers)
  {
    $column = 0;
    $headerStr = "";
    foreach ($this->getHeaders() as $field)
    { 
      $this->appendColumn($field, $column);
      $column++;
    }
    $this->appendLineBreak();
  }

  protected function appendColumn($txt, $column)
  {
    $str = $column == 0? '' : ';';
    $str .= '"'.addcslashes($txt, '"').'"';
    $this->textInRam .= $str;
  }

  protected function appendLineBreak()
  {
    $this->textInRam .= "\n";
  }

  protected function buildRows($rows)
  {
    foreach ($rows as $line)
    {
      $this->buildRow($line);
      $this->appendLineBreak();
    }
  }

  protected function buildRow($row)
  {
    $column = 0;
    foreach ($row as $key => $field)
    {
      $this->appendColumn($field, $column);
      $column++;
    }
  }
}
