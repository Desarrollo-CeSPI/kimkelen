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
 * PHPExcel_Reader_Excel2003XML
 *
 * @category   PHPExcel
 * @package    PHPExcel_Reader
 * @copyright  Copyright (c) 2006 - 2010 PHPExcel (http://www.codeplex.com/PHPExcel)
 */
class PHPExcel_Reader_Excel2003XML implements PHPExcel_Reader_IReader
{
	/**
	 * Read data only?
	 *
	 * @var boolean
	 */
	private $_readDataOnly = false;

	/**
	 * Restict which sheets should be loaded?
	 *
	 * @var array
	 */
	private $_loadSheetsOnly = null;

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
	private $_styles = array();

	/**
	 * PHPExcel_Reader_IReadFilter instance
	 *
	 * @var PHPExcel_Reader_IReadFilter
	 */
	private $_readFilter = null;


	/**
	 * Read data only?
	 *
	 * @return boolean
	 */
	public function getReadDataOnly() {
		return $this->_readDataOnly;
	}

	/**
	 * Set read data only
	 *
	 * @param boolean $pValue
	 * @return PHPExcel_Reader_Excel2007
	 */
	public function setReadDataOnly($pValue = false) {
		$this->_readDataOnly = $pValue;
		return $this;
	}

	/**
	 * Get which sheets to load
	 *
	 * @return mixed
	 */
	public function getLoadSheetsOnly()
	{
		return $this->_loadSheetsOnly;
	}

	/**
	 * Set which sheets to load
	 *
	 * @param mixed $value
	 * @return PHPExcel_Reader_Excel2007
	 */
	public function setLoadSheetsOnly($value = null)
	{
		$this->_loadSheetsOnly = is_array($value) ?
			$value : array($value);
		return $this;
	}

	/**
	 * Set all sheets to load
	 *
	 * @return PHPExcel_Reader_Excel2007
	 */
	public function setLoadAllSheets()
	{
		$this->_loadSheetsOnly = null;
		return $this;
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
	 * @return PHPExcel_Reader_Excel2007
	 */
	public function setReadFilter(PHPExcel_Reader_IReadFilter $pValue) {
		$this->_readFilter = $pValue;
		return $this;
	}

	/**
	 * Create a new PHPExcel_Reader_Excel2003XML
	 */
	public function __construct() {
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

//	Office					xmlns:o="urn:schemas-microsoft-com:office:office"
//	Excel					xmlns:x="urn:schemas-microsoft-com:office:excel"
//	XML Spreadsheet			xmlns:ss="urn:schemas-microsoft-com:office:spreadsheet"
//	Spreadsheet component	xmlns:c="urn:schemas-microsoft-com:office:component:spreadsheet"
//	XML schema 				xmlns:s="uuid:BDC6E3F0-6DA3-11d1-A2A3-00AA00C14882"
//	XML data type			xmlns:dt="uuid:C2F41010-65B3-11d1-A29F-00AA00C14882"
//	MS-persist recordset	xmlns:rs="urn:schemas-microsoft-com:rowset"
//	Rowset					xmlns:z="#RowsetSchema"
//

		$signature = array(
				'<?xml version="1.0"?>',
				'<?mso-application progid="Excel.Sheet"?>'
			);

		// Check if file exists
		if (!file_exists($pFilename)) {
			throw new Exception("Could not open " . $pFilename . " for reading! File does not exist.");
		}

		// Read sample data (first 2 KB will do)
		$fh = fopen($pFilename, 'r');
		$data = fread($fh, 2048);
		fclose($fh);

		$headers = explode("\n",$data);
		$valid = true;
		foreach($signature as $key => $match) {
			if (isset($headers[$key])) {
				$line = trim(rtrim($headers[$key], "\r\n"));
				if ($line != $match) {
					$valid = false;
					break;
				}
			} else {
				$valid = false;
				break;
			}
		}

		return $valid;
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

	private static function identifyFixedStyleValue($styleList,&$styleAttributeValue) {
		$styleAttributeValue = strtolower($styleAttributeValue);
		foreach($styleList as $style) {
			if ($styleAttributeValue == strtolower($style)) {
				$styleAttributeValue = $style;
				return true;
			}
		}
		return false;
	}

 	/**
 	 * pixel units to excel width units(units of 1/256th of a character width)
 	 * @param pxs
 	 * @return
 	 */
 	private static function _pixel2WidthUnits($pxs) {
		$UNIT_OFFSET_MAP = array(0, 36, 73, 109, 146, 182, 219);

		$widthUnits = 256 * ($pxs / 7);
		$widthUnits += $UNIT_OFFSET_MAP[($pxs % 7)];
		return $widthUnits;
	}

	/**
	 * excel width units(units of 1/256th of a character width) to pixel units
	 * @param widthUnits
	 * @return
	 */
	private static function _widthUnits2Pixel($widthUnits) {
		$pixels = ($widthUnits / 256) * 7;
		$offsetWidthUnits = $widthUnits % 256;
		$pixels += round($offsetWidthUnits / (256 / 7));
		return $pixels;
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
		$fromFormats	= array('\-',	'\ ');
		$toFormats		= array('-',	' ');

		$underlineStyles = array (
				PHPExcel_Style_Font::UNDERLINE_NONE,
				PHPExcel_Style_Font::UNDERLINE_DOUBLE,
				PHPExcel_Style_Font::UNDERLINE_DOUBLEACCOUNTING,
				PHPExcel_Style_Font::UNDERLINE_SINGLE,
				PHPExcel_Style_Font::UNDERLINE_SINGLEACCOUNTING
			);
		$verticalAlignmentStyles = array (
				PHPExcel_Style_Alignment::VERTICAL_BOTTOM,
				PHPExcel_Style_Alignment::VERTICAL_TOP,
				PHPExcel_Style_Alignment::VERTICAL_CENTER,
				PHPExcel_Style_Alignment::VERTICAL_JUSTIFY
			);
		$horizontalAlignmentStyles = array (
				PHPExcel_Style_Alignment::HORIZONTAL_GENERAL,
				PHPExcel_Style_Alignment::HORIZONTAL_LEFT,
				PHPExcel_Style_Alignment::HORIZONTAL_RIGHT,
				PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
				PHPExcel_Style_Alignment::HORIZONTAL_CENTER_CONTINUOUS,
				PHPExcel_Style_Alignment::HORIZONTAL_JUSTIFY
			);


		// Check if file exists
		if (!file_exists($pFilename)) {
			throw new Exception("Could not open " . $pFilename . " for reading! File does not exist.");
		}

		$xml = simplexml_load_file($pFilename);
		$namespaces = $xml->getNamespaces(true);
//		echo '<pre>';
//		print_r($namespaces);
//		echo '</pre><hr />';
//
//		echo '<pre>';
//		print_r($xml);
//		echo '</pre><hr />';
//
		$docProps = $objPHPExcel->getProperties();
		foreach($xml->DocumentProperties[0] as $propertyName => $propertyValue) {
			switch ($propertyName) {
				case 'Title' :
						$docProps->setTitle($propertyValue);
						break;
				case 'Subject' :
						$docProps->setSubject($propertyValue);
						break;
				case 'Author' :
						$docProps->setCreator($propertyValue);
						break;
				case 'Created' :
						$creationDate = strtotime($propertyValue);
						$docProps->setCreated($creationDate);
						break;
				case 'LastAuthor' :
						$docProps->setLastModifiedBy($propertyValue);
						break;
				case 'Company' :
						$docProps->setCompany($propertyValue);
						break;
				case 'Category' :
						$docProps->setCategory($propertyValue);
						break;
				case 'Keywords' :
						$docProps->setKeywords($propertyValue);
						break;
				case 'Description' :
						$docProps->setDescription($propertyValue);
						break;
			}
		}


		foreach($xml->Styles[0] as $style) {
			$style_ss = $style->attributes($namespaces['ss']);
			$styleID = (string) $style_ss['ID'];
//			echo 'Style ID = '.$styleID.'<br />';
			if ($styleID == 'Default') {
				$this->_styles['Default'] = array();
			} else {
				$this->_styles[$styleID] = $this->_styles['Default'];
			}
			foreach ($style as $styleType => $styleData) {
				$styleAttributes = $styleData->attributes($namespaces['ss']);
//				echo $styleType.'<br />';
				switch ($styleType) {
					case 'Alignment' :
							foreach($styleAttributes as $styleAttributeKey => $styleAttributeValue) {
//								echo $styleAttributeKey.' = '.$styleAttributeValue.'<br />';
								$styleAttributeValue = (string) $styleAttributeValue;
								switch ($styleAttributeKey) {
									case 'Vertical' :
											if (self::identifyFixedStyleValue($verticalAlignmentStyles,$styleAttributeValue)) {
												$this->_styles[$styleID]['alignment']['vertical'] = $styleAttributeValue;
											}
											break;
									case 'Horizontal' :
											if (self::identifyFixedStyleValue($horizontalAlignmentStyles,$styleAttributeValue)) {
												$this->_styles[$styleID]['alignment']['horizontal'] = $styleAttributeValue;
											}
											break;
									case 'WrapText' :
											$this->_styles[$styleID]['alignment']['wrap'] = true;
											break;
								}
							}
							break;
					case 'Borders' :
							foreach($styleData->Border as $borderStyle) {
								$borderAttributes = $borderStyle->attributes($namespaces['ss']);
								$thisBorder = array();
								foreach($borderAttributes as $borderStyleKey => $borderStyleValue) {
//									echo $borderStyleKey.' = '.$borderStyleValue.'<br />';
									switch ($borderStyleKey) {
										case 'LineStyle' :
												$thisBorder['style'] = PHPExcel_Style_Border::BORDER_MEDIUM;
//												$thisBorder['style'] = $borderStyleValue;
												break;
										case 'Weight' :
//												$thisBorder['style'] = $borderStyleValue;
												break;
										case 'Position' :
												$borderPosition = strtolower($borderStyleValue);
												break;
										case 'Color' :
												$borderColour = substr($borderStyleValue,1);
												$thisBorder['color']['rgb'] = $borderColour;
												break;
									}
								}
								if (count($thisBorder) > 0) {
									if (($borderPosition == 'left') || ($borderPosition == 'right') || ($borderPosition == 'top') || ($borderPosition == 'bottom')) {
										$this->_styles[$styleID]['borders'][$borderPosition] = $thisBorder;
									}
								}
							}
							break;
					case 'Font' :
							foreach($styleAttributes as $styleAttributeKey => $styleAttributeValue) {
//								echo $styleAttributeKey.' = '.$styleAttributeValue.'<br />';
								$styleAttributeValue = (string) $styleAttributeValue;
								switch ($styleAttributeKey) {
									case 'FontName' :
											$this->_styles[$styleID]['font']['name'] = $styleAttributeValue;
											break;
									case 'Size' :
											$this->_styles[$styleID]['font']['size'] = $styleAttributeValue;
											break;
									case 'Color' :
											$this->_styles[$styleID]['font']['color']['rgb'] = substr($styleAttributeValue,1);
											break;
									case 'Bold' :
											$this->_styles[$styleID]['font']['bold'] = true;
											break;
									case 'Italic' :
											$this->_styles[$styleID]['font']['italic'] = true;
											break;
									case 'Underline' :
											if (self::identifyFixedStyleValue($underlineStyles,$styleAttributeValue)) {
												$this->_styles[$styleID]['font']['underline'] = $styleAttributeValue;
											}
											break;
								}
							}
							break;
					case 'Interior' :
							foreach($styleAttributes as $styleAttributeKey => $styleAttributeValue) {
//								echo $styleAttributeKey.' = '.$styleAttributeValue.'<br />';
								switch ($styleAttributeKey) {
									case 'Color' :
											$this->_styles[$styleID]['fill']['color']['rgb'] = substr($styleAttributeValue,1);
											break;
								}
							}
							break;
					case 'NumberFormat' :
							foreach($styleAttributes as $styleAttributeKey => $styleAttributeValue) {
//								echo $styleAttributeKey.' = '.$styleAttributeValue.'<br />';
								$styleAttributeValue = str_replace($fromFormats,$toFormats,$styleAttributeValue);
								switch ($styleAttributeValue) {
									case 'Short Date' :
											$styleAttributeValue = 'dd/mm/yyyy';
											break;
								}
								if ($styleAttributeValue > '') {
									$this->_styles[$styleID]['numberformat']['code'] = $styleAttributeValue;
								}
							}
							break;
					case 'Protection' :
							foreach($styleAttributes as $styleAttributeKey => $styleAttributeValue) {
//								echo $styleAttributeKey.' = '.$styleAttributeValue.'<br />';
							}
							break;
				}
			}
//			print_r($this->_styles[$styleID]);
//			echo '<hr />';
		}
//		echo '<hr />';

		$worksheetID = 0;
		foreach($xml->Worksheet as $worksheet) {
			$worksheet_ss = $worksheet->attributes($namespaces['ss']);
			if ((isset($this->_loadSheetsOnly)) && (isset($worksheet_ss['Name'])) &&
				(!in_array($worksheet_ss['Name'], $this->_loadSheetsOnly))) {
				continue;
			}

			// Create new Worksheet
			$objPHPExcel->createSheet();
			$objPHPExcel->setActiveSheetIndex($worksheetID);
			if (isset($worksheet_ss['Name'])) {
				$worksheetName = (string) $worksheet_ss['Name'];
				$objPHPExcel->getActiveSheet()->setTitle($worksheetName);
			}

			$columnID = 'A';
			foreach($worksheet->Table->Column as $columnData) {
				$columnData_ss = $columnData->attributes($namespaces['ss']);
				if (isset($columnData_ss['Index'])) {
					$columnID = PHPExcel_Cell::stringFromColumnIndex($columnData_ss['Index']-1);
				}
				if (isset($columnData_ss['Width'])) {
					$columnWidth = $columnData_ss['Width'];
//					echo '<b>Setting column width for '.$columnID.' to '.$columnWidth.'</b><br />';
					$objPHPExcel->getActiveSheet()->getColumnDimension($columnID)->setWidth($columnWidth / 5.4);
				}
				++$columnID;
			}

			$rowID = 1;
			foreach($worksheet->Table->Row as $rowData) {
				$row_ss = $rowData->attributes($namespaces['ss']);
				if (isset($row_ss['Index'])) {
					$rowID = (integer) $row_ss['Index'];
				}
//				echo '<b>Row '.$rowID.'</b><br />';
				if (isset($row_ss['StyleID'])) {
					$rowStyle = $row_ss['StyleID'];
				}
				if (isset($row_ss['Height'])) {
					$rowHeight = $row_ss['Height'];
//					echo '<b>Setting row height to '.$rowHeight.'</b><br />';
					$objPHPExcel->getActiveSheet()->getRowDimension($rowID)->setRowHeight($rowHeight);
				}
				$columnID = 'A';
				foreach($rowData->Cell as $cell) {

					$cell_ss = $cell->attributes($namespaces['ss']);
					if (isset($cell_ss['Index'])) {
						$columnID = PHPExcel_Cell::stringFromColumnIndex($cell_ss['Index']-1);
					}
					$cellRange = $columnID.$rowID;

					if ((isset($cell_ss['MergeAcross'])) || (isset($cell_ss['MergeDown']))) {
						$columnTo = $columnID;
						if (isset($cell_ss['MergeAcross'])) {
							$columnTo = PHPExcel_Cell::stringFromColumnIndex(PHPExcel_Cell::columnIndexFromString($columnID) + $cell_ss['MergeAcross'] -1);
						}
						$rowTo = $rowID;
						if (isset($cell_ss['MergeDown'])) {
							$rowTo = $rowTo + $cell_ss['MergeDown'];
						}
						$cellRange .= ':'.$columnTo.$rowTo;
						$objPHPExcel->getActiveSheet()->mergeCells($cellRange);
					}

					$hasCalculatedValue = false;
					$cellDataFormula = '';
					if (isset($cell_ss['Formula'])) {
						$cellDataFormula = $cell_ss['Formula'];
						$hasCalculatedValue = true;
					}
					if (isset($cell->Data)) {
						$cellValue = $cellData = $cell->Data;
						$type = PHPExcel_Cell_DataType::TYPE_NULL;
						$cellData_ss = $cellData->attributes($namespaces['ss']);
						if (isset($cellData_ss['Type'])) {
							$cellDataType = $cellData_ss['Type'];
							switch ($cellDataType) {
								/*
								const TYPE_STRING		= 's';
								const TYPE_FORMULA		= 'f';
								const TYPE_NUMERIC		= 'n';
								const TYPE_BOOL			= 'b';
							    const TYPE_NULL			= 's';
							    const TYPE_INLINE		= 'inlineStr';
								const TYPE_ERROR		= 'e';
								*/
								case 'String' :
										$type = PHPExcel_Cell_DataType::TYPE_STRING;
										break;
								case 'Number' :
										$type = PHPExcel_Cell_DataType::TYPE_NUMERIC;
										$cellValue = (float) $cellValue;
										if (floor($cellValue) == $cellValue) {
											$cellValue = (integer) $cellValue;
										}
										break;
								case 'Boolean' :
										$type = PHPExcel_Cell_DataType::TYPE_BOOL;
										$cellValue = ($cellValue != 0);
										break;
								case 'DateTime' :
										$type = PHPExcel_Cell_DataType::TYPE_NUMERIC;
										$cellValue = PHPExcel_Shared_Date::PHPToExcel(strtotime($cellValue));
										break;
								case 'Error' :
										$type = PHPExcel_Cell_DataType::TYPE_ERROR;
										break;
							}
						}
						if ($hasCalculatedValue) {
							$type = PHPExcel_Cell_DataType::TYPE_FORMULA;
							$columnNumber = PHPExcel_Cell::columnIndexFromString($columnID);
							//	Convert R1C1 style references to A1 style references (but only when not quoted)
							$temp = explode('"',$cellDataFormula);
							foreach($temp as $key => &$value) {
								//	Only replace in alternate array entries (i.e. non-quoted blocks)
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
										if ($rowReference == '') $rowReference = $rowID;
										//	Bracketed R references are relative to the current row
										if ($rowReference{0} == '[') $rowReference = $rowID + trim($rowReference,'[]');
										$columnReference = $cellReference[4][0];
										//	Empty C reference is the current column
										if ($columnReference == '') $columnReference = $columnNumber;
										//	Bracketed C references are relative to the current column
										if ($columnReference{0} == '[') $columnReference = $columnNumber + trim($columnReference,'[]');
										$A1CellReference = PHPExcel_Cell::stringFromColumnIndex($columnReference-1).$rowReference;
											$value = substr_replace($value,$A1CellReference,$cellReference[0][1],strlen($cellReference[0][0]));
									}
								}
							}
							unset($value);
							//	Then rebuild the formula string
							$cellDataFormula = implode('"',$temp);
						}

//						echo 'Cell '.$columnID.$rowID.' is a '.$type.' with a value of '.(($hasCalculatedValue) ? $cellDataFormula : $cellValue).'<br />';
//
						$objPHPExcel->getActiveSheet()->getCell($columnID.$rowID)->setValueExplicit((($hasCalculatedValue) ? $cellDataFormula : $cellValue),$type);
						if ($hasCalculatedValue) {
//							echo 'Forumla result is '.$cellValue.'<br />';
							$objPHPExcel->getActiveSheet()->getCell($columnID.$rowID)->setCalculatedValue($cellValue);
						}
					}
					if (isset($cell_ss['StyleID'])) {
						$style = (string) $cell_ss['StyleID'];
//						echo 'Cell style for '.$columnID.$rowID.' is '.$style.'<br />';
						if ((isset($this->_styles[$style])) && (count($this->_styles[$style]) > 0)) {
//							echo 'Cell '.$columnID.$rowID.'<br />';
//							print_r($this->_styles[$style]);
//							echo '<br />';
							if (!$objPHPExcel->getActiveSheet()->cellExists($columnID.$rowID)) {
								$objPHPExcel->getActiveSheet()->setCellValue($columnID.$rowID,NULL);
							}
							$objPHPExcel->getActiveSheet()->getStyle($cellRange)->applyFromArray($this->_styles[$style]);
						}
					}
					++$columnID;
				}
				++$rowID;
			}
			++$worksheetID;
		}

		// Return
		return $objPHPExcel;
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
	 * @return PHPExcel_Reader_Excel2003XML
	 */
	public function setSheetIndex($pValue = 0) {
		$this->_sheetIndex = $pValue;
		return $this;
	}
}
