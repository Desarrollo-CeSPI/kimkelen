<?php 
/*
 * Kimkëlen - School Management Software
 * Copyright (C) 2013 CeSPI - UNLP <desarrollo@cespi.unlp.edu.ar>
 *
 * This file is part of Kimkëlen.
 *
 * Kimkëlen is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License v2.0 as published by
 * the Free Software Foundation.
 *
 * Kimkëlen is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with Kimkëlen.  If not, see <http://www.gnu.org/licenses/gpl-2.0.html>.
 */ ?>
<?php

class ReportRendererXls extends BaseReportRenderer
{
  public function __construct()
  {
    $this->rowIndex = 1;
    $this->renderObject = new sfPhpExcel();
    $this->configureRenderObjectDefaults();
  }

  public function getRenderObject()
  {
    return $this->renderObject;
  }

  public function configureRenderObjectDefaults()
  {
    $this->getRenderObject()->setActiveSheetIndex(0);
    $this->applyDefaultSheetStyle($this->getRenderObject()->getActiveSheet());
  }

  protected function applyDefaultSheetStyle($sheet)
  {
    $sheet->getDefaultStyle()->getFont()->setSize(ReportRendererXlsConfiguration::getFontSize());
    $sheet->getDefaultStyle()->getFont()->setName(ReportRendererXlsConfiguration::getFontName());
    $sheet->getPageSetup()->setPaperSize(ReportRendererXlsConfiguration::getPaperSize());
    $sheet->getPageSetup()->setOrientation(ReportRendererXlsConfiguration::getOrientation());
    $sheet->getPageSetup()->setFitToWidth(ReportRendererXlsConfiguration::getFitToPage());
    $sheet->getPageSetup()->setFitToHeight(false);
    $sheet->getPageSetup()->setHorizontalCentered(true);
    $sheet->getPageMargins()->setTop(ReportRendererXlsConfiguration::getTopMargin());
    $sheet->getPageMargins()->setRight(ReportRendererXlsConfiguration::getRightMargin());
    $sheet->getPageMargins()->setBottom(ReportRendererXlsConfiguration::getBottomMargin());
    $sheet->getPageMargins()->setLeft(ReportRendererXlsConfiguration::getLeftMargin());

    return $sheet;
  }
  
  public function renderBlankRow()
  {
    $this->renderRow(array(array(
        'content' => '',
        'borders' => array(
            'top'     => array('style' => PHPExcel_Style_Border::BORDER_NONE),
            'bottom'  => array('style' => PHPExcel_Style_Border::BORDER_NONE),
            'left'    => array('style' => PHPExcel_Style_Border::BORDER_NONE),
            'right'   => array('style' => PHPExcel_Style_Border::BORDER_NONE),
          ),
    )));
  }
  
  public function renderRow($data)
  {
    $data = is_array($data)? $data : array($data);

    foreach ($data as $i => $cell)
    {
    
      $cell = $this->fillDefaults($cell, $i);
      $this->parseDynamicFields($cell);

      $this->renderCell($cell);
    }

    $this->rowIndex++;
  }

  public function parseDynamicFields(&$cell)
  {
    $cell['content'] = str_replace(
      array(
        '%%column%%',
        '%%row%%',
      ),
      array(
        $cell['column_start'],
        $cell['row_start']
      ),
      $cell['content']
    );
  }

  public function fillDefaults($cell, $i = 0)
  {
    $cell = is_array($cell)? $cell : array('content' => is_null($cell)? '' : $cell);
    if(!isset($cell['content'])) { throw new Exception('aaa'); };
    $ret = array(
      'content'       => $cell['content'],
      'size'          => isset($cell['size'])? $cell['size'] : BaseReportRenderer::FONT_SIZE_NORMAL,
      'style'         => isset($cell['style'])? $cell['style'] : null,
      'column_start'  => isset($cell['column_start'])? $cell['column_start'] : $i,
      'column_end'    => isset($cell['column_end'])? $cell['column_end'] : null,
      'row_start'     => isset($cell['row_start'])? $cell['row_start'] : $this->rowIndex,
      'row_end'       => isset($cell['row_end'])? $cell['row_end'] : null,
      'borders'       => isset($cell['borders'])
        ? $cell['borders']
        : array(
            'top'     => array('style' => PHPExcel_Style_Border::BORDER_THIN, 'color' => array('rgb' => '000000')),
            'bottom'  => array('style' => PHPExcel_Style_Border::BORDER_THIN, 'color' => array('rgb' => '000000')),
            'left'    => array('style' => PHPExcel_Style_Border::BORDER_THIN, 'color' => array('rgb' => '000000')),
            'right'   => array('style' => PHPExcel_Style_Border::BORDER_THIN, 'color' => array('rgb' => '000000')),
          ),
    );

    return $ret;
  }

  public function renderCell($data)
  {

    $this->getRenderObject()->getActiveSheet()->setCellValueByColumnAndRow($data['column_start'], $data['row_start'], $data['content']);

    if (!is_null($data['row_end']) || !is_null($data['column_end']))
    {     
      $this->getRenderObject()->getActiveSheet()->mergeCellsByColumnAndRow(
        $data['column_start'],
        $data['row_start'],
        is_null($data['column_end'])? $data['column_start'] : $data['column_end'],
        is_null($data['row_end'])? $data['row_start'] : $data['row_end']
      );
    }

    $this->applyStyle($data);
//    $this->applyMergedCellStyle($row, 1, $number, $this->buildPageContextFormat());
  }

  public function applyStyle($data)
  {    
    if (is_null($data['column_end']))
    {
      $xy = $data['column_start'];
    }
    else
    {
      $columnL = chr(ord('A') + $data['column_start']);
      $columnR = chr(ord('A') + $data['column_end']);

      $xy = $columnL.$data['row_start'].":".$columnR.$data['row_start'];
    }

    $styleArray = array();

    if ($data['style'] & self::STYLE_BOLD)
    {
      $styleArray['font'] = isset($styleArray['font'])
        ? array_merge($styleArray['font'], array('bold' => true))
        : array('bold' => true);
    }

    if (isset($data['wrap']) && $data['wrap'])
    {
      $styleArray['wrap'] = true;
    }

    if (isset($data['shrink']) && $data['shrink'])
    {
      $styleArray['shrinkToFit'] = true;
    }
    
    if (isset($data['borders']))
    {
      $styleArray['borders'] = $data['borders'];
    }

    if ($data['style'] & self::STYLE_ITALIC)
    {
      $styleArray['font'] = isset($styleArray['font'])
        ? array_merge($styleArray['font'], array('italic' => true))
        : array('italic' => true);
    }

    if ($data['style'] & self::STYLE_UNDERLINE)
    {
      $styleArray['font'] = isset($styleArray['font'])
        ? array_merge($styleArray['font'], array('underline' => PHPExcel_Style_Font::UNDERLINE_SINGLE))
        : array('underline' => PHPExcel_Style_Font::UNDERLINE_SINGLE);
    }

    if ($data['style'] & self::STYLE_CENTERED)
    {
      $styleArray['alignment'] = isset($styleArray['alignment'])
        ? array_merge($styleArray['alignment'], array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER))
        : array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
    }

    if ($data['style'] & self::STYLE_RIGHTED)
    {
      $styleArray['alignment'] = isset($styleArray['alignment'])
        ? array_merge($styleArray['alignment'], array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_RIGHT))
        : array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
    }

    if ($data['style'] & self::STYLE_LEFTED)
    {
      $styleArray['alignment'] = isset($styleArray['alignment'])
        ? array_merge($styleArray['alignment'], array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_LEFT))
        : array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
    }

    if ($data['style'] & self::STYLE_VERTICAL_BOTTOM)
    {
      $styleArray['alignment'] = isset($styleArray['vertical_alignment'])
        ? array_merge($styleArray['alignment'], array('vertical' => PHPExcel_Style_Alignment::VERTICAL_BOTTOM))
        : array('vertical' => PHPExcel_Style_Alignment::VERTICAL_BOTTOM);
    }
    
    if ($data['style'] & self::STYLE_VERTICAL_CENTER)
    {
      $styleArray['alignment'] = isset($styleArray['alignment'])
        ? array_merge($styleArray['alignment'], array('vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER))
        : array('vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER);
    }
    
    if ($data['style'] & self::STYLE_VERTICAL_TOP)
    {
      $styleArray['alignment'] = isset($styleArray['alignment'])
        ? array_merge($styleArray['alignment'], array('vertical' => PHPExcel_Style_Alignment::VERTICAL_TOP))
        : array('vertical' => PHPExcel_Style_Alignment::VERTICAL_TOP);
    }
    
    $styleArray['font'] = isset($styleArray['font'])
      ? array_merge($styleArray['font'], array('size' => $data['size']))
      : array('size' => $data['size']);

    $this->getRenderObject()->getActiveSheet()->getStyleByColumnAndRow($xy, $data['row_start'])->applyFromArray($styleArray);
  }

  public function renderContent()
  {
    ini_set("max_execution_time", 0);

    $fn = ReportRendererXlsConfiguration::getTempDir().DIRECTORY_SEPARATOR.time().rand().'_rand.xls';

    $excelWriter = new PHPExcel_Writer_Excel5($this->getRenderObject());
    $excelWriter->save($fn);

    $contents = file_get_contents($fn);
    unlink($fn);

    return $contents;
  }

  public function getMimeType()
  {
    return 'application/x-excel';
  }

  public function getHtmlHeaders()
  {
    return array(
      'Pragma'              => 'public',
      'Content-type'        => $this->getMimeType().'; charset=UTF-8',
      'Cache-Control'       => ' maxage=3600',
      'Content-Disposition' => ' attachment; filename="reporte.xls"',
    );
  }

  public function setAutosizeColumnDimensions($to, $from = 0)
  {
    /*
    $this->getRenderObject()->getActiveSheet()->getRowDimension(self::TITLE_ROW_NUMBER)->setRowHeight(20);
    $this->getRenderObject()->getActiveSheet()->getRowDimension(self::COMMON_TITLE_ROW_NUMBER)->setRowHeight(25);
    */
    for ($i=$from;$i<$to;$i++)
    {
      $column = chr(ord('A') + $i);
      $this->getRenderObject()->getActiveSheet()->getColumnDimension($column)->setAutoSize(true);
    }
  }
}