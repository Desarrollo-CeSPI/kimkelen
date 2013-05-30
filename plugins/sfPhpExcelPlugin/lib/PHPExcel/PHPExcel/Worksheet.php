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
 * @package    PHPExcel_Worksheet
 * @copyright  Copyright (c) 2006 - 2010 PHPExcel (http://www.codeplex.com/PHPExcel)
 * @license    http://www.gnu.org/licenses/old-licenses/lgpl-2.1.txt	LGPL
 * @version    ##VERSION##, ##DATE##
 */


/**
 * PHPExcel_Worksheet
 *
 * @category   PHPExcel
 * @package    PHPExcel_Worksheet
 * @copyright  Copyright (c) 2006 - 2010 PHPExcel (http://www.codeplex.com/PHPExcel)
 */
class PHPExcel_Worksheet implements PHPExcel_IComparable
{
	/* Break types */
	const BREAK_NONE	= 0;
	const BREAK_ROW		= 1;
	const BREAK_COLUMN	= 2;

	/* Sheet state */
	const SHEETSTATE_VISIBLE 	= 'visible';
	const SHEETSTATE_HIDDEN 	= 'hidden';
	const SHEETSTATE_VERYHIDDEN = 'veryHidden';

	/**
	 * Invalid characters in sheet title
	 *
	 * @var array
	 */
	private static $_invalidCharacters = array('*', ':', '/', '\\', '?', '[', ']');

	/**
	 * Parent spreadsheet
	 *
	 * @var PHPExcel
	 */
	private $_parent;

	/**
	 * Collection of cells
	 *
	 * @var PHPExcel_Cell[]
	 */
	private $_cellCollection = array();

	/**
	 * Collection of row dimensions
	 *
	 * @var PHPExcel_Worksheet_RowDimension[]
	 */
	private $_rowDimensions = array();

	/**
	 * Default row dimension
	 *
	 * @var PHPExcel_Worksheet_RowDimension
	 */
	private $_defaultRowDimension = null;

	/**
	 * Collection of column dimensions
	 *
	 * @var PHPExcel_Worksheet_ColumnDimension[]
	 */
	private $_columnDimensions = array();

	/**
	 * Default column dimension
	 *
	 * @var PHPExcel_Worksheet_ColumnDimension
	 */
	private $_defaultColumnDimension = null;

	/**
	 * Collection of drawings
	 *
	 * @var PHPExcel_Worksheet_BaseDrawing[]
	 */
	private $_drawingCollection = null;

	/**
	 * Worksheet title
	 *
	 * @var string
	 */
	private $_title;

	/**
	 * Sheet state
	 *
	 * @var string
	 */
	private $_sheetState;

	/**
	 * Page setup
	 *
	 * @var PHPExcel_Worksheet_PageSetup
	 */
	private $_pageSetup;

	/**
	 * Page margins
	 *
	 * @var PHPExcel_Worksheet_PageMargins
	 */
	private $_pageMargins;

	/**
	 * Page header/footer
	 *
	 * @var PHPExcel_Worksheet_HeaderFooter
	 */
	private $_headerFooter;

	/**
	 * Sheet view
	 *
	 * @var PHPExcel_Worksheet_SheetView
	 */
	private $_sheetView;

	/**
	 * Protection
	 *
	 * @var PHPExcel_Worksheet_Protection
	 */
	private $_protection;

	/**
	 * Collection of styles
	 *
	 * @var PHPExcel_Style[]
	 */
	private $_styles = array();

	/**
	 * Conditional styles. Indexed by cell coordinate, e.g. 'A1'
	 *
	 * @var array
	 */
	private $_conditionalStylesCollection = array();

	/**
	 * Is the current cell collection sorted already?
	 *
	 * @var boolean
	 */
	private $_cellCollectionIsSorted = false;

	/**
	 * Collection of breaks
	 *
	 * @var array
	 */
	private $_breaks = array();

	/**
	 * Collection of merged cell ranges
	 *
	 * @var array
	 */
	private $_mergeCells = array();

	/**
	 * Collection of protected cell ranges
	 *
	 * @var array
	 */
	private $_protectedCells = array();

	/**
	 * Autofilter Range
	 *
	 * @var string
	 */
	private $_autoFilter = '';

	/**
	 * Freeze pane
	 *
	 * @var string
	 */
	private $_freezePane = '';

	/**
	 * Show gridlines?
	 *
	 * @var boolean
	 */
	private $_showGridlines = true;

	/**
	* Print gridlines?
	*
	* @var boolean
	*/
	private $_printGridlines = false;

	/**
	 * Show summary below? (Row/Column outline)
	 *
	 * @var boolean
	 */
	private $_showSummaryBelow = true;

	/**
	 * Show summary right? (Row/Column outline)
	 *
	 * @var boolean
	 */
	private $_showSummaryRight = true;

	/**
	 * Collection of comments
	 *
	 * @var PHPExcel_Comment[]
	 */
	private $_comments = array();

	/**
	 * Active cell. (Only one!)
	 *
	 * @var string
	 */
	private $_activeCell = 'A1';

	/**
	 * Selected cells
	 *
	 * @var string
	 */
	private $_selectedCells = 'A1';

	/**
	 * Cached highest column
	 *
	 * @var string
	 */
	private $_cachedHighestColumn = 'A';

	/**
	 * Cached highest row
	 *
	 * @var int
	 */
	private $_cachedHighestRow = 1;

	/**
	 * Right-to-left?
	 *
	 * @var boolean
	 */
	private $_rightToLeft = false;

	/**
	 * Hyperlinks. Indexed by cell coordinate, e.g. 'A1'
	 *
	 * @var array
	 */
	private $_hyperlinkCollection = array();

	/**
	 * Data validation objects. Indexed by cell coordinate, e.g. 'A1'
	 *
	 * @var array
	 */
	private $_dataValidationCollection = array();

	/**
	 * Tab color
	 *
	 * @var PHPExcel_Style_Color
	 */
	private $_tabColor;

	/**
	 * Create a new worksheet
	 *
	 * @param PHPExcel 		$pParent
	 * @param string 		$pTitle
	 */
	public function __construct(PHPExcel $pParent = null, $pTitle = 'Worksheet')
	{
		// Set parent and title
		$this->_parent = $pParent;
		$this->setTitle($pTitle);
		$this->setSheetState(PHPExcel_Worksheet::SHEETSTATE_VISIBLE);

		// Set page setup
		$this->_pageSetup 			= new PHPExcel_Worksheet_PageSetup();

		// Set page margins
		$this->_pageMargins 		= new PHPExcel_Worksheet_PageMargins();

		// Set page header/footer
		$this->_headerFooter 		= new PHPExcel_Worksheet_HeaderFooter();

		// Set sheet view
		$this->_sheetView           = new PHPExcel_Worksheet_SheetView();

    	// Drawing collection
    	$this->_drawingCollection 	= new ArrayObject();

    	// Protection
    	$this->_protection			= new PHPExcel_Worksheet_Protection();

    	// Gridlines
    	$this->_showGridlines		= true;
		$this->_printGridlines		= false;

    	// Outline summary
    	$this->_showSummaryBelow	= true;
    	$this->_showSummaryRight	= true;

    	// Default row dimension
    	$this->_defaultRowDimension = new PHPExcel_Worksheet_RowDimension(null);

    	// Default column dimension
    	$this->_defaultColumnDimension = new PHPExcel_Worksheet_ColumnDimension(null);
	}

	/**
	 * Get array of invalid characters for sheet title
	 *
	 * @return array
	 */
	public static function getInvalidCharacters()
	{
		return self::$_invalidCharacters;
	}

	/**
	 * Check sheet title for valid Excel syntax
	 *
	 * @param string $pValue The string to check
	 * @return string The valid string
	 * @throws Exception
	 */
	private static function _checkSheetTitle($pValue)
	{
		// Some of the printable ASCII characters are invalid:  * : / \ ? [ ]
		if (str_replace(self::$_invalidCharacters, '', $pValue) !== $pValue) {
			throw new Exception('Invalid character found in sheet title');
		}

		// Maximum 31 characters allowed for sheet title
		if (PHPExcel_Shared_String::CountCharacters($pValue) > 31) {
			throw new Exception('Maximum 31 characters allowed in sheet title.');
		}

		return $pValue;
	}

	/**
	 * Get collection of cells
	 *
	 * @param boolean $pSorted Also sort the cell collection?
	 * @return PHPExcel_Cell[]
	 */
	public function getCellCollection($pSorted = true)
	{
		if ($pSorted) {
			// Re-order cell collection
			$this->sortCellCollection();
		}

		return $this->_cellCollection;
	}

	/**
	 * Sort collection of cells
	 *
	 * @return PHPExcel_Worksheet
	 */
	public function sortCellCollection()
	{
		if (!$this->_cellCollectionIsSorted) {
			// Re-order cell collection
        	// uasort($this->_cellCollection, array('PHPExcel_Cell', 'compareCells')); <-- slow

			$indexed = array();
			foreach (array_keys($this->_cellCollection) as $index) {
				$rowNum = $this->_cellCollection[$index]->getRow();
				$colNum = PHPExcel_Cell::columnIndexFromString($this->_cellCollection[$index]->getColumn());

				// Columns are limited to ZZZ (18278), so 20000 is plenty to assure no conflicts
				$key =  $rowNum * 20000 + $colNum;

				$indexed[$key] = $index; // &$this->_cellCollection[$index];
			}
			ksort($indexed);

			// Rebuild cellCollection from the sorted index
			$newCellCollection = array();
		    foreach ($indexed as $index) {
		        $newCellCollection[$index] = $this->_cellCollection[$index];
			}

			$this->_cellCollection = $newCellCollection;

			$this->_cellCollectionIsSorted = true;
		}
		return $this;
	}

	/**
	 * Get collection of row dimensions
	 *
	 * @return PHPExcel_Worksheet_RowDimension[]
	 */
	public function getRowDimensions()
	{
		return $this->_rowDimensions;
	}

	/**
	 * Get default row dimension
	 *
	 * @return PHPExcel_Worksheet_RowDimension
	 */
	public function getDefaultRowDimension()
	{
		return $this->_defaultRowDimension;
	}

	/**
	 * Get collection of column dimensions
	 *
	 * @return PHPExcel_Worksheet_ColumnDimension[]
	 */
	public function getColumnDimensions()
	{
		return $this->_columnDimensions;
	}

	/**
	 * Get default column dimension
	 *
	 * @return PHPExcel_Worksheet_ColumnDimension
	 */
	public function getDefaultColumnDimension()
	{
		return $this->_defaultColumnDimension;
	}

	/**
	 * Get collection of drawings
	 *
	 * @return PHPExcel_Worksheet_BaseDrawing[]
	 */
	public function getDrawingCollection()
	{
		return $this->_drawingCollection;
	}

	/**
	 * Refresh column dimensions
	 *
	 * @return PHPExcel_Worksheet
	 */
	public function refreshColumnDimensions()
	{
		$currentColumnDimensions = $this->getColumnDimensions();
		$newColumnDimensions = array();

		foreach ($currentColumnDimensions as $objColumnDimension) {
			$newColumnDimensions[$objColumnDimension->getColumnIndex()] = $objColumnDimension;
		}

		$this->_columnDimensions = $newColumnDimensions;

		return $this;
	}

	/**
	 * Refresh row dimensions
	 *
	 * @return PHPExcel_Worksheet
	 */
	public function refreshRowDimensions()
	{
		$currentRowDimensions = $this->getRowDimensions();
		$newRowDimensions = array();

		foreach ($currentRowDimensions as $objRowDimension) {
			$newRowDimensions[$objRowDimension->getRowIndex()] = $objRowDimension;
		}

		$this->_rowDimensions = $newRowDimensions;

		return $this;
	}

    /**
     * Calculate worksheet dimension
     *
     * @return string  String containing the dimension of this worksheet
     */
    public function calculateWorksheetDimension()
    {
        // Return
        return 'A1' . ':' .  $this->getHighestColumn() . $this->getHighestRow();
    }

	/**
	 * Calculate widths for auto-size columns
	 *
	 * @param  boolean  $calculateMergeCells  Calculate merge cell width
	 * @return PHPExcel_Worksheet;
	 */
	public function calculateColumnWidths($calculateMergeCells = false)
	{
		// initialize $autoSizes array
		$autoSizes = array();
		foreach ($this->getColumnDimensions() as $colDimension) {
			if ($colDimension->getAutoSize()) {
				$autoSizes[$colDimension->getColumnIndex()] = -1;
			}
		}

		// There is only something to do if there are some auto-size columns
		if (!empty($autoSizes))
		{
			foreach ($this->getCellCollection(false) as $cell) {
				if (isset($autoSizes[$cell->getColumn()])) {
					// Is merge cell?
					$isMergeCell = false;
					foreach ($this->getMergeCells() as $cells) {
						if ($cell->isInRange($cells) && !$calculateMergeCells) {
							$isMergeCell = true; // do not calculate merge cells
							break;
						}
					}
					
					// Determine width
					if (!$isMergeCell) {			
					// Calculated value
					$cellValue = $cell->getCalculatedValue();

					// To formatted string
					$cellValue = PHPExcel_Style_NumberFormat::toFormattedString($cellValue, $this->getParent()->getCellXfByIndex($cell->getXfIndex())->getNumberFormat()->getFormatCode());

					$autoSizes[$cell->getColumn()] = max(
						(float)$autoSizes[$cell->getColumn()],
						(float)PHPExcel_Shared_Font::calculateColumnWidth(
							$this->getParent()->getCellXfByIndex($cell->getXfIndex())->getFont(),
							$cellValue,
							$this->getParent()->getCellXfByIndex($cell->getXfIndex())->getAlignment()->getTextRotation(),
							$this->getDefaultStyle()->getFont()
						)
					);
				}
			}
			}

			// adjust column widths
			foreach ($autoSizes as $columnIndex => $width) {
				if ($width == -1) $width = $this->getDefaultColumnDimension()->getWidth();
				$this->getColumnDimension($columnIndex)->setWidth($width);
			}
		}

		return $this;
    }

    /**
     * Get parent
     *
     * @return PHPExcel
     */
    public function getParent() {
    	return $this->_parent;
    }

    /**
     * Re-bind parent
     *
     * @param PHPExcel $parent
     * @return PHPExcel_Worksheet
     */
    public function rebindParent(PHPExcel $parent) {
		$namedRanges = $this->_parent->getNamedRanges();
		foreach ($namedRanges as $namedRange) {
			$parent->addNamedRange($namedRange);
		}

		$this->_parent->removeSheetByIndex(
			$this->_parent->getIndex($this)
		);
		$this->_parent = $parent;

		return $this;
    }

	/**
	 * Get title
	 *
	 * @return string
	 */
	public function getTitle()
	{
		return $this->_title;
	}

    /**
     * Set title
     *
     * @param string $pValue String containing the dimension of this worksheet
	 * @return PHPExcel_Worksheet
     */
    public function setTitle($pValue = 'Worksheet')
    {
    	// Is this a 'rename' or not?
    	if ($this->getTitle() == $pValue) {
    		return;
    	}

		// Syntax check
		self::_checkSheetTitle($pValue);

    	// Old title
    	$oldTitle = $this->getTitle();

		// Is there already such sheet name?
		if ($this->getParent()->getSheetByName($pValue)) {
			// Use name, but append with lowest possible integer

			$i = 1;
			while ($this->getParent()->getSheetByName($pValue . ' ' . $i)) {
				++$i;
			}

			$altTitle = $pValue . ' ' . $i;
			$this->setTitle($altTitle);

			return;
		}

		// Set title
        $this->_title = $pValue;

    	// New title
    	$newTitle = $this->getTitle();
    	PHPExcel_ReferenceHelper::getInstance()->updateNamedFormulas($this->getParent(), $oldTitle, $newTitle);

    	return $this;
    }

	/**
	 * Get sheet state
	 *
	 * @return string Sheet state (visible, hidden, veryHidden)
	 */
	public function getSheetState() {
		return $this->_sheetState;
	}

	/**
	 * Set sheet state
	 *
	 * @param string $value Sheet state (visible, hidden, veryHidden)
	 * @return PHPExcel_Worksheet
	 */
	public function setSheetState($value = PHPExcel_Worksheet::SHEETSTATE_VISIBLE) {
		$this->_sheetState = $value;
		return $this;
	}

    /**
     * Get page setup
     *
     * @return PHPExcel_Worksheet_PageSetup
     */
    public function getPageSetup()
    {
    	return $this->_pageSetup;
    }

    /**
     * Set page setup
     *
     * @param PHPExcel_Worksheet_PageSetup	$pValue
     * @return PHPExcel_Worksheet
     */
    public function setPageSetup(PHPExcel_Worksheet_PageSetup $pValue)
    {
   		$this->_pageSetup = $pValue;
   		return $this;
    }

    /**
     * Get page margins
     *
     * @return PHPExcel_Worksheet_PageMargins
     */
    public function getPageMargins()
    {
    	return $this->_pageMargins;
    }

    /**
     * Set page margins
     *
     * @param PHPExcel_Worksheet_PageMargins	$pValue
     * @return PHPExcel_Worksheet
     */
    public function setPageMargins(PHPExcel_Worksheet_PageMargins $pValue)
    {
   		$this->_pageMargins = $pValue;
   		return $this;
    }

    /**
     * Get page header/footer
     *
     * @return PHPExcel_Worksheet_HeaderFooter
     */
    public function getHeaderFooter()
    {
    	return $this->_headerFooter;
    }

    /**
     * Set page header/footer
     *
     * @param PHPExcel_Worksheet_HeaderFooter	$pValue
     * @return PHPExcel_Worksheet
     */
    public function setHeaderFooter(PHPExcel_Worksheet_HeaderFooter $pValue)
    {
    	$this->_headerFooter = $pValue;
    	return $this;
    }

    /**
     * Get sheet view
     *
     * @return PHPExcel_Worksheet_HeaderFooter
     */
    public function getSheetView()
    {
    	return $this->_sheetView;
    }

    /**
     * Set sheet view
     *
     * @param PHPExcel_Worksheet_SheetView	$pValue
     * @return PHPExcel_Worksheet
     */
    public function setSheetView(PHPExcel_Worksheet_SheetView $pValue)
    {
    	$this->_sheetView = $pValue;
    	return $this;
    }

    /**
     * Get Protection
     *
     * @return PHPExcel_Worksheet_Protection
     */
    public function getProtection()
    {
    	return $this->_protection;
    }

    /**
     * Set Protection
     *
     * @param PHPExcel_Worksheet_Protection	$pValue
     * @return PHPExcel_Worksheet
     */
    public function setProtection(PHPExcel_Worksheet_Protection $pValue)
    {
   		$this->_protection = $pValue;
   		return $this;
    }

    /**
     * Get highest worksheet column
     *
     * @return string Highest column name
     */
    public function getHighestColumn()
    {
		return $this->_cachedHighestColumn;
    }

    /**
     * Get highest worksheet row
     *
     * @return int Highest row number
     */
    public function getHighestRow()
    {
		return $this->_cachedHighestRow;
    }

    /**
     * Set a cell value
     *
     * @param string 	$pCoordinate	Coordinate of the cell
     * @param mixed 	$pValue			Value of the cell
     * @return PHPExcel_Worksheet
     */
    public function setCellValue($pCoordinate = 'A1', $pValue = null)
    {
    	// Set value
    	$this->getCell($pCoordinate)->setValue($pValue);

    	return $this;
    }

    /**
     * Set a cell value by using numeric cell coordinates
     *
     * @param string 	$pColumn		Numeric column coordinate of the cell
     * @param string 	$pRow			Numeric row coordinate of the cell
     * @param mixed 	$pValue			Value of the cell
     * @return PHPExcel_Worksheet
     */
    public function setCellValueByColumnAndRow($pColumn = 0, $pRow = 0, $pValue = null)
    {
    	return $this->setCellValue(PHPExcel_Cell::stringFromColumnIndex($pColumn) . $pRow, $pValue);
    }

    /**
     * Set a cell value
     *
     * @param string 	$pCoordinate	Coordinate of the cell
     * @param mixed 	$pValue			Value of the cell
     * @param string	$pDataType		Explicit data type
     * @return PHPExcel_Worksheet
     */
    public function setCellValueExplicit($pCoordinate = 'A1', $pValue = null, $pDataType = PHPExcel_Cell_DataType::TYPE_STRING)
    {
    	// Set value
    	$this->getCell($pCoordinate)->setValueExplicit($pValue, $pDataType);
    	return $this;
    }

    /**
     * Set a cell value by using numeric cell coordinates
     *
     * @param string 	$pColumn		Numeric column coordinate of the cell
     * @param string 	$pRow			Numeric row coordinate of the cell
     * @param mixed 	$pValue			Value of the cell
     * @param string	$pDataType		Explicit data type
     * @return PHPExcel_Worksheet
     */
    public function setCellValueExplicitByColumnAndRow($pColumn = 0, $pRow = 0, $pValue = null, $pDataType = PHPExcel_Cell_DataType::TYPE_STRING)
    {
    	return $this->setCellValueExplicit(PHPExcel_Cell::stringFromColumnIndex($pColumn) . $pRow, $pValue, $pDataType);
    }

    /**
     * Get cell at a specific coordinate
     *
     * @param 	string 			$pCoordinate	Coordinate of the cell
     * @throws 	Exception
     * @return 	PHPExcel_Cell 	Cell that was found
     */
    public function getCell($pCoordinate = 'A1')
    {
		// Check cell collection
		if (isset($this->_cellCollection[$pCoordinate])) {
			return $this->_cellCollection[$pCoordinate];
		}

		// Worksheet reference?
		if (strpos($pCoordinate, '!') !== false) {
			$worksheetReference = PHPExcel_Worksheet::extractSheetTitle($pCoordinate, true);
			return $this->getParent()->getSheetByName($worksheetReference[0])->getCell($worksheetReference[1]);
		}

		// Named range?
		if ((!preg_match('/^'.PHPExcel_Calculation::CALCULATION_REGEXP_CELLREF.'$/i', $pCoordinate, $matches)) &&
			(preg_match('/^'.PHPExcel_Calculation::CALCULATION_REGEXP_NAMEDRANGE.'$/i', $pCoordinate, $matches))) {
			$namedRange = PHPExcel_NamedRange::resolveRange($pCoordinate, $this);
			if (!is_null($namedRange)) {
				$pCoordinate = $namedRange->getRange();
				if ($this->getHashCode() != $namedRange->getWorksheet()->getHashCode()) {
					if (!$namedRange->getLocalOnly()) {
						return $namedRange->getWorksheet()->getCell($pCoordinate);
					} else {
						throw new Exception('Named range ' . $namedRange->getName() . ' is not accessible from within sheet ' . $this->getTitle());
					}
				} else {
					//Allow named ranges within the same sheet.
					return $this->getCell($pCoordinate);
				}
			}
		}

    	// Uppercase coordinate
    	$pCoordinate = strtoupper($pCoordinate);

    	if (strpos($pCoordinate,':') !== false || strpos($pCoordinate,',') !== false) {
    		throw new Exception('Cell coordinate can not be a range of cells.');
    	} elseif (strpos($pCoordinate,'$') !== false) {
    		throw new Exception('Cell coordinate must not be absolute.');
    	} else {
			// Create new cell object

			// Coordinates
			$aCoordinates = PHPExcel_Cell::coordinateFromString($pCoordinate);

			$this->_cellCollection[$pCoordinate] = new PHPExcel_Cell($aCoordinates[0], $aCoordinates[1], null, PHPExcel_Cell_DataType::TYPE_NULL, $this);
			$this->_cellCollectionIsSorted = false;

			if (PHPExcel_Cell::columnIndexFromString($this->_cachedHighestColumn) < PHPExcel_Cell::columnIndexFromString($aCoordinates[0]))
				$this->_cachedHighestColumn = $aCoordinates[0];

			if ($this->_cachedHighestRow < $aCoordinates[1])
				$this->_cachedHighestRow = $aCoordinates[1];

			// Cell needs appropriate xfIndex
			$rowDimensions    = $this->getRowDimensions();
			$columnDimensions = $this->getColumnDimensions();

			if ( isset($rowDimensions[$aCoordinates[1]]) && $rowDimensions[$aCoordinates[1]]->getXfIndex() !== null ) {
				// then there is a row dimension with explicit style, assign it to the cell
				$this->_cellCollection[$pCoordinate]->setXfIndex($rowDimensions[$aCoordinates[1]]->getXfIndex());

			} else if ( isset($columnDimensions[$aCoordinates[0]]) ) {
				// then there is a column dimension, assign it to the cell
				$this->_cellCollection[$pCoordinate]->setXfIndex($columnDimensions[$aCoordinates[0]]->getXfIndex());

			} else {
				// set to default index
				$this->_cellCollection[$pCoordinate]->setXfIndex(0);
			}

	        return $this->_cellCollection[$pCoordinate];
    	}
    }

    /**
     * Get cell at a specific coordinate by using numeric cell coordinates
     *
     * @param 	string $pColumn		Numeric column coordinate of the cell
     * @param 	string $pRow		Numeric row coordinate of the cell
     * @return 	PHPExcel_Cell 		Cell that was found
     */
    public function getCellByColumnAndRow($pColumn = 0, $pRow = 0)
    {
		$columnLetter = PHPExcel_Cell::stringFromColumnIndex($pColumn);
		$coordinate = $columnLetter . $pRow;

		if (!isset($this->_cellCollection[$coordinate])) {
			$this->_cellCollection[$coordinate] = new PHPExcel_Cell($columnLetter, $pRow, null, PHPExcel_Cell_DataType::TYPE_NULL, $this);
			$this->_cellCollectionIsSorted = false;

			if (PHPExcel_Cell::columnIndexFromString($this->_cachedHighestColumn) < $pColumn)
				$this->_cachedHighestColumn = $columnLetter;

			if ($this->_cachedHighestRow < $pRow)
				$this->_cachedHighestRow = $pRow;
		}

		return $this->_cellCollection[$coordinate];
    }

    /**
     * Cell at a specific coordinate exists?
     *
     * @param 	string 			$pCoordinate	Coordinate of the cell
     * @throws 	Exception
     * @return 	boolean
     */
    public function cellExists($pCoordinate = 'A1')
    {
    	// Worksheet reference?
		if (strpos($pCoordinate, '!') !== false) {
			$worksheetReference = PHPExcel_Worksheet::extractSheetTitle($pCoordinate, true);
			return $this->getParent()->getSheetByName($worksheetReference[0])->cellExists($worksheetReference[1]);
		}

		// Named range?
		if ((!preg_match('/^'.PHPExcel_Calculation::CALCULATION_REGEXP_CELLREF.'$/i', $pCoordinate, $matches)) &&
			(preg_match('/^'.PHPExcel_Calculation::CALCULATION_REGEXP_NAMEDRANGE.'$/i', $pCoordinate, $matches))) {
			$namedRange = PHPExcel_NamedRange::resolveRange($pCoordinate, $this);
			if (!is_null($namedRange)) {
				$pCoordinate = $namedRange->getRange();
				if ($this->getHashCode() != $namedRange->getWorksheet()->getHashCode()) {
					if (!$namedRange->getLocalOnly()) {
						return $namedRange->getWorksheet()->cellExists($pCoordinate);
					} else {
						throw new Exception('Named range ' . $namedRange->getName() . ' is not accessible from within sheet ' . $this->getTitle());
					}
				}
			}
		}

    	// Uppercase coordinate
    	$pCoordinate = strtoupper($pCoordinate);

    	if (strpos($pCoordinate,':') !== false || strpos($pCoordinate,',') !== false) {
    		throw new Exception('Cell coordinate can not be a range of cells.');
    	} elseif (strpos($pCoordinate,'$') !== false) {
    		throw new Exception('Cell coordinate must not be absolute.');
    	} else {
	    	// Coordinates
	    	$aCoordinates = PHPExcel_Cell::coordinateFromString($pCoordinate);

	        // Cell exists?
	        return isset($this->_cellCollection[$pCoordinate]);
    	}
    }

    /**
     * Cell at a specific coordinate by using numeric cell coordinates exists?
     *
     * @param 	string $pColumn		Numeric column coordinate of the cell
     * @param 	string $pRow		Numeric row coordinate of the cell
     * @return 	boolean
     */
    public function cellExistsByColumnAndRow($pColumn = 0, $pRow = 0)
    {
    	return $this->cellExists(PHPExcel_Cell::stringFromColumnIndex($pColumn) . $pRow);
    }

    /**
     * Get row dimension at a specific row
     *
     * @param int $pRow	Numeric index of the row
     * @return PHPExcel_Worksheet_RowDimension
     */
    public function getRowDimension($pRow = 0)
    {
    	// Found
    	$found = null;

        // Get row dimension
        if (!isset($this->_rowDimensions[$pRow])) {
        	$this->_rowDimensions[$pRow] = new PHPExcel_Worksheet_RowDimension($pRow);

			if ($this->_cachedHighestRow < $pRow)
				$this->_cachedHighestRow = $pRow;
        }
        return $this->_rowDimensions[$pRow];
    }

    /**
     * Get column dimension at a specific column
     *
     * @param string $pColumn	String index of the column
     * @return PHPExcel_Worksheet_ColumnDimension
     */
    public function getColumnDimension($pColumn = 'A')
    {
    	// Uppercase coordinate
    	$pColumn = strtoupper($pColumn);

    	// Fetch dimensions
    	if (!isset($this->_columnDimensions[$pColumn])) {
    		$this->_columnDimensions[$pColumn] = new PHPExcel_Worksheet_ColumnDimension($pColumn);

			if (PHPExcel_Cell::columnIndexFromString($this->_cachedHighestColumn) < PHPExcel_Cell::columnIndexFromString($pColumn))
				$this->_cachedHighestColumn = $pColumn;
    	}
    	return $this->_columnDimensions[$pColumn];
    }

    /**
     * Get column dimension at a specific column by using numeric cell coordinates
     *
     * @param 	string $pColumn		Numeric column coordinate of the cell
     * @param 	string $pRow		Numeric row coordinate of the cell
     * @return 	PHPExcel_Worksheet_ColumnDimension
     */
    public function getColumnDimensionByColumn($pColumn = 0)
    {
        return $this->getColumnDimension(PHPExcel_Cell::stringFromColumnIndex($pColumn));
    }

    /**
     * Get styles
     *
     * @return PHPExcel_Style[]
     */
    public function getStyles()
    {
    	return $this->_styles;
    }

    /**
     * Get default style of workbork.
     *
     * @deprecated
     * @return 	PHPExcel_Style
     * @throws 	Exception
     */
    public function getDefaultStyle()
    {
    	return $this->_parent->getDefaultStyle();
    }

    /**
     * Set default style - should only be used by PHPExcel_IReader implementations!
     *
     * @deprecated
     * @param 	PHPExcel_Style $value
     * @throws 	Exception
     * @return PHPExcel_Worksheet
     */
    public function setDefaultStyle(PHPExcel_Style $pValue)
    {
		$this->_parent->getDefaultStyle()->applyFromArray(array(
			'font' => array(
				'name' => $pValue->getFont()->getName(),
				'size' => $pValue->getFont()->getSize(),
			),
		));
		return $this;
    }

    /**
     * Get style for cell
     *
     * @param 	string 	$pCellCoordinate	Cell coordinate to get style for
     * @return 	PHPExcel_Style
     * @throws 	Exception
     */
    public function getStyle($pCellCoordinate = 'A1')
    {
		// set this sheet as active
		$this->_parent->setActiveSheetIndex($this->_parent->getIndex($this));

		// set cell coordinate as active
		$this->setSelectedCells($pCellCoordinate);

		return $this->_parent->getCellXfSupervisor();
    }

	/**
	 * Get conditional styles for a cell
	 *
	 * @param string $pCoordinate
	 * @return PHPExcel_Style_Conditional[]
	 */
	public function getConditionalStyles($pCoordinate = 'A1')
	{
		if (!isset($this->_conditionalStylesCollection[$pCoordinate])) {
			$this->_conditionalStylesCollection[$pCoordinate] = array();
		}
		return $this->_conditionalStylesCollection[$pCoordinate];
	}

	/**
	 * Do conditional styles exist for this cell?
	 *
	 * @param string $pCoordinate
	 * @return boolean
	 */
	public function conditionalStylesExists($pCoordinate = 'A1')
	{
		if (isset($this->_conditionalStylesCollection[$pCoordinate])) {
			return true;
		}
		return false;
	}

	/**
	 * Removes conditional styles for a cell
	 *
	 * @param string $pCoordinate
	 * @return PHPExcel_Worksheet
	 */
	public function removeConditionalStyles($pCoordinate = 'A1')
	{
		unset($this->_conditionalStylesCollection[$pCoordinate]);
		return $this;
	}

	/**
	 * Get collection of conditional styles
	 *
	 * @return array
	 */
	public function getConditionalStylesCollection()
	{
		return $this->_conditionalStylesCollection;
	}

	/**
	 * Set conditional styles
	 *
	 * @param $pCoordinate string E.g. 'A1'
	 * @param $pValue PHPExcel_Style_Conditional[]
	 * @return PHPExcel_Worksheet
	 */
	public function setConditionalStyles($pCoordinate = 'A1', $pValue)
	{
		$this->_conditionalStylesCollection[$pCoordinate] = $pValue;
		return $this;
	}

    /**
     * Get style for cell by using numeric cell coordinates
     *
     * @param 	int $pColumn	Numeric column coordinate of the cell
     * @param 	int $pRow		Numeric row coordinate of the cell
     * @return 	PHPExcel_Style
     */
    public function getStyleByColumnAndRow($pColumn = 0, $pRow = 0)
    {
    	return $this->getStyle(PHPExcel_Cell::stringFromColumnIndex($pColumn) . $pRow);
    }

    /**
     * Set shared cell style to a range of cells
     *
     * Please note that this will overwrite existing cell styles for cells in range!
     *
     * @deprecated
     * @param 	PHPExcel_Style	$pSharedCellStyle	Cell style to share
     * @param 	string			$pRange				Range of cells (i.e. "A1:B10"), or just one cell (i.e. "A1")
     * @throws	Exception
     * @return PHPExcel_Worksheet
     */
     public function setSharedStyle(PHPExcel_Style $pSharedCellStyle = null, $pRange = '')
    {
		$this->duplicateStyle($pSharedCellStyle, $pRange);
		return $this;
    }

    /**
     * Duplicate cell style to a range of cells
     *
     * Please note that this will overwrite existing cell styles for cells in range!
     *
     * @param 	PHPExcel_Style	$pCellStyle	Cell style to duplicate
     * @param 	string			$pRange		Range of cells (i.e. "A1:B10"), or just one cell (i.e. "A1")
     * @throws	Exception
     * @return PHPExcel_Worksheet
     */
    public function duplicateStyle(PHPExcel_Style $pCellStyle = null, $pRange = '')
    {
    	// make sure we have a real style and not supervisor
		$style = $pCellStyle->getIsSupervisor() ? $pCellStyle->getSharedComponent() : $pCellStyle;

		// Add the style to the workbook if necessary
		$workbook = $this->_parent;
		if ($existingStyle = $this->_parent->getCellXfByHashCode($pCellStyle->getHashCode())) {
			// there is already such cell Xf in our collection
			$xfIndex = $existingStyle->getIndex();
		} else {
			// we don't have such a cell Xf, need to add
			$workbook->addCellXf($pCellStyle);
			$xfIndex = $pCellStyle->getIndex();
		}

		// Uppercase coordinate
    	$pRange = strtoupper($pRange);

   		// Is it a cell range or a single cell?
   		$rangeA 	= '';
   		$rangeB 	= '';
   		if (strpos($pRange, ':') === false) {
   			$rangeA = $pRange;
   			$rangeB = $pRange;
   		} else {
   			list($rangeA, $rangeB) = explode(':', $pRange);
   		}

   		// Calculate range outer borders
   		$rangeStart = PHPExcel_Cell::coordinateFromString($rangeA);
   		$rangeEnd 	= PHPExcel_Cell::coordinateFromString($rangeB);

   		// Translate column into index
   		$rangeStart[0]	= PHPExcel_Cell::columnIndexFromString($rangeStart[0]) - 1;
   		$rangeEnd[0]	= PHPExcel_Cell::columnIndexFromString($rangeEnd[0]) - 1;

   		// Make sure we can loop upwards on rows and columns
   		if ($rangeStart[0] > $rangeEnd[0] && $rangeStart[1] > $rangeEnd[1]) {
   			$tmp = $rangeStart;
   			$rangeStart = $rangeEnd;
   			$rangeEnd = $tmp;
   		}

   		// Loop through cells and apply styles
   		for ($col = $rangeStart[0]; $col <= $rangeEnd[0]; ++$col) {
   			for ($row = $rangeStart[1]; $row <= $rangeEnd[1]; ++$row) {
   				$this->getCell(PHPExcel_Cell::stringFromColumnIndex($col) . $row)->setXfIndex($xfIndex);
   			}
   		}

   		return $this;
    }

    /**
     * Duplicate cell style array to a range of cells
     *
     * Please note that this will overwrite existing cell styles for cells in range,
     * if they are in the styles array. For example, if you decide to set a range of
     * cells to font bold, only include font bold in the styles array.
     *
     * @deprecated
     * @param	array			$pStyles	Array containing style information
     * @param 	string			$pRange		Range of cells (i.e. "A1:B10"), or just one cell (i.e. "A1")
     * @param 	boolean			$pAdvanced	Advanced mode for setting borders.
     * @throws	Exception
     * @return PHPExcel_Worksheet
     */
    public function duplicateStyleArray($pStyles = null, $pRange = '', $pAdvanced = true)
    {
		$this->getStyle($pRange)->applyFromArray($pStyles, $pAdvanced);
    	return $this;
    }

    /**
     * Set break on a cell
     *
     * @param 	string			$pCell		Cell coordinate (e.g. A1)
     * @param 	int				$pBreak		Break type (type of PHPExcel_Worksheet::BREAK_*)
     * @throws	Exception
     * @return PHPExcel_Worksheet
     */
    public function setBreak($pCell = 'A1', $pBreak = PHPExcel_Worksheet::BREAK_NONE)
    {
    	// Uppercase coordinate
    	$pCell = strtoupper($pCell);

    	if ($pCell != '') {
    		$this->_breaks[$pCell] = $pBreak;
    	} else {
    		throw new Exception('No cell coordinate specified.');
    	}

    	return $this;
    }

    /**
     * Set break on a cell by using numeric cell coordinates
     *
     * @param 	int 	$pColumn	Numeric column coordinate of the cell
     * @param 	int 	$pRow		Numeric row coordinate of the cell
     * @param 	int		$pBreak		Break type (type of PHPExcel_Worksheet::BREAK_*)
     * @throws	Exception
     * @return PHPExcel_Worksheet
     */
    public function setBreakByColumnAndRow($pColumn = 0, $pRow = 0, $pBreak = PHPExcel_Worksheet::BREAK_NONE)
    {
    	return $this->setBreak(PHPExcel_Cell::stringFromColumnIndex($pColumn) . $pRow, $pBreak);
    }

    /**
     * Get breaks
     *
     * @return array[]
     */
    public function getBreaks()
    {
    	return $this->_breaks;
    }

    /**
     * Set merge on a cell range
     *
     * @param 	string			$pRange		Cell range (e.g. A1:E1)
     * @throws	Exception
     * @return PHPExcel_Worksheet
     */
    public function mergeCells($pRange = 'A1:A1')
    {
    	// Uppercase coordinate
    	$pRange = strtoupper($pRange);

    	if (strpos($pRange,':') !== false) {
    		$this->_mergeCells[$pRange] = $pRange;

			// make sure cells are created
			$aReferences = PHPExcel_Cell::extractAllCellReferencesInRange($pRange);
			foreach ($aReferences as $reference) {
				$this->getCell($reference);
			}

    	} else {
    		throw new Exception('Merge must be set on a range of cells.');
    	}

    	return $this;
    }

    /**
     * Set merge on a cell range by using numeric cell coordinates
     *
     * @param 	int $pColumn1	Numeric column coordinate of the first cell
     * @param 	int $pRow1		Numeric row coordinate of the first cell
     * @param 	int $pColumn2	Numeric column coordinate of the last cell
     * @param 	int $pRow2		Numeric row coordinate of the last cell
     * @throws	Exception
     * @return PHPExcel_Worksheet
     */
    public function mergeCellsByColumnAndRow($pColumn1 = 0, $pRow1 = 0, $pColumn2 = 0, $pRow2 = 0)
    {
    	$cellRange = PHPExcel_Cell::stringFromColumnIndex($pColumn1) . $pRow1 . ':' . PHPExcel_Cell::stringFromColumnIndex($pColumn2) . $pRow2;
    	return $this->mergeCells($cellRange);
    }

    /**
     * Remove merge on a cell range
     *
     * @param 	string			$pRange		Cell range (e.g. A1:E1)
     * @throws	Exception
     * @return PHPExcel_Worksheet
     */
    public function unmergeCells($pRange = 'A1:A1')
    {
    	// Uppercase coordinate
    	$pRange = strtoupper($pRange);

    	if (strpos($pRange,':') !== false) {
    		if (isset($this->_mergeCells[$pRange])) {
    			unset($this->_mergeCells[$pRange]);
    		} else {
    			throw new Exception('Cell range ' . $pRange . ' not known as merged.');
    		}
    	} else {
    		throw new Exception('Merge can only be removed from a range of cells.');
    	}

    	return $this;
    }

    /**
     * Remove merge on a cell range by using numeric cell coordinates
     *
     * @param 	int $pColumn1	Numeric column coordinate of the first cell
     * @param 	int $pRow1		Numeric row coordinate of the first cell
     * @param 	int $pColumn2	Numeric column coordinate of the last cell
     * @param 	int $pRow2		Numeric row coordinate of the last cell
     * @throws	Exception
     * @return PHPExcel_Worksheet
     */
    public function unmergeCellsByColumnAndRow($pColumn1 = 0, $pRow1 = 0, $pColumn2 = 0, $pRow2 = 0)
    {
    	$cellRange = PHPExcel_Cell::stringFromColumnIndex($pColumn1) . $pRow1 . ':' . PHPExcel_Cell::stringFromColumnIndex($pColumn2) . $pRow2;
    	return $this->unmergeCells($cellRange);
    }

    /**
     * Get merge cells array.
     *
     * @return array[]
     */
    public function getMergeCells()
    {
    	return $this->_mergeCells;
    }

	/**
	 * Set merge cells array for the entire sheet. Use instead mergeCells() to merge
	 * a single cell range.
	 *
	 * @param array
	 */
	public function setMergeCells($pValue = array())
	{
		$this->_mergeCells = $pValue;

		return $this;
	}

    /**
     * Set protection on a cell range
     *
     * @param 	string			$pRange				Cell (e.g. A1) or cell range (e.g. A1:E1)
     * @param 	string			$pPassword			Password to unlock the protection
     * @param 	boolean 		$pAlreadyHashed 	If the password has already been hashed, set this to true
     * @throws	Exception
     * @return PHPExcel_Worksheet
     */
    public function protectCells($pRange = 'A1', $pPassword = '', $pAlreadyHashed = false)
    {
    	// Uppercase coordinate
    	$pRange = strtoupper($pRange);

    	if (!$pAlreadyHashed) {
    		$pPassword = PHPExcel_Shared_PasswordHasher::hashPassword($pPassword);
    	}
    	$this->_protectedCells[$pRange] = $pPassword;

    	return $this;
    }

    /**
     * Set protection on a cell range by using numeric cell coordinates
     *
     * @param 	int 	$pColumn1			Numeric column coordinate of the first cell
     * @param 	int 	$pRow1				Numeric row coordinate of the first cell
     * @param 	int 	$pColumn2			Numeric column coordinate of the last cell
     * @param 	int 	$pRow2				Numeric row coordinate of the last cell
     * @param 	string	$pPassword			Password to unlock the protection
     * @param 	boolean $pAlreadyHashed 	If the password has already been hashed, set this to true
     * @throws	Exception
     * @return PHPExcel_Worksheet
     */
    public function protectCellsByColumnAndRow($pColumn1 = 0, $pRow1 = 0, $pColumn2 = 0, $pRow2 = 0, $pPassword = '', $pAlreadyHashed = false)
    {
    	$cellRange = PHPExcel_Cell::stringFromColumnIndex($pColumn1) . $pRow1 . ':' . PHPExcel_Cell::stringFromColumnIndex($pColumn2) . $pRow2;
    	return $this->protectCells($cellRange, $pPassword, $pAlreadyHashed);
    }

    /**
     * Remove protection on a cell range
     *
     * @param 	string			$pRange		Cell (e.g. A1) or cell range (e.g. A1:E1)
     * @throws	Exception
     * @return PHPExcel_Worksheet
     */
    public function unprotectCells($pRange = 'A1')
    {
    	// Uppercase coordinate
    	$pRange = strtoupper($pRange);

    	if (isset($this->_protectedCells[$pRange])) {
    		unset($this->_protectedCells[$pRange]);
    	} else {
    		throw new Exception('Cell range ' . $pRange . ' not known as protected.');
    	}
    	return $this;
    }

    /**
     * Remove protection on a cell range by using numeric cell coordinates
     *
     * @param 	int 	$pColumn1			Numeric column coordinate of the first cell
     * @param 	int 	$pRow1				Numeric row coordinate of the first cell
     * @param 	int 	$pColumn2			Numeric column coordinate of the last cell
     * @param 	int 	$pRow2				Numeric row coordinate of the last cell
     * @param 	string	$pPassword			Password to unlock the protection
     * @param 	boolean $pAlreadyHashed 	If the password has already been hashed, set this to true
     * @throws	Exception
     * @return PHPExcel_Worksheet
     */
    public function unprotectCellsByColumnAndRow($pColumn1 = 0, $pRow1 = 0, $pColumn2 = 0, $pRow2 = 0, $pPassword = '', $pAlreadyHashed = false)
    {
    	$cellRange = PHPExcel_Cell::stringFromColumnIndex($pColumn1) . $pRow1 . ':' . PHPExcel_Cell::stringFromColumnIndex($pColumn2) . $pRow2;
    	return $this->unprotectCells($cellRange, $pPassword, $pAlreadyHashed);
    }

    /**
     * Get protected cells
     *
     * @return array[]
     */
    public function getProtectedCells()
    {
    	return $this->_protectedCells;
    }

    /**
     * Get Autofilter Range
     *
     * @return string
     */
    public function getAutoFilter()
    {
    	return $this->_autoFilter;
    }

    /**
     * Set Autofilter Range
     *
     * @param 	string		$pRange		Cell range (i.e. A1:E10)
     * @throws 	Exception
     * @return PHPExcel_Worksheet
     */
    public function setAutoFilter($pRange = '')
    {
    	// Uppercase coordinate
    	$pRange = strtoupper($pRange);

    	if (strpos($pRange,':') !== false) {
    		$this->_autoFilter = $pRange;
    	} else {
    		throw new Exception('Autofilter must be set on a range of cells.');
    	}
    	return $this;
    }

    /**
     * Set Autofilter Range by using numeric cell coordinates
     *
     * @param 	int 	$pColumn1	Numeric column coordinate of the first cell
     * @param 	int 	$pRow1		Numeric row coordinate of the first cell
     * @param 	int 	$pColumn2	Numeric column coordinate of the second cell
     * @param 	int 	$pRow2		Numeric row coordinate of the second cell
     * @throws 	Exception
     * @return PHPExcel_Worksheet
     */
    public function setAutoFilterByColumnAndRow($pColumn1 = 0, $pRow1 = 0, $pColumn2 = 0, $pRow2 = 0)
    {
    	return $this->setAutoFilter(
    		PHPExcel_Cell::stringFromColumnIndex($pColumn1) . $pRow1
    		. ':' .
    		PHPExcel_Cell::stringFromColumnIndex($pColumn2) . $pRow2
    	);
    }

    /**
     * Get Freeze Pane
     *
     * @return string
     */
    public function getFreezePane()
    {
    	return $this->_freezePane;
    }

    /**
     * Freeze Pane
     *
     * @param 	string		$pCell		Cell (i.e. A1)
     * @throws 	Exception
     * @return PHPExcel_Worksheet
     */
    public function freezePane($pCell = '')
    {
    	// Uppercase coordinate
    	$pCell = strtoupper($pCell);

    	if (strpos($pCell,':') === false && strpos($pCell,',') === false) {
    		$this->_freezePane = $pCell;
    	} else {
    		throw new Exception('Freeze pane can not be set on a range of cells.');
    	}
    	return $this;
    }

    /**
     * Freeze Pane by using numeric cell coordinates
     *
     * @param 	int 	$pColumn	Numeric column coordinate of the cell
     * @param 	int 	$pRow		Numeric row coordinate of the cell
     * @throws 	Exception
     * @return PHPExcel_Worksheet
     */
    public function freezePaneByColumnAndRow($pColumn = 0, $pRow = 0)
    {
    	return $this->freezePane(PHPExcel_Cell::stringFromColumnIndex($pColumn) . $pRow);
    }

    /**
     * Unfreeze Pane
     *
     * @return PHPExcel_Worksheet
     */
    public function unfreezePane()
    {
    	return $this->freezePane('');
    }

    /**
     * Insert a new row, updating all possible related data
     *
     * @param 	int	$pBefore	Insert before this one
     * @param 	int	$pNumRows	Number of rows to insert
     * @throws 	Exception
     * @return PHPExcel_Worksheet
     */
    public function insertNewRowBefore($pBefore = 1, $pNumRows = 1) {
    	if ($pBefore >= 1) {
    		$objReferenceHelper = PHPExcel_ReferenceHelper::getInstance();
    		$objReferenceHelper->insertNewBefore('A' . $pBefore, 0, $pNumRows, $this);
    	} else {
    		throw new Exception("Rows can only be inserted before at least row 1.");
    	}
    	return $this;
    }

    /**
     * Insert a new column, updating all possible related data
     *
     * @param 	int	$pBefore	Insert before this one
     * @param 	int	$pNumCols	Number of columns to insert
     * @throws 	Exception
     * @return PHPExcel_Worksheet
     */
    public function insertNewColumnBefore($pBefore = 'A', $pNumCols = 1) {
    	if (!is_numeric($pBefore)) {
    		$objReferenceHelper = PHPExcel_ReferenceHelper::getInstance();
    		$objReferenceHelper->insertNewBefore($pBefore . '1', $pNumCols, 0, $this);
    	} else {
    		throw new Exception("Column references should not be numeric.");
    	}
    	return $this;
    }

    /**
     * Insert a new column, updating all possible related data
     *
     * @param 	int	$pBefore	Insert before this one (numeric column coordinate of the cell)
     * @param 	int	$pNumCols	Number of columns to insert
     * @throws 	Exception
     * @return PHPExcel_Worksheet
     */
    public function insertNewColumnBeforeByIndex($pBefore = 0, $pNumCols = 1) {
    	if ($pBefore >= 0) {
    		return $this->insertNewColumnBefore(PHPExcel_Cell::stringFromColumnIndex($pBefore), $pNumCols);
    	} else {
    		throw new Exception("Columns can only be inserted before at least column A (0).");
    	}
    }

    /**
     * Delete a row, updating all possible related data
     *
     * @param 	int	$pRow		Remove starting with this one
     * @param 	int	$pNumRows	Number of rows to remove
     * @throws 	Exception
     * @return PHPExcel_Worksheet
     */
    public function removeRow($pRow = 1, $pNumRows = 1) {
    	if ($pRow >= 1) {
    		$objReferenceHelper = PHPExcel_ReferenceHelper::getInstance();
    		$objReferenceHelper->insertNewBefore('A' . ($pRow + $pNumRows), 0, -$pNumRows, $this);
    	} else {
    		throw new Exception("Rows to be deleted should at least start from row 1.");
    	}
    	return $this;
    }

    /**
     * Remove a column, updating all possible related data
     *
     * @param 	int	$pColumn	Remove starting with this one
     * @param 	int	$pNumCols	Number of columns to remove
     * @throws 	Exception
     * @return PHPExcel_Worksheet
     */
    public function removeColumn($pColumn = 'A', $pNumCols = 1) {
    	if (!is_numeric($pColumn)) {
    		$pColumn = PHPExcel_Cell::stringFromColumnIndex(PHPExcel_Cell::columnIndexFromString($pColumn) - 1 + $pNumCols);
    		$objReferenceHelper = PHPExcel_ReferenceHelper::getInstance();
    		$objReferenceHelper->insertNewBefore($pColumn . '1', -$pNumCols, 0, $this);
    	} else {
    		throw new Exception("Column references should not be numeric.");
    	}
    	return $this;
    }

    /**
     * Remove a column, updating all possible related data
     *
     * @param 	int	$pColumn	Remove starting with this one (numeric column coordinate of the cell)
     * @param 	int	$pNumCols	Number of columns to remove
     * @throws 	Exception
     * @return PHPExcel_Worksheet
     */
    public function removeColumnByIndex($pColumn = 0, $pNumCols = 1) {
    	if ($pColumn >= 0) {
    		return $this->removeColumn(PHPExcel_Cell::stringFromColumnIndex($pColumn), $pNumCols);
    	} else {
    		throw new Exception("Columns can only be inserted before at least column A (0).");
    	}
    }

    /**
     * Show gridlines?
     *
     * @return boolean
     */
    public function getShowGridlines() {
    	return $this->_showGridlines;
    }

    /**
     * Set show gridlines
     *
     * @param boolean $pValue	Show gridlines (true/false)
     * @return PHPExcel_Worksheet
     */
    public function setShowGridlines($pValue = false) {
    	$this->_showGridlines = $pValue;
    	return $this;
    }

	/**
	* Print gridlines?
	*
	* @return boolean
	*/
	public function getPrintGridlines() {
		return $this->_printGridlines;
	}

	/**
	* Set print gridlines
	*
	* @param boolean $pValue Print gridlines (true/false)
	* @return PHPExcel_Worksheet
	*/
	public function setPrintGridlines($pValue = false) {
		$this->_printGridlines = $pValue;
		return $this;
	}

    /**
     * Show summary below? (Row/Column outlining)
     *
     * @return boolean
     */
    public function getShowSummaryBelow() {
    	return $this->_showSummaryBelow;
    }

    /**
     * Set show summary below
     *
     * @param boolean $pValue	Show summary below (true/false)
     * @return PHPExcel_Worksheet
     */
    public function setShowSummaryBelow($pValue = true) {
    	$this->_showSummaryBelow = $pValue;
    	return $this;
    }

    /**
     * Show summary right? (Row/Column outlining)
     *
     * @return boolean
     */
    public function getShowSummaryRight() {
    	return $this->_showSummaryRight;
    }

    /**
     * Set show summary right
     *
     * @param boolean $pValue	Show summary right (true/false)
     * @return PHPExcel_Worksheet
     */
    public function setShowSummaryRight($pValue = true) {
    	$this->_showSummaryRight = $pValue;
    	return $this;
    }

    /**
     * Get comments
     *
     * @return PHPExcel_Comment[]
     */
    public function getComments()
    {
    	return $this->_comments;
    }

    /**
     * Get comment for cell
     *
     * @param 	string 	$pCellCoordinate	Cell coordinate to get comment for
     * @return 	PHPExcel_Comment
     * @throws 	Exception
     */
    public function getComment($pCellCoordinate = 'A1')
    {
    	// Uppercase coordinate
    	$pCellCoordinate = strtoupper($pCellCoordinate);

    	if (strpos($pCellCoordinate,':') !== false || strpos($pCellCoordinate,',') !== false) {
    		throw new Exception('Cell coordinate string can not be a range of cells.');
    	} else if (strpos($pCellCoordinate,'$') !== false) {
    		throw new Exception('Cell coordinate string must not be absolute.');
    	} else if ($pCellCoordinate == '') {
    		throw new Exception('Cell coordinate can not be zero-length string.');
    	} else {
    		// Check if we already have a comment for this cell.
    		// If not, create a new comment.
    		if (isset($this->_comments[$pCellCoordinate])) {
    			return $this->_comments[$pCellCoordinate];
    		} else {
    			$newComment = new PHPExcel_Comment();
    			$this->_comments[$pCellCoordinate] = $newComment;
    			return $newComment;
    		}
    	}
    }

    /**
     * Get comment for cell by using numeric cell coordinates
     *
     * @param 	int $pColumn	Numeric column coordinate of the cell
     * @param 	int $pRow		Numeric row coordinate of the cell
     * @return 	PHPExcel_Comment
     */
    public function getCommentByColumnAndRow($pColumn = 0, $pRow = 0)
    {
    	return $this->getComment(PHPExcel_Cell::stringFromColumnIndex($pColumn) . $pRow);
    }

    /**
     * Get selected cell
     *
     * @deprecated
     * @return string
     */
    public function getSelectedCell()
    {
    	return $this->getSelectedCells();
    }

    /**
     * Get active cell
     *
     * @return string Example: 'A1'
     */
    public function getActiveCell()
    {
    	return $this->_activeCell;
    }

    /**
     * Get selected cells
     *
     * @return string
     */
    public function getSelectedCells()
    {
    	return $this->_selectedCells;
    }

    /**
     * Selected cell
     *
     * @param 	string		$pCell		Cell (i.e. A1)
     * @return PHPExcel_Worksheet
     */
    public function setSelectedCell($pCoordinate = 'A1')
    {
    	return $this->setSelectedCells($pCoordinate);
    }

    /**
     * Select a range of cells.
     *
     * @param 	string		$pCoordinate	Cell range, examples: 'A1', 'B2:G5', 'A:C', '3:6'
     * @throws 	Exception
     * @return PHPExcel_Worksheet
     */
    public function setSelectedCells($pCoordinate = 'A1')
    {
		// Uppercase coordinate
    	$pCoordinate = strtoupper($pCoordinate);

		// Convert 'A' to 'A:A'
		$pCoordinate = preg_replace('/^([A-Z]+)$/', '${1}:${1}', $pCoordinate);

		// Convert '1' to '1:1'
		$pCoordinate = preg_replace('/^([0-9]+)$/', '${1}:${1}', $pCoordinate);

		// Convert 'A:C' to 'A1:C1048576'
		$pCoordinate = preg_replace('/^([A-Z]+):([A-Z]+)$/', '${1}1:${2}1048576', $pCoordinate);

    	// Convert '1:3' to 'A1:XFD3'
		$pCoordinate = preg_replace('/^([0-9]+):([0-9]+)$/', 'A${1}:XFD${2}', $pCoordinate);

    	if (strpos($pCoordinate,':') !== false || strpos($pCoordinate,',') !== false) {
			list($first, ) = PHPExcel_Cell::splitRange($pCoordinate);
			$this->_activeCell = $first[0];
		} else {
			$this->_activeCell = $pCoordinate;
		}
		$this->_selectedCells = $pCoordinate;
    	return $this;
    }

    /**
     * Selected cell by using numeric cell coordinates
     *
     * @param 	int 	$pColumn	Numeric column coordinate of the cell
     * @param 	int 	$pRow		Numeric row coordinate of the cell
     * @throws 	Exception
     * @return PHPExcel_Worksheet
     */
    public function setSelectedCellByColumnAndRow($pColumn = 0, $pRow = 0)
    {
    	return $this->setSelectedCells(PHPExcel_Cell::stringFromColumnIndex($pColumn) . $pRow);
    }

    /**
	 * Get right-to-left
	 *
	 * @return boolean
     */
    public function getRightToLeft() {
    	return $this->_rightToLeft;
    }

    /**
     * Set right-to-left
     *
     * @param boolean $value Right-to-left true/false
     * @return PHPExcel_Worksheet
     */
    public function setRightToLeft($value = false) {
    	$this->_rightToLeft = $value;
    	return $this;
    }

    /**
     * Fill worksheet from values in array
     *
     * @param array $source	Source array
     * @param mixed $nullValue Value in source array that stands for blank cell
     * @throws Exception
     * @return PHPExcel_Worksheet
     */
    public function fromArray($source = null, $nullValue = null, $pCell = 'A1') {
    	if (is_array($source)) {
			// start coordinate
			list ($startColumn, $startRow) = PHPExcel_Cell::coordinateFromString($pCell);
			$startColumn = PHPExcel_Cell::columnIndexFromString($startColumn) - 1;

			// Loop through $source
			$currentRow = $startRow - 1;
			$rowData = null;
			foreach ($source as $rowData) {
				++$currentRow;

				$rowCount = count($rowData);
				for ($i = 0; $i < $rowCount; ++$i) {
					if ($rowData[$i] != $nullValue) {
						// Set cell value
						$this->setCellValue(
							PHPExcel_Cell::stringFromColumnIndex($i + $startColumn) . $currentRow, $rowData[$i]
						);
					}
				}
			}
    	} else {
    		throw new Exception("Parameter \$source should be an array.");
    	}
    	return $this;
    }

    /**
     * Create array from worksheet
     *
     * @param mixed $nullValue Value treated as "null"
     * @param boolean $calculateFormulas Should formulas be calculated?
     * @return array
     */
    public function toArray($nullValue = null, $calculateFormulas = true) {
    	// Returnvalue
    	$returnValue = array();

        // Garbage collect...
        $this->garbageCollect();

    	// Get worksheet dimension
    	$dimension = explode(':', $this->calculateWorksheetDimension());
    	$dimension[0] = PHPExcel_Cell::coordinateFromString($dimension[0]);
    	$dimension[0][0] = PHPExcel_Cell::columnIndexFromString($dimension[0][0]) - 1;
    	$dimension[1] = PHPExcel_Cell::coordinateFromString($dimension[1]);
    	$dimension[1][0] = PHPExcel_Cell::columnIndexFromString($dimension[1][0]) - 1;

    	// Loop through cells
    	for ($row = $dimension[0][1]; $row <= $dimension[1][1]; ++$row) {
    		for ($column = $dimension[0][0]; $column <= $dimension[1][0]; ++$column) {
    			// Cell exists?
    			if ($this->cellExistsByColumnAndRow($column, $row)) {
    				$cell = $this->getCellByColumnAndRow($column, $row);

    				if ($cell->getValue() instanceof PHPExcel_RichText) {
    					$returnValue[$row][$column] = $cell->getValue()->getPlainText();
    				} else {
	    				if ($calculateFormulas) {
	    					$returnValue[$row][$column] = $cell->getCalculatedValue();
	    				} else {
	    					$returnValue[$row][$column] = $cell->getValue();
	    				}
    				}

					$style = $this->_parent->getCellXfByIndex($cell->getXfIndex());

    				$returnValue[$row][$column] = PHPExcel_Style_NumberFormat::toFormattedString($returnValue[$row][$column], $style->getNumberFormat()->getFormatCode());
    			} else {
    				$returnValue[$row][$column] = $nullValue;
    			}
    		}
    	}

    	// Return
    	return $returnValue;
    }

	/**
	 * Get row iterator
	 *
	 * @return PHPExcel_Worksheet_RowIterator
	 */
	public function getRowIterator() {
		return new PHPExcel_Worksheet_RowIterator($this);
	}

    /**
     * Run PHPExcel garabage collector.
     *
     * @return PHPExcel_Worksheet
     */
    public function garbageCollect() {
    	// Build a reference table from images
    	$imageCoordinates = array();
  		$iterator = $this->getDrawingCollection()->getIterator();
   		while ($iterator->valid()) {
   			$imageCoordinates[$iterator->current()->getCoordinates()] = true;

   			$iterator->next();
   		}

		// Lookup highest column and highest row if cells are cleaned
		$highestColumn = -1;
		$highestRow    = 1;

    	// Find cells that can be cleaned
    	foreach ($this->_cellCollection as $coordinate => $cell) {
    		// Can be cleaned?
    		$canBeCleaned = false;

    		/* Excel doesn't remove such empty cells
			// Empty value?
    		if (is_null($cell->getValue()) || (!is_object($cell->getValue()) && $cell->getValue() === '' && !$cell->hasHyperlink())) {
				// default style ?
				if ($cell->getXfIndex() == 0) {
					$canBeCleaned = true;
				}
    		}
			*/

    		// Referenced in image?
    		if (isset($imageCoordinates[$coordinate]) && $imageCoordinates[$coordinate] === true) {
    			$canBeCleaned = false;
    		}

    		// Clean?
    		if ($canBeCleaned) {
				// Remove the cell
    			unset($this->_cellCollection[$coordinate]);
    		} else {
				// Determine highest column and row
				if ($highestColumn < PHPExcel_Cell::columnIndexFromString($cell->getColumn())) {
					$highestColumn = PHPExcel_Cell::columnIndexFromString($cell->getColumn());
				}
				if ($cell->getRow() > $highestRow) {
					$highestRow = $cell->getRow();
				}
			}
    	}

        // Loop through column dimensions
        foreach ($this->_columnDimensions as $dimension) {
        	if ($highestColumn < PHPExcel_Cell::columnIndexFromString($dimension->getColumnIndex())) {
        		$highestColumn = PHPExcel_Cell::columnIndexFromString($dimension->getColumnIndex());
        	}
        }

        // Loop through row dimensions
        foreach ($this->_rowDimensions as $dimension) {
        	if ($highestRow < $dimension->getRowIndex()) {
        		$highestRow = $dimension->getRowIndex();
        	}
        }

		// Cache values
		if ($highestColumn < 0) {
			$this->_cachedHighestColumn = 'A';
		} else {
			$this->_cachedHighestColumn = PHPExcel_Cell::stringFromColumnIndex(--$highestColumn);
		}
		$this->_cachedHighestRow = $highestRow;

		// Return
    	return $this;
    }

	/**
	 * Get hash code
	 *
	 * @return string	Hash code
	 */
	public function getHashCode() {
    	return md5(
    		  $this->_title
    		. $this->_autoFilter
    		. ($this->_protection->isProtectionEnabled() ? 't' : 'f')
    		//. $this->calculateWorksheetDimension()
    		. __CLASS__
    	);
    }

    /**
     * Extract worksheet title from range.
     *
     * Example: extractSheetTitle('test!A1') ==> 'A1'
     * Example: extractSheetTitle('test!A1', true) ==> array('test', 'A1');
     *
     * @param string $pRange	Range to extract title from
     * @param bool $returnRange	Return range? (see example)
     * @return mixed
     */
    public static function extractSheetTitle($pRange, $returnRange = false) {
    	// Sheet title included?
    	if (strpos($pRange, '!') === false) {
    		return '';
    	}

    	// Position of separator exclamation mark
		$sep = strrpos($pRange, '!');

		// Extract sheet title
		$reference[0] = substr($pRange, 0, $sep);
		$reference[1] = substr($pRange, $sep + 1);

    	// Strip possible enclosing single quotes
    	if (strpos($reference[0], '\'') === 0) {
    		$reference[0] = substr($reference[0], 1);
    	}
    	if (strrpos($reference[0], '\'') === strlen($reference[0]) - 1) {
    		$reference[0] = substr($reference[0], 0, strlen($reference[0]) - 1);
    	}

    	if ($returnRange) {
    		return $reference;
    	} else {
    		return $reference[1];
    	}
    }

	/**
	 * Get hyperlink
	 *
	 * @param string $pCellCoordinate	Cell coordinate to get hyperlink for
	 */
	public function getHyperlink($pCellCoordinate = 'A1')
	{
		// return hyperlink if we already have one
		if (isset($this->_hyperlinkCollection[$pCellCoordinate])) {
			return $this->_hyperlinkCollection[$pCellCoordinate];
		}

		// else create hyperlink
		$cell = $this->getCell($pCellCoordinate);
		$this->_hyperlinkCollection[$pCellCoordinate] = new PHPExcel_Cell_Hyperlink($cell);
		return $this->_hyperlinkCollection[$pCellCoordinate];
	}

	/**
	 * Set hyperlnk
	 *
	 * @param string $pCellCoordinate	Cell coordinate to insert hyperlink
	 * @param 	PHPExcel_Cell_Hyperlink	$pHyperlink
	 * @return PHPExcel_Worksheet
	 */
	public function setHyperlink($pCellCoordinate = 'A1', PHPExcel_Cell_Hyperlink $pHyperlink = null)
	{
		if ($pHyperlink === null) {
			unset($this->_hyperlinkCollection[$pCellCoordinate]);
		} else {
			$this->_hyperlinkCollection[$pCellCoordinate] = $pHyperlink;
			$pHyperlink->setParent($this->getCell($pCellCoordinate));
		}
		return $this;
	}

	/**
	 * Hyperlink at a specific coordinate exists?
	 *
	 * @param string $pCellCoordinate
	 * @return boolean
	 */
	public function hyperlinkExists($pCoordinate = 'A1')
	{
		return isset($this->_hyperlinkCollection[$pCoordinate]);
	}

	/**
	 * Get collection of hyperlinks
	 *
	 * @return PHPExcel_Cell_Hyperlink[]
	 */
	public function getHyperlinkCollection()
	{
		return $this->_hyperlinkCollection;
	}

	/**
	 * Get data validation
	 *
	 * @param string $pCellCoordinate	Cell coordinate to get data validation for
	 */
	public function getDataValidation($pCellCoordinate = 'A1')
	{
		// return data validation if we already have one
		if (isset($this->_dataValidationCollection[$pCellCoordinate])) {
			return $this->_dataValidationCollection[$pCellCoordinate];
		}

		// else create data validation
		$cell = $this->getCell($pCellCoordinate);
		$this->_dataValidationCollection[$pCellCoordinate] = new PHPExcel_Cell_DataValidation($cell);
		return $this->_dataValidationCollection[$pCellCoordinate];
	}

	/**
	 * Set data validation
	 *
	 * @param string $pCellCoordinate	Cell coordinate to insert data validation
	 * @param 	PHPExcel_Cell_DataValidation	$pDataValidation
	 * @return PHPExcel_Worksheet
	 */
	public function setDataValidation($pCellCoordinate = 'A1', PHPExcel_Cell_DataValidation $pDataValidation = null)
	{
		if ($pDataValidation === null) {
			unset($this->_dataValidationCollection[$pCellCoordinate]);
		} else {
			$this->_dataValidationCollection[$pCellCoordinate] = $pDataValidation;
			$pDataValidation->setParent($this->getCell($pCellCoordinate));
		}
		return $this;
	}

	/**
	 * Data validation at a specific coordinate exists?
	 *
	 * @param string $pCellCoordinate
	 * @return boolean
	 */
	public function dataValidationExists($pCoordinate = 'A1')
	{
		return isset($this->_dataValidationCollection[$pCoordinate]);
	}

	/**
	 * Get collection of data validations
	 *
	 * @return PHPExcel_Cell_DataValidation[]
	 */
	public function getDataValidationCollection()
	{
		return $this->_dataValidationCollection;
	}

	/**
	 * Get tab color
	 *
	 * @return PHPExcel_Style_Color
	 */
	public function getTabColor()
	{
		if (is_null($this->_tabColor))
			$this->_tabColor = new PHPExcel_Style_Color();

		return $this->_tabColor;
	}

	/**
	 * Reset tab color
	 *
	 * @return PHPExcel_Worksheet
	 */
	public function resetTabColor()
	{
		$this->_tabColor = null;
		unset($this->_tabColor);

		return $this;
	}

	/**
	 * Tab color set?
	 *
	 * @return boolean
	 */
	public function isTabColorSet()
	{
		return !is_null($this->_tabColor);
	}

	/**
	 * Copy worksheet (!= clone!)
	 *
	 * @return PHPExcel_Worksheet
	 */
	public function copy() {
		$copied = clone $this;

		return $copied;
	}

	/**
	 * Implement PHP __clone to create a deep clone, not just a shallow copy.
	 */
	public function __clone() {
		foreach ($this as $key => $val) {
			if ($key == '_parent') {
				continue;
			}

			if (is_object($val) || (is_array($val))) {
				$this->{$key} = unserialize(serialize($val));
			}
		}
	}
}
