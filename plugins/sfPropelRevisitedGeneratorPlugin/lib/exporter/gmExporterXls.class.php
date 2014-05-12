<?php

class gmExporterXls extends gmExporter
{
  const
    FILE_EXTENSION    = 'xls',
    MIME_TYPE         = 'application/x-excel';

  protected
    $headerRow        = 1,
    $rowInformation   = array(),
    $myContext        = null,
    $excelWriter      = null,
    $excelObject      = null;

  public function __construct()
  {
    parent::__construct();
    $this->buildExcelObject();
  }

  public function resetRowCount()
  {
    $this->headerRow = 1;
  }

  public function getTitleRowNumber()
  {
  }

  public function getHeaderRowNumber()
  {
  }

  public function getCommonTitleRowNumber()
  {
  }

  public function getCommonDateRowNumber()
  {
  }

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

    $this->buildTitle();
    $this->buildHeaders($this->getHeaders());
    $this->buildRows($this->getRowInformation());
    $this->setAutosizeColumnDimensions();
  }

  public function getTitleLastRowNumber()
  {
    return $this->headerRow - 1;
  }

  public function getActiveSheet()
  {
    return $this->excelObject->getActiveSheetIndex();
  }

  public function setAutosizeColumnDimensions()
  {
    if (sfConfig::get('app_xls_autosize_columns', true))
    {
      if ($this->getTitleLastRowNumber() > 0)
      {
        foreach (range(1, $this->getTitleLastRowNumber()) as $row)
        {
          $this->excelObject->getActiveSheet()->getRowDimension($row)->setRowHeight(20);
        }
      }

      for ($i=0;$i<$this->getHeaderCount();$i++)
      {
        $column = chr(ord('A') + $i);
        $this->excelObject->getActiveSheet()->getColumnDimension($column)->setAutoSize(true);
      }
    }
  }

  public function saveFile($whereTo)
  {
    ini_set("max_execution_time",0);
    $this->excelWriter = new PHPExcel_Writer_Excel2007($this->getExcelObject());
    $this->excelWriter->save($whereTo);
  }

  public function createSheet($i)
  {
    return $this->applyDefaultSheetStyle($this->excelObject->createSheet($i));
  }

  public function setSheetTitle($number, $title)
  {
    if (!is_null($sheet = $this->excelObject->getSheet($number)))
    {
      $sheet->setTitle($title);
    }
  }

  public function getSheetCount()
  {
    return $this->excelObject->getSheetCount();
  }

  public function setActiveSheetIndex($i)
  {
    $this->excelObject->setActiveSheetIndex($i);
  }

  protected function applyDefaultSheetStyle($sheet)
  {
    $sheet->getDefaultStyle()->getFont()->setSize(sfConfig::get('app_xls_font_size', 9));
    $sheet->getDefaultStyle()->getFont()->setName(sfConfig::get('app_xls_font_name', 'Arial'));
    $sheet->getPageSetup()->setPaperSize(PHPExcel_Worksheet_PageSetup::PAPERSIZE_LETTER);
    $sheet->getPageSetup()->setPaperSize(PHPExcel_Worksheet_PageSetup::PAPERSIZE_LETTER);
    $sheet->getPageSetup()->setOrientation(sfConfig::get('app_xls_orientation_landscape')? PHPExcel_Worksheet_PageSetup::ORIENTATION_LANDSCAPE : PHPExcel_Worksheet_PageSetup::ORIENTATION_PORTRAIT);
    $sheet->getPageSetup()->setFitToPage(true);
    $sheet->getPageMargins()->setTop(sfConfig::get('app_xls_top_margin'));
    $sheet->getPageMargins()->setRight(sfConfig::get('app_xls_right_margin'));
    $sheet->getPageMargins()->setBottom(sfConfig::get('app_xls_bottom_margin'));
    $sheet->getPageMargins()->setLeft(sfConfig::get('app_xls_left_margin'));

    return $sheet;
  }

  protected function buildExcelObject()
  {
    $this->excelObject = new sfPhpExcel();
    $this->applyDefaultSheetStyle($this->excelObject->getActiveSheet());
    $this->excelObject->setActiveSheetIndex(0);
  }

  public function setExcelObject($excelObject)
  {
    $this->excelObject = $object;
  }

  public function getExcelObject()
  {
    return $this->excelObject;
  }

  protected function buildTitleFormat($order = 1)
  {
    $ret = array(
      'font'      => array(
        'bold'    => $order == 1,
        'size'    => $order < 4? 15 - $order : 11,
      ),
      'alignment'  => array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                            'vertical'   => PHPExcel_Style_Alignment::VERTICAL_CENTER,
                            'wrap'       => true),
      'borders' => array(
        'top'     => array('style' => PHPExcel_Style_Border::BORDER_THIN, 'color' => array('rgb' => 'ffffff')),
        'bottom'  => array('style' => PHPExcel_Style_Border::BORDER_THIN, 'color' => array('rgb' => 'ffffff')),
        'left'    => array('style' => PHPExcel_Style_Border::BORDER_THIN, 'color' => array('rgb' => 'ffffff')),
        'right'   => array('style' => PHPExcel_Style_Border::BORDER_THIN, 'color' => array('rgb' => 'ffffff')),
      ),
    );
    return $ret;
  }

  protected function buildGeneralFormat()
  {
    $ret = array(
      'borders' => array(
        'top'     => array('style' => PHPExcel_Style_Border::BORDER_THIN),
        'bottom'  => array('style' => PHPExcel_Style_Border::BORDER_THIN),
        'left'    => array('style' => PHPExcel_Style_Border::BORDER_THIN),
        'right'   => array('style' => PHPExcel_Style_Border::BORDER_THIN),
      ),
      'alignment'  => array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_LEFT),
    );
    return $ret;
  }

  protected function buildHeaderFormat()
  {
    $ret = array(
      'font'      => array(
          'bold'       => true,
      ),
      'alignment' => array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER),
      'borders' => array(
        'top'     => array('style' => PHPExcel_Style_Border::BORDER_MEDIUM),
        'bottom'  => array('style' => PHPExcel_Style_Border::BORDER_MEDIUM),
        'left'    => array('style' => PHPExcel_Style_Border::BORDER_MEDIUM),
        'right'   => array('style' => PHPExcel_Style_Border::BORDER_MEDIUM),
      ),
    );
    return array_merge($ret, $this->buildGeneralFormat());
  }

  protected function getTitles()
  {
    return is_array($this->getTitle())? $this->getTitle() : array($this->getTitle());
  }

  protected function buildTitle()
  {
    $titles = $this->getTitles();
    $row    = $this->headerRow;
    $order  = 1;

    foreach ($titles as $title)
    {
      $this->excelObject->getActiveSheet()->setCellValueByColumnAndRow(0, $row, $this->translate($title));
      $this->excelObject->getActiveSheet()->mergeCellsByColumnAndRow(0, $row, $this->getHeaderCount()-1, $row);
      $this->excelObject->getActiveSheet()->getStyleByColumnAndRow(0, $row)->applyFromArray($this->buildTitleFormat($order));
      $row++;
      $order++;
    }

    $this->headerRow = $row;
  }

  protected function buildHeaders($headers)
  {
    $column = 0;
    foreach ($this->getHeaders() as $field)
    {
      $this->excelObject->getActiveSheet()->setCellValueByColumnAndRow($column, $this->headerRow, $this->translate($field));
      $this->excelObject->getActiveSheet()->getStyleByColumnAndRow($column, $this->headerRow)->applyFromArray($this->buildHeaderFormat());
      $column++;
    }
  }

  protected function buildRows($rows)
  {
    $rowNumber = $this->headerRow + 1;

    foreach ($rows as $line)
    {
      $this->buildRow($line, $rowNumber);
      $rowNumber++;
    }
  }

  protected function buildRow($row, $rowNumber)
  {
    $column = 0;
    foreach ($row as $key => $field)
    {
      $this->excelObject->getActiveSheet()->setCellValueByColumnAndRow($column, $rowNumber, $field);
      $this->excelObject->getActiveSheet()->getStyleByColumnAndRow($column, $rowNumber)->applyFromArray($this->buildGeneralFormat());
      $column++;
    }
  }
}
