<?php
/**
 * PHPExcel
 *
 * Copyright (c) 2006 - 2010 PHPExcel
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
 * @package    PHPExcel_Reader
 * @copyright  Copyright (c) 2006 - 2010 PHPExcel (http://www.codeplex.com/PHPExcel)
 * @license    http://www.gnu.org/licenses/old-licenses/lgpl-2.1.txt	LGPL
 * @version    ##VERSION##, ##DATE##
 */


/** PHPExcel root directory */
if (!defined('PHPEXCEL_ROOT')) {
	/**
	 * @ignore
	 */
	define('PHPEXCEL_ROOT', dirname(__FILE__) . '/../../');
	require(PHPEXCEL_ROOT . 'PHPExcel/Autoloader.php');
	PHPExcel_Autoloader::Register();
}

/**
 * PHPExcel_Reader_SYLK
 *
 * @category   PHPExcel
 * @package    PHPExcel_Reader
 * @copyright  Copyright (c) 2006 - 2010 PHPExcel (http://www.codeplex.com/PHPExcel)
 */
class PHPExcel_Reader_SYLK implements PHPExcel_Reader_IReader
{
	/**
	 * Input encoding
	 *
	 * @var string
	 */
	private $_inputEncoding;

	/**
	 * Delimiter
	 *
	 * @var string
	 */
	private $_delimiter;

	/**
	 * Enclosure
	 *
	 * @var string
	 */
	private $_enclosure;

	/**
	 * Line ending
	 *
	 * @var string
	 */
	private $_lineEnding;

	/**
	 * Sheet index to read
	 *
	 * @var int
	 */
	private $_sheetIndex;

	/**
	 * Formats
	 *
	 * @var array
	 */
	private $_formats = array();

	/**
	 * Format Count
	 *
	 * @var int
	 */
	private $_format = 0;

	/**
	 * PHPExcel_Reader_IReadFilter instance
	 *
	 * @var PHPExcel_Reader_IReadFilter
	 */
	private $_readFilter = null;

	/**
	 * Create a new PHPExcel_Reader_SYLK
	 */
	public function __construct() {
		$this->_inputEncoding = 'ANSI';
		$this->_delimiter 	= ';';
		$this->_enclosure 	= '"';
		$this->_lineEnding 	= PHP_EOL;
		$this->_sheetIndex 	= 0;
		$this->_readFilter 	= new PHPExcel_Reader_DefaultReadFilter();
	}

	/**
	 * Can the current PHPExcel_Reader_IReader read the file?
	 *
	 * @param 	string 		$pFileName
	 * @return 	boolean
	 */
	public function canRead($pFilename)
	{
		// Check if file exists
		if (!file_exists($pFilename)) {
			throw new Exception("Could not open " . $pFilename . " for reading! File does not exist.");
		}

		// Read sample data (first 2 KB will do)
		$fh = fopen($pFilename, 'r');
		$data = fread($fh, 2048);
		fclose($fh);

		// Count delimiters in file
		$delimiterCount = substr_count($data, ';');
		if ($delimiterCount < 1) {
			return false;
		}

		// Analyze first line looking for ID; signature
		$lines = explode("\n", $data);
		if (substr($lines[0],0,4) != 'ID;P') {
			return false;
		}

		return true;
	}

	/**
	 * Loads PHPExcel from file
	 *
	 * @param 	string 		$pFilename
	 * @return 	PHPExcel
	 * @throws 	Exception
	 */
	public function load($pFilename)
	{
		// Create new PHPExcel
		$objPHPExcel = new PHPExcel();

		// Load into this instance
		return $this->loadIntoExisting($pFilename, $objPHPExcel);
	}

	/**
	 * Read filter
	 *
	 * @return PHPExcel_Reader_IReadFilter
	 */
	public function getReadFilter() {
		return $this->_readFilter;
	}

	/**
	 * Set read filter
	 *
	 * @param PHPExcel_Reader_IReadFilter $pValue
	 */
	public function setReadFilter(PHPExcel_Reader_IReadFilter $pValue) {
		$this->_readFilter = $pValue;
	}

	/**
	 * Set input encoding
	 *
	 * @param string $pValue Input encoding
	 */
	public function setInputEncoding($pValue = 'ANSI')
	{
		$this->_inputEncoding = $pValue;
	}

	/**
	 * Get input encoding
	 *
	 * @return string
	 */
	public function getInputEncoding()
	{
		return $this->_inputEncoding;
	}

	/**
	 * Loads PHPExcel from file into PHPExcel instance
	 *
	 * @param 	string 		$pFilename
	 * @param	PHPExcel	$objPHPExcel
	 * @return 	PHPExcel
	 * @throws 	Exception
	 */
	public function loadIntoExisting($pFilename, PHPExcel $objPHPExcel)
	{
		// Check if file exists
		if (!file_exists($pFilename)) {
			throw new Exception("Could not open " . $pFilename . " for reading! File does not exist.");
		}

		// Create new PHPExcel
		while ($objPHPExcel->getSheetCount() <= $this->_sheetIndex) {
			$objPHPExcel->createSheet();
		}
		$objPHPExcel->setActiveSheetIndex( $this->_sheetIndex );

		$fromFormats	= array('\-',	'\ ');
		$toFormats		= array('-',	' ');

		// Open file
		$fileHandle = fopen($pFilename, 'r');
		if ($fileHandle === false) {
			throw new Exception("Could not open file $pFilename for reading.");
		}

		// Loop through file
		$rowData = array();
		$column = $row = '';

		// loop through one row (line) at a time in the file
		while (($rowData = fgets($fileHandle)) !== FALSE) {

			// convert SYLK encoded $rowData to UTF-8
			$rowData = PHPExcel_Shared_String::SYLKtoUTF8($rowData);

			// explode each row at semicolons while taking into account that literal semicolon (;)
			// is escaped like this (;;)
			$rowData = explode("\t",str_replace('¤',';',str_replace(';',"\t",str_replace(';;','¤',rtrim($rowData)))));

			$dataType = array_shift($rowData);
			//	Read shared styles
			if ($dataType == 'P') {
				$formatArray = array();
				foreach($rowData as $rowDatum) {
					switch($rowDatum{0}) {
						case 'P' :	$formatArray['numberformat']['code'] = str_replace($fromFormats,$toFormats,substr($rowDatum,1));
									break;
						case 'E' :
						case 'F' :	$formatArray['font']['name'] = substr($rowDatum,1);
									break;
						case 'L' :	$formatArray['font']['size'] = substr($rowDatum,1);
									break;
						case 'S' :	$styleSettings = substr($rowDatum,1);
									for ($i=0;$i<strlen($styleSettings);++$i) {
										switch ($styleSettings{$i}) {
											case 'I' :	$formatArray['font']['italic'] = true;
														break;
											case 'D' :	$formatArray['font']['bold'] = true;
														break;
											case 'T' :	$formatArray['borders']['top']['style'] = PHPExcel_Style_Border::BORDER_THIN;
														break;
											case 'B' :	$formatArray['borders']['bottom']['style'] = PHPExcel_Style_Border::BORDER_THIN;
														break;
											case 'L' :	$formatArray['borders']['left']['style'] = PHPExcel_Style_Border::BORDER_THIN;
														break;
											case 'R' :	$formatArray['borders']['right']['style'] = PHPExcel_Style_Border::BORDER_THIN;
														break;
										}
									}
									break;
					}
				}
				$this->_formats['P'.$this->_format++] = $formatArray;
			//	Read cell value data
			} elseif ($dataType == 'C') {
				$hasCalculatedValue = false;
				$cellData = $cellDataFormula = '';
				foreach($rowData as $rowDatum) {
					switch($rowDatum{0}) {
						case 'C' :
						case 'X' :	$column = substr($rowDatum,1);
									break;
						case 'R' :
						case 'Y' :	$row = substr($rowDatum,1);
									break;
						case 'K' :	$cellData = substr($rowDatum,1);
									break;
						case 'E' :	$cellDataFormula = '='.substr($rowDatum,1);
									//	Convert R1C1 style references to A1 style references (but only when not quoted)
									$temp = explode('"',$cellDataFormula);
									foreach($temp as $key => &$value) {
										//	Only count/replace in alternate array entries
										if (($key % 2) == 0) {
											preg_match_all('/(R(\[?-?\d*\]?))(C(\[?-?\d*\]?))/',$value, $cellReferences,PREG_SET_ORDER+PREG_OFFSET_CAPTURE);
											//	Reverse the matches array, otherwise all our offsets will become incorrect if we modify our way
											//		through the formula from left to right. Reversing means that we work right to left.through
											//		the formula
											$cellReferences = array_reverse($cellReferences);
											//	Loop through each R1C1 style reference in turn, converting it to its A1 style equivalent,
											//		then modify the formula to use that new reference
											foreach($cellReferences as $cellReference) {
												$rowReference = $cellReference[2][0];
												//	Empty R reference is the current row
												if ($rowReference == '') $rowReference = $row;
												//	Bracketed R references are relative to the current row
												if ($rowReference{0} == '[') $rowReference = $row + trim($rowReference,'[]');
												$columnReference = $cellReference[4][0];
												//	Empty C reference is the current column
												if ($columnReference == '') $columnReference = $column;
												//	Bracketed C references are relative to the current column
												if ($columnReference{0} == '[') $columnReference = $column + trim($columnReference,'[]');
												$A1CellReference = PHPExcel_Cell::stringFromColumnIndex($columnReference-1).$rowReference;

												$value = substr_replace($value,$A1CellReference,$cellReference[0][1],strlen($cellReference[0][0]));
											}
										}
									}
									unset($value);
									//	Then rebuild the formula string
									$cellDataFormula = implode('"',$temp);
									$hasCalculatedValue = true;
									break;
					}
				}
				$columnLetter = PHPExcel_Cell::stringFromColumnIndex($column-1);
				$cellData = PHPExcel_Calculation::_unwrapResult($cellData);

				// Set cell value
				$objPHPExcel->getActiveSheet()->setCellValue($columnLetter.$row, (($hasCalculatedValue) ? $cellDataFormula : $cellData));
				if ($hasCalculatedValue) {
					$cellData = PHPExcel_Calculation::_unwrapResult($cellData);
					$objPHPExcel->getActiveSheet()->getCell($columnLetter.$row)->setCalculatedValue($cellData);
				}
			//	Read cell formatting
			} elseif ($dataType == 'F') {
				$formatStyle = $columnWidth = $styleSettings = '';
				$styleData = array();
				foreach($rowData as $rowDatum) {
					switch($rowDatum{0}) {
						case 'C' :
						case 'X' :	$column = substr($rowDatum,1);
									break;
						case 'R' :
						case 'Y' :	$row = substr($rowDatum,1);
									break;
						case 'P' :	$formatStyle = $rowDatum;
									break;
						case 'W' :	list($startCol,$endCol,$columnWidth) = explode(' ',substr($rowDatum,1));
									break;
						case 'S' :	$styleSettings = substr($rowDatum,1);
									for ($i=0;$i<strlen($styleSettings);++$i) {
										switch ($styleSettings{$i}) {
											case 'I' :	$styleData['font']['italic'] = true;
														break;
											case 'D' :	$styleData['font']['bold'] = true;
														break;
											case 'T' :	$styleData['borders']['top']['style'] = PHPExcel_Style_Border::BORDER_THIN;
														break;
											case 'B' :	$styleData['borders']['bottom']['style'] = PHPExcel_Style_Border::BORDER_THIN;
														break;
											case 'L' :	$styleData['borders']['left']['style'] = PHPExcel_Style_Border::BORDER_THIN;
														break;
											case 'R' :	$styleData['borders']['right']['style'] = PHPExcel_Style_Border::BORDER_THIN;
														break;
										}
									}
									break;
					}
				}
				if (($formatStyle > '') && ($column > '') && ($row > '')) {
					$columnLetter = PHPExcel_Cell::stringFromColumnIndex($column-1);
					$objPHPExcel->getActiveSheet()->getStyle($columnLetter.$row)->applyFromArray($this->_formats[$formatStyle]);
				}
				if ((count($styleData) > 0) && ($column > '') && ($row > '')) {
					$columnLetter = PHPExcel_Cell::stringFromColumnIndex($column-1);
					$objPHPExcel->getActiveSheet()->getStyle($columnLetter.$row)->applyFromArray($styleData);
				}
				if ($columnWidth > '') {
					if ($startCol == $endCol) {
						$startCol = PHPExcel_Cell::stringFromColumnIndex($startCol-1);
						$objPHPExcel->getActiveSheet()->getColumnDimension($startCol)->setWidth($columnWidth);
					} else {
						$startCol = PHPExcel_Cell::stringFromColumnIndex($startCol-1);
						$endCol = PHPExcel_Cell::stringFromColumnIndex($endCol-1);
						$objPHPExcel->getActiveSheet()->getColumnDimension($startCol)->setWidth($columnWidth);
						do {
							$objPHPExcel->getActiveSheet()->getColumnDimension(++$startCol)->setWidth($columnWidth);
						} while ($startCol != $endCol);
					}
				}
			} else {
				foreach($rowData as $rowDatum) {
					switch($rowDatum{0}) {
						case 'C' :
						case 'X' :	$column = substr($rowDatum,1);
									break;
						case 'R' :
						case 'Y' :	$row = substr($rowDatum,1);
									break;
					}
				}
			}
		}

		// Close file
		fclose($fileHandle);

		// Return
		return $objPHPExcel;
	}

	/**
	 * Get delimiter
	 *
	 * @return string
	 */
	public function getDelimiter() {
		return $this->_delimiter;
	}

	/**
	 * Set delimiter
	 *
	 * @param	string	$pValue		Delimiter, defaults to ,
	 * @return PHPExcel_Reader_SYLK
	 */
	public function setDelimiter($pValue = ',') {
		$this->_delimiter = $pValue;
		return $this;
	}

	/**
	 * Get enclosure
	 *
	 * @return string
	 */
	public function getEnclosure() {
		return $this->_enclosure;
	}

	/**
	 * Set enclosure
	 *
	 * @param	string	$pValue		Enclosure, defaults to "
	 * @return PHPExcel_Reader_SYLK
	 */
	public function setEnclosure($pValue = '"') {
		if ($pValue == '') {
			$pValue = '"';
		}
		$this->_enclosure = $pValue;
		return $this;
	}

	/**
	 * Get line ending
	 *
	 * @return string
	 */
	public function getLineEnding() {
		return $this->_lineEnding;
	}

	/**
	 * Set line ending
	 *
	 * @param	string	$pValue		Line ending, defaults to OS line ending (PHP_EOL)
	 * @return PHPExcel_Reader_SYLK
	 */
	public function setLineEnding($pValue = PHP_EOL) {
		$this->_lineEnding = $pValue;
		return $this;
	}

	/**
	 * Get sheet index
	 *
	 * @return int
	 */
	public function getSheetIndex() {
		return $this->_sheetIndex;
	}

	/**
	 * Set sheet index
	 *
	 * @param	int		$pValue		Sheet index
	 * @return PHPExcel_Reader_SYLK
	 */
	public function setSheetIndex($pValue = 0) {
		$this->_sheetIndex = $pValue;
		return $this;
	}

}
