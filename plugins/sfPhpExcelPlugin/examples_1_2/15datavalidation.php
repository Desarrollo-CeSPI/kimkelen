<?php
/**
 * PHPExcel
 *
 * Copyright (C) 2006 - 2009 PHPExcel
 *
 * This library is free software; you can redistribute it and/or
 * modify it under the terms of the GNU Lesser General Public
 * License as published by the Free Software Foundation; either
 * version 2.1 of the License, or (at your option) any later version.
 * 
 * This library is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU
 * Lesser General Public License for more details.
 * 
 * You should have received a copy of the GNU Lesser General Public
 * License along with this library; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301  USA
 *
 * @category   PHPExcel
 * @package    PHPExcel
 * @copyright  Copyright (c) 2006 - 2009 PHPExcel (http://www.codeplex.com/PHPExcel)
 * @license    http://www.gnu.org/licenses/lgpl.txt	LGPL
 * @version    1.6.7, 2009-04-22
 */

/* Modified by Bertrand Zuchuat */
require_once 'symfony.inc.php';

// Create new PHPExcel object
echo date('H:i:s') . " Create new PHPExcel object\n";
$objPHPExcel = new sfPhpExcel();

// Set properties
echo date('H:i:s') . " Set properties\n";
$objPHPExcel->getProperties()->setCreator("Maarten Balliauw");
$objPHPExcel->getProperties()->setLastModifiedBy("Maarten Balliauw");
$objPHPExcel->getProperties()->setTitle("Office 2007 XLSX Test Document");
$objPHPExcel->getProperties()->setSubject("Office 2007 XLSX Test Document");
$objPHPExcel->getProperties()->setDescription("Test document for Office 2007 XLSX, generated using PHP classes.");
$objPHPExcel->getProperties()->setKeywords("office 2007 openxml php");
$objPHPExcel->getProperties()->setCategory("Test result file");


// Create a first sheet
echo date('H:i:s') . " Add data\n";
$objPHPExcel->setActiveSheetIndex(0);
$objPHPExcel->getActiveSheet()->setCellValue('A1', "Cell B3 and B5 contain data validation...");
$objPHPExcel->getActiveSheet()->setCellValue('A3', "Number:");
$objPHPExcel->getActiveSheet()->setCellValue('B3', "10");
$objPHPExcel->getActiveSheet()->setCellValue('A5', "List:");
$objPHPExcel->getActiveSheet()->setCellValue('B5', "Item A");


// Set data validation
echo date('H:i:s') . " Set data validation\n";
$objValidation = $objPHPExcel->getActiveSheet()->getCell('B3')->getDataValidation();
$objValidation->setType( PHPExcel_Cell_DataValidation::TYPE_WHOLE );
$objValidation->setErrorStyle( PHPExcel_Cell_DataValidation::STYLE_STOP );
$objValidation->setAllowBlank(true);
$objValidation->setShowInputMessage(true);
$objValidation->setShowErrorMessage(true);
$objValidation->setErrorTitle('Input error');
$objValidation->setError('Number is not allowed!');
$objValidation->setPromptTitle('Allowed input');
$objValidation->setPrompt('Only numbers between 10 and 20 are allowed.');
$objValidation->setFormula1(10);
$objValidation->setFormula2(20);
$objPHPExcel->getActiveSheet()->getCell('B3')->setDataValidation($objValidation);

$objValidation = $objPHPExcel->getActiveSheet()->getCell('B5')->getDataValidation();
$objValidation->setType( PHPExcel_Cell_DataValidation::TYPE_LIST );
$objValidation->setErrorStyle( PHPExcel_Cell_DataValidation::STYLE_INFORMATION );
$objValidation->setAllowBlank(false);
$objValidation->setShowInputMessage(true);
$objValidation->setShowErrorMessage(true);
$objValidation->setShowDropDown(true);
$objValidation->setErrorTitle('Input error');
$objValidation->setError('Value is not in list.');
$objValidation->setPromptTitle('Pick from list');
$objValidation->setPrompt('Please pick a value from the drop-down list.');
$objValidation->setFormula1('"Item A,Item B,Item C"');	// Make sure to put the list items between " and "  !!!
$objPHPExcel->getActiveSheet()->getCell('B5')->setDataValidation($objValidation);


// Set active sheet index to the first sheet, so Excel opens this as the first sheet
$objPHPExcel->setActiveSheetIndex(0);

		
// Save Excel 2007 file
echo date('H:i:s') . " Write to Excel2007 format\n";
$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
$objWriter->save(str_replace('.php', '.xlsx', __FILE__));


// Echo memory peak usage
echo date('H:i:s') . " Peak memory usage: " . (memory_get_peak_usage(true) / 1024 / 1024) . " MB\r\n";

// Echo done
echo date('H:i:s') . " Done writing file.\r\n";
