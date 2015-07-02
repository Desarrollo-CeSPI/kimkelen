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
 * PHPExcel_Reader_OOCalc
 *
 * @category   PHPExcel
 * @package    PHPExcel_Reader
 * @copyright  Copyright (c) 2006 - 2010 PHPExcel (http://www.codeplex.com/PHPExcel)
 */
class PHPExcel_Reader_OOCalc implements PHPExcel_Reader_IReader
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
	 * Create a new PHPExcel_Reader_OOCalc
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
		// Check if file exists
		if (!file_exists($pFilename)) {
			throw new Exception("Could not open " . $pFilename . " for reading! File does not exist.");
		}

		// Load file
		$zip = new ZipArchive;
		if ($zip->open($pFilename) === true) {
			// check if it is an OOXML archive
			$mimeType = $zip->getFromName("mimetype");

			$zip->close();

			return ($mimeType === 'application/vnd.oasis.opendocument.spreadsheet');
		}

		return false;
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

		$zip = new ZipArchive;
		if ($zip->open($pFilename) === true) {
//			echo '<h1>Meta Information</h1>';
			$xml = simplexml_load_string($zip->getFromName("meta.xml"));
			$namespacesMeta = $xml->getNamespaces(true);
//			echo '<pre>';
//			print_r($namespacesMeta);
//			echo '</pre><hr />';

			$docProps = $objPHPExcel->getProperties();
			$officeProperty = $xml->children($namespacesMeta['office']);
			foreach($officeProperty as $officePropertyData) {
				$officePropertyDC = array();
				if (isset($namespacesMeta['dc'])) {
					$officePropertyDC = $officePropertyData->children($namespacesMeta['dc']);
				}
				foreach($officePropertyDC as $propertyName => $propertyValue) {
//					echo $propertyName.' = '.$propertyValue.'<hr />';

					switch ($propertyName) {
						case 'title' :
								$docProps->setTitle($propertyValue);
								break;
						case 'subject' :
								$docProps->setSubject($propertyValue);
								break;
						case 'creator' :
								$docProps->setCreator($propertyValue);
								break;
						case 'date' :
								$creationDate = strtotime($propertyValue);
								$docProps->setCreated($creationDate);
								break;
						case 'description' :
								$docProps->setDescription($propertyValue);
								break;
					}
				}
				$officePropertyMeta = array();
				if (isset($namespacesMeta['dc'])) {
					$officePropertyMeta = $officePropertyData->children($namespacesMeta['meta']);
				}
				foreach($officePropertyMeta as $propertyName => $propertyValue) {
					$propertyValueAttributes = $propertyValue->attributes($namespacesMeta['meta']);

//					echo $propertyName.' = '.$propertyValue.'<br />';
//					foreach ($propertyValueAttributes as $key => $value) {
//						echo $key.' = '.$value.'<br />';
//					}
//					echo '<hr />';
//
					switch ($propertyName) {
						case 'keyword' :
								$docProps->setKeywords($propertyValue);
								break;
					}
				}
			}


//			echo '<h1>Workbook Content</h1>';
			$xml = simplexml_load_string($zip->getFromName("content.xml"));
			$namespacesContent = $xml->getNamespaces(true);
//			echo '<pre>';
//			print_r($namespacesContent);
//			echo '</pre><hr />';

			$workbook = $xml->children($namespacesContent['office']);
			foreach($workbook->body->spreadsheet as $workbookData) {
				$workbookData = $workbookData->children($namespacesContent['table']);
				$worksheetID = 0;
				foreach($workbookData->table as $worksheetDataSet) {
					$worksheetData = $worksheetDataSet->children($namespacesContent['table']);
//					print_r($worksheetData);
//					echo '<br />';
					$worksheetDataAttributes = $worksheetDataSet->attributes($namespacesContent['table']);
//					print_r($worksheetDataAttributes);
//					echo '<br />';
					if ((isset($this->_loadSheetsOnly)) && (isset($worksheetDataAttributes['name'])) &&
						(!in_array($worksheetDataAttributes['name'], $this->_loadSheetsOnly))) {
						continue;
					}

//					echo '<h2>Worksheet '.$worksheetDataAttributes['name'].'</h2>';
					// Create new Worksheet
					$objPHPExcel->createSheet();
					$objPHPExcel->setActiveSheetIndex($worksheetID);
					if (isset($worksheetDataAttributes['name'])) {
						$worksheetName = (string) $worksheetDataAttributes['name'];
						$objPHPExcel->getActiveSheet()->setTitle($worksheetName);
					}

					$rowID = 1;
					foreach($worksheetData as $key => $rowData) {
//						echo '<b>'.$key.'</b><br />';
						switch ($key) {
							case 'table-row' :
								$columnID = 'A';
								foreach($rowData as $key => $cellData) {
//									echo '<b>'.$columnID.$rowID.'</b><br />';
									$cellDataText = $cellData->children($namespacesContent['text']);
									$cellDataOfficeAttributes = $cellData->attributes($namespacesContent['office']);
									$cellDataTableAttributes = $cellData->attributes($namespacesContent['table']);

//									echo 'Office Attributes: ';
//									print_r($cellDataOfficeAttributes);
//									echo '<br />Table Attributes: ';
//									print_r($cellDataTableAttributes);
//									echo '<br />Cell Data Text';
//									print_r($cellDataText);
//									echo '<br />';
//
									$type = $formatting = $hyperlink = null;
									$hasCalculatedValue = false;
									$cellDataFormula = '';
									if (isset($cellDataTableAttributes['formula'])) {
										$cellDataFormula = $cellDataTableAttributes['formula'];
										$hasCalculatedValue = true;
									}

									if (isset($cellDataText->p)) {
//										echo 'Value Type is '.$cellDataOfficeAttributes['value-type'].'<br />';
										switch ($cellDataOfficeAttributes['value-type']) {
											case 'string' :
													$type = PHPExcel_Cell_DataType::TYPE_STRING;
													$dataValue = $cellDataText->p;
													if (isset($dataValue->a)) {
														$dataValue = $dataValue->a;
														$cellXLinkAttributes = $dataValue->attributes($namespacesContent['xlink']);
														$hyperlink = $cellXLinkAttributes['href'];
													}
													break;
											case 'boolean' :
													$type = PHPExcel_Cell_DataType::TYPE_BOOL;
													$dataValue = ($cellDataText->p == 'TRUE') ? True : False;
													break;
											case 'float' :
													$type = PHPExcel_Cell_DataType::TYPE_NUMERIC;
													$dataValue = (float) $cellDataOfficeAttributes['value'];
													if (floor($dataValue) == $dataValue) {
														$dataValue = (integer) $dataValue;
													}
													break;
											case 'date' :
													$type = PHPExcel_Cell_DataType::TYPE_NUMERIC;
													$dateObj = date_create($cellDataOfficeAttributes['date-value']);
													list($year,$month,$day,$hour,$minute,$second) = explode(' ',$dateObj->format('Y m d H i s'));
													$dataValue = PHPExcel_Shared_Date::FormattedPHPToExcel($year,$month,$day,$hour,$minute,$second);
													if ($dataValue != floor($dataValue)) {
														$formatting = PHPExcel_Style_NumberFormat::FORMAT_DATE_XLSX15.' '.PHPExcel_Style_NumberFormat::FORMAT_DATE_TIME4;
													} else {
														$formatting = PHPExcel_Style_NumberFormat::FORMAT_DATE_XLSX15;
													}
													break;
											case 'time' :
													$type = PHPExcel_Cell_DataType::TYPE_NUMERIC;
													$dataValue = PHPExcel_Shared_Date::PHPToExcel(strtotime('01-01-1970 '.implode(':',sscanf($cellDataOfficeAttributes['time-value'],'PT%dH%dM%dS'))));
													$formatting = PHPExcel_Style_NumberFormat::FORMAT_DATE_TIME4;
													break;
										}
//										echo 'Data value is '.$dataValue.'<br />';
//										if (!is_null($hyperlink)) {
//											echo 'Hyperlink is '.$hyperlink.'<br />';
//										}
									}

									if ($hasCalculatedValue) {
										$type = PHPExcel_Cell_DataType::TYPE_FORMULA;
//										echo 'Formula: '.$cellDataFormula.'<br />';
										$cellDataFormula = substr($cellDataFormula,strpos($cellDataFormula,':=')+1);
										$temp = explode('"',$cellDataFormula);
										foreach($temp as $key => &$value) {
											//	Only replace in alternate array entries (i.e. non-quoted blocks)
											if (($key % 2) == 0) {
												$value = preg_replace('/\[\.(.*):\.(.*)\]/Ui','$1:$2',$value);
												$value = preg_replace('/\[\.(.*)\]/Ui','$1',$value);
												$value = PHPExcel_Calculation::_translateSeparator(';',',',$value);
											}
										}
										unset($value);
										//	Then rebuild the formula string
										$cellDataFormula = implode('"',$temp);
//										echo 'Adjusted Formula: '.$cellDataFormula.'<br />';
									}

									if (!is_null($type)) {
										$objPHPExcel->getActiveSheet()->getCell($columnID.$rowID)->setValueExplicit((($hasCalculatedValue) ? $cellDataFormula : $dataValue),$type);
										if ($hasCalculatedValue) {
//											echo 'Forumla result is '.$dataValue.'<br />';
											$objPHPExcel->getActiveSheet()->getCell($columnID.$rowID)->setCalculatedValue($dataValue);
										}
										if (($cellDataOfficeAttributes['value-type'] == 'date') ||
											($cellDataOfficeAttributes['value-type'] == 'time')) {
											$objPHPExcel->getActiveSheet()->getStyle($columnID.$rowID)->getNumberFormat()->setFormatCode($formatting);
										}
										if (!is_null($hyperlink)) {
											$objPHPExcel->getActiveSheet()->getCell($columnID.$rowID)->getHyperlink()->setUrl($hyperlink);
										}
									}

									//	Merged cells
									if ((isset($cellDataTableAttributes['number-columns-spanned'])) || (isset($cellDataTableAttributes['number-rows-spanned']))) {
										$columnTo = $columnID;
										if (isset($cellDataTableAttributes['number-columns-spanned'])) {
											$columnTo = PHPExcel_Cell::stringFromColumnIndex(PHPExcel_Cell::columnIndexFromString($columnID) + $cellDataTableAttributes['number-columns-spanned'] -2);
										}
										$rowTo = $rowID;
										if (isset($cellDataTableAttributes['number-rows-spanned'])) {
											$rowTo = $rowTo + $cellDataTableAttributes['number-rows-spanned'] - 1;
										}
										$cellRange = $columnID.$rowID.':'.$columnTo.$rowTo;
										$objPHPExcel->getActiveSheet()->mergeCells($cellRange);
									}

									if (isset($cellDataTableAttributes['number-columns-repeated'])) {
//										echo 'Repeated '.$cellDataTableAttributes['number-columns-repeated'].' times<br />';
										$columnID = PHPExcel_Cell::stringFromColumnIndex(PHPExcel_Cell::columnIndexFromString($columnID) + $cellDataTableAttributes['number-columns-repeated'] - 2);
									}
									++$columnID;
								}
								++$rowID;
								break;
						}
					}
					++$worksheetID;
				}
			}

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
	 * @return PHPExcel_Reader_OOCalc
	 */
	public function setSheetIndex($pValue = 0) {
		$this->_sheetIndex = $pValue;
		return $this;
	}
}
