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
 * @package    PHPExcel_Writer
 * @copyright  Copyright (c) 2006 - 2010 PHPExcel (http://www.codeplex.com/PHPExcel)
 * @license    http://www.gnu.org/licenses/old-licenses/lgpl-2.1.txt	LGPL
 * @version    ##VERSION##, ##DATE##
 */


/**
 * PHPExcel_Writer_HTML
 *
 * @category   PHPExcel
 * @package    PHPExcel_Writer
 * @copyright  Copyright (c) 2006 - 2010 PHPExcel (http://www.codeplex.com/PHPExcel)
 */
class PHPExcel_Writer_HTML implements PHPExcel_Writer_IWriter {
	/**
	 * PHPExcel object
	 *
	 * @var PHPExcel
	 */
	protected $_phpExcel;

	/**
	 * Sheet index to write
	 *
	 * @var int
	 */
	private $_sheetIndex;

	/**
	 * Pre-calculate formulas
	 *
	 * @var boolean
	 */
	private $_preCalculateFormulas = true;

	/**
	 * Images root
	 *
	 * @var string
	 */
	private $_imagesRoot = '.';

	/**
	 * Use inline CSS?
	 *
	 * @var boolean
	 */
	private $_useInlineCss = false;

	/**
	 * Array of CSS styles
	 *
	 * @var array
	 */
	private $_cssStyles = null;

	/**
	 * Array of column widths in points
	 *
	 * @var array
	 */
	private $_columnWidths = null;

	/**
	 * Default font
	 *
	 * @var PHPExcel_Style_Font
	 */
	private $_defaultFont;

	/**
	 * Flag whether spans have been calculated
	 *
	 * @var boolean
	 */
	private $_spansAreCalculated;

	/**
	 * Excel cells that should not be written as HTML cells
	 *
	 * @var array
	 */
	private $_isSpannedCell;

	/**
	 * Excel cells that are upper-left corner in a cell merge
	 *
	 * @var array
	 */
	private $_isBaseCell;

	/**
	 * Excel rows that should not be written as HTML rows
	 *
	 * @var array
	 */
	private $_isSpannedRow;

	/**
	 * Is the current writer creating PDF?
	 *
	 * @var boolean
	 */
	protected $_isPdf = false;

	/**
	 * Create a new PHPExcel_Writer_HTML
	 *
	 * @param 	PHPExcel	$phpExcel	PHPExcel object
	 */
	public function __construct(PHPExcel $phpExcel) {
		$this->_phpExcel = $phpExcel;
		$this->_defaultFont = $this->_phpExcel->getDefaultStyle()->getFont();
		$this->_sheetIndex = 0;
		$this->_imagesRoot = '.';

		$this->_spansAreCalculated = false;
		$this->_isSpannedCell = array();
		$this->_isBaseCell    = array();
		$this->_isSpannedRow  = array();
	}

	/**
	 * Save PHPExcel to file
	 *
	 * @param 	string 		$pFileName
	 * @throws 	Exception
	 */
	public function save($pFilename = null) {
		// garbage collect
		$this->_phpExcel->garbageCollect();

		$saveArrayReturnType = PHPExcel_Calculation::getArrayReturnType();
		PHPExcel_Calculation::setArrayReturnType(PHPExcel_Calculation::RETURN_ARRAY_AS_VALUE);

		// Build CSS
		$this->buildCSS(!$this->_useInlineCss);

		// Open file
		$fileHandle = fopen($pFilename, 'w');
		if ($fileHandle === false) {
			throw new Exception("Could not open file $pFilename for writing.");
		}

		// Write headers
		fwrite($fileHandle, $this->generateHTMLHeader(!$this->_useInlineCss));

		// Write data
		fwrite($fileHandle, $this->generateSheetData());

		// Write footer
		fwrite($fileHandle, $this->generateHTMLFooter());

		// Close file
		fclose($fileHandle);

		PHPExcel_Calculation::setArrayReturnType($saveArrayReturnType);
	}

	/**
	 * Map VAlign
	 */
	private function _mapVAlign($vAlign) {
		switch ($vAlign) {
			case PHPExcel_Style_Alignment::VERTICAL_BOTTOM: return 'bottom';
			case PHPExcel_Style_Alignment::VERTICAL_TOP: return 'top';
			case PHPExcel_Style_Alignment::VERTICAL_CENTER:
			case PHPExcel_Style_Alignment::VERTICAL_JUSTIFY: return 'middle';
			default: return 'baseline';
		}
	}

	/**
	 * Map HAlign
	 *
	 * @return string|false
	 */
	private function _mapHAlign($hAlign) {
		switch ($hAlign) {
			case PHPExcel_Style_Alignment::HORIZONTAL_GENERAL: return false;
			case PHPExcel_Style_Alignment::HORIZONTAL_LEFT: return 'left';
			case PHPExcel_Style_Alignment::HORIZONTAL_RIGHT: return 'right';
			case PHPExcel_Style_Alignment::HORIZONTAL_CENTER: return 'center';
			case PHPExcel_Style_Alignment::HORIZONTAL_JUSTIFY: return 'justify';
			default: return false;
		}
	}

	/**
	 * Map border style
	 */
	private function _mapBorderStyle($borderStyle) {
		switch ($borderStyle) {
			case PHPExcel_Style_Border::BORDER_NONE: return '0px';
			case PHPExcel_Style_Border::BORDER_DASHED: return '1px dashed';
			case PHPExcel_Style_Border::BORDER_DOTTED: return '1px dotted';
			case PHPExcel_Style_Border::BORDER_DOUBLE: return '3px double';
			case PHPExcel_Style_Border::BORDER_THICK: return '2px solid';
			default: return '1px solid'; // map others to thin
		}
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
	 * @return PHPExcel_Writer_HTML
	 */
	public function setSheetIndex($pValue = 0) {
		$this->_sheetIndex = $pValue;
		return $this;
	}

	/**
	 * Write all sheets (resets sheetIndex to NULL)
	 */
	public function writeAllSheets() {
		$this->_sheetIndex = null;
	}

	/**
	 * Generate HTML header
	 *
	 * @param	boolean		$pIncludeStyles		Include styles?
	 * @return	string
	 * @throws Exception
	 */
	public function generateHTMLHeader($pIncludeStyles = false) {
		// PHPExcel object known?
		if (is_null($this->_phpExcel)) {
			throw new Exception('Internal PHPExcel object not set to an instance of an object.');
		}

		// Construct HTML
		$html = '';
		$html .= '<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">' . "\r\n";
		$html .= '<!-- Generated by PHPExcel - http://www.phpexcel.net -->' . "\r\n";
		$html .= '<html>' . "\r\n";
		$html .= '  <head>' . "\r\n";
		$html .= '    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">' . "\r\n";
		$html .= '    <title>' . htmlspecialchars($this->_phpExcel->getProperties()->getTitle()) . '</title>' . "\r\n";
		if ($pIncludeStyles) {
			$html .= $this->generateStyles(true);
		}
		$html .= '  </head>' . "\r\n";
		$html .= '' . "\r\n";
		$html .= '  <body>' . "\r\n";

		// Return
		return $html;
	}

	/**
	 * Generate sheet data
	 *
	 * @return	string
	 * @throws Exception
	 */
	public function generateSheetData() {
		// PHPExcel object known?
		if (is_null($this->_phpExcel)) {
			throw new Exception('Internal PHPExcel object not set to an instance of an object.');
		}

		// Ensure that Spans have been calculated?
		if (!$this->_spansAreCalculated) {
			$this->_calculateSpans();
		}

		// Fetch sheets
		$sheets = array();
		if (is_null($this->_sheetIndex)) {
			$sheets = $this->_phpExcel->getAllSheets();
		} else {
			$sheets[] = $this->_phpExcel->getSheet($this->_sheetIndex);
		}

		// Construct HTML
		$html = '';

		// Loop all sheets
		$sheetId = 0;
		foreach ($sheets as $sheet) {
			// Write table header
			$html .= $this->_generateTableHeader($sheet);

	    	// Get worksheet dimension
	    	$dimension = explode(':', $sheet->calculateWorksheetDimension());
	    	$dimension[0] = PHPExcel_Cell::coordinateFromString($dimension[0]);
	    	$dimension[0][0] = PHPExcel_Cell::columnIndexFromString($dimension[0][0]) - 1;
	    	$dimension[1] = PHPExcel_Cell::coordinateFromString($dimension[1]);
	    	$dimension[1][0] = PHPExcel_Cell::columnIndexFromString($dimension[1][0]) - 1;

	    	// Loop through cells
	    	$rowData = null;
	    	for ($row = $dimension[0][1]; $row <= $dimension[1][1]; ++$row) {
				// Start a new row
				$rowData = array();

				// Loop through columns
	    		for ($column = $dimension[0][0]; $column <= $dimension[1][0]; ++$column) {
	    			// Cell exists?
	    			if ($sheet->cellExistsByColumnAndRow($column, $row)) {
	    				$rowData[$column] = $sheet->getCellByColumnAndRow($column, $row);
	    			} else {
	    				$rowData[$column] = '';
	    			}
	    		}

	    		// Write row if there are HTML table cells in it
				if ( !isset($this->_isSpannedRow[$sheet->getParent()->getIndex($sheet)][$row]) ) {
					$html .= $this->_generateRow($sheet, $rowData, $row - 1);
				}
	    	}

			// Write table footer
			$html .= $this->_generateTableFooter();

			// Writing PDF?
			if ($this->_isPdf)
			{
				if (is_null($this->_sheetIndex) && $sheetId + 1 < $this->_phpExcel->getSheetCount()) {
					$html .= '<tcpdf method="AddPage" />';
				}
			}

			// Next sheet
			++$sheetId;
		}

		// Return
		return $html;
	}

	/**
	 * Generate image tag in cell
	 *
	 * @param	PHPExcel_Worksheet 	$pSheet			PHPExcel_Worksheet
	 * @param	string				$coordinates	Cell coordinates
	 * @return	string
	 * @throws	Exception
	 */
	private function _writeImageTagInCell(PHPExcel_Worksheet $pSheet, $coordinates) {
		// Construct HTML
		$html = '';

		// Write images
		foreach ($pSheet->getDrawingCollection() as $drawing) {
			if ($drawing instanceof PHPExcel_Worksheet_Drawing) {
				if ($drawing->getCoordinates() == $coordinates) {
					$filename = $drawing->getPath();

					// Strip off eventual '.'
					if (substr($filename, 0, 1) == '.') {
						$filename = substr($filename, 1);
					}

					// Prepend images root
					$filename = $this->getImagesRoot() . $filename;

					// Strip off eventual '.'
					if (substr($filename, 0, 1) == '.' && substr($filename, 0, 2) != './') {
						$filename = substr($filename, 1);
					}

					// Convert UTF8 data to PCDATA
					$filename = htmlspecialchars($filename);

					$html .= "\r\n";
					$html .= '        <img style="position: relative; left: ' . $drawing->getOffsetX() . 'px; top: ' . $drawing->getOffsetY() . 'px; width: ' . $drawing->getWidth() . 'px; height: ' . $drawing->getHeight() . 'px;" src="' . $filename . '" border="0" width="' . $drawing->getWidth() . '" height="' . $drawing->getHeight() . '" />' . "\r\n";
				}
			}
		}

		// Return
		return $html;
	}

	/**
	 * Generate CSS styles
	 *
	 * @param	boolean	$generateSurroundingHTML	Generate surrounding HTML tags? (<style> and </style>)
	 * @return	string
	 * @throws	Exception
	 */
	public function generateStyles($generateSurroundingHTML = true) {
		// PHPExcel object known?
		if (is_null($this->_phpExcel)) {
			throw new Exception('Internal PHPExcel object not set to an instance of an object.');
		}

		// Build CSS
		$css = $this->buildCSS($generateSurroundingHTML);

		// Construct HTML
		$html = '';

		// Start styles
		if ($generateSurroundingHTML) {
			$html .= '    <style type="text/css">' . "\r\n";
			$html .= '      html { ' . $this->_assembleCSS($css['html']) . ' }' . "\r\n";
		}

		// Write all other styles
		foreach ($css as $styleName => $styleDefinition) {
			if ($styleName != 'html') {
				$html .= '      ' . $styleName . ' { ' . $this->_assembleCSS($styleDefinition) . ' }' . "\r\n";
			}
		}

		// End styles
		if ($generateSurroundingHTML) {
			$html .= '    </style>' . "\r\n";
		}

		// Return
		return $html;
	}

	/**
	 * Build CSS styles
	 *
	 * @param	boolean	$generateSurroundingHTML	Generate surrounding HTML style? (html { })
	 * @return	array
	 * @throws	Exception
	 */
	public function buildCSS($generateSurroundingHTML = true) {
		// PHPExcel object known?
		if (is_null($this->_phpExcel)) {
			throw new Exception('Internal PHPExcel object not set to an instance of an object.');
		}

		// Cached?
		if (!is_null($this->_cssStyles)) {
			return $this->_cssStyles;
		}

		// Ensure that spans have been calculated
		if (!$this->_spansAreCalculated) {
			$this->_calculateSpans();
		}

		// Construct CSS
		$css = array();

		// Start styles
		if ($generateSurroundingHTML) {
			// html { }
			$css['html']['font-family']      = 'Calibri, Arial, Helvetica, sans-serif';
			$css['html']['font-size']        = '11pt';
			$css['html']['background-color'] = 'white';
		}


		// table { }
		$css['table']['border-collapse']  = 'collapse';
		$css['table']['page-break-after'] = 'always';

		// .gridlines td { }
		$css['.gridlines td']['border'] = '1px dotted black';

		// .b {}
		$css['.b']['text-align'] = 'center'; // BOOL

		// .e {}
		$css['.e']['text-align'] = 'center'; // ERROR

		// .f {}
		$css['.f']['text-align'] = 'right'; // FORMULA

		// .inlineStr {}
		$css['.inlineStr']['text-align'] = 'left'; // INLINE

		// .n {}
		$css['.n']['text-align'] = 'right'; // NUMERIC

		// .s {}
		$css['.s']['text-align'] = 'left'; // STRING

		// Calculate cell style hashes
		foreach ($this->_phpExcel->getCellXfCollection() as $index => $style) {
			$css['td.style' . $index] = $this->_createCSSStyle( $style );
		}

		// Fetch sheets
		$sheets = array();
		if (is_null($this->_sheetIndex)) {
			$sheets = $this->_phpExcel->getAllSheets();
		} else {
			$sheets[] = $this->_phpExcel->getSheet($this->_sheetIndex);
		}

		// Build styles per sheet
		foreach ($sheets as $sheet) {
			// Calculate hash code
			$sheetIndex = $sheet->getParent()->getIndex($sheet);

			// Build styles
			// Calculate column widths
			$sheet->calculateColumnWidths();

			// col elements, initialize
			$highestColumnIndex = PHPExcel_Cell::columnIndexFromString($sheet->getHighestColumn()) - 1;
			for ($column = 0; $column <= $highestColumnIndex; ++$column) {
				$this->_columnWidths[$sheetIndex][$column] = 42; // approximation
				$css['table.sheet' . $sheetIndex . ' col.col' . $column]['width'] = '42pt';
			}

			// col elements, loop through columnDimensions and set width
			foreach ($sheet->getColumnDimensions() as $columnDimension) {
				if (($width = PHPExcel_Shared_Drawing::cellDimensionToPixels($columnDimension->getWidth(), $this->_defaultFont)) >= 0) {
					$width = PHPExcel_Shared_Drawing::pixelsToPoints($width);
					$column = PHPExcel_Cell::columnIndexFromString($columnDimension->getColumnIndex()) - 1;
					$this->_columnWidths[$sheetIndex][$column] = $width;
					$css['table.sheet' . $sheetIndex . ' col.col' . $column]['width'] = $width . 'pt';

					if ($columnDimension->getVisible() === false) {
						$css['table.sheet' . $sheetIndex . ' col.col' . $column]['visibility'] = 'collapse';
						$css['table.sheet' . $sheetIndex . ' col.col' . $column]['*display'] = 'none'; // target IE6+7
					}
				}
			}

			// Default row height
			$rowDimension = $sheet->getDefaultRowDimension();

			// table.sheetN tr { }
			$css['table.sheet' . $sheetIndex . ' tr'] = array();

			if ($rowDimension->getRowHeight() == -1) {
				$pt_height = PHPExcel_Shared_Font::getDefaultRowHeightByFont($this->_phpExcel->getDefaultStyle()->getFont());
			} else {
				$pt_height = $rowDimension->getRowHeight();
			}
			$css['table.sheet' . $sheetIndex . ' tr']['height'] = $pt_height . 'pt';
			if ($rowDimension->getVisible() === false) {
				$css['table.sheet' . $sheetIndex . ' tr']['display']    = 'none';
				$css['table.sheet' . $sheetIndex . ' tr']['visibility'] = 'hidden';
			}

			// Calculate row heights
			foreach ($sheet->getRowDimensions() as $rowDimension) {
				$row = $rowDimension->getRowIndex() - 1;

				// table.sheetN tr.rowYYYYYY { }
				$css['table.sheet' . $sheetIndex . ' tr.row' . $row] = array();

				if ($rowDimension->getRowHeight() == -1) {
					$pt_height = PHPExcel_Shared_Font::getDefaultRowHeightByFont($this->_phpExcel->getDefaultStyle()->getFont());
				} else {
					$pt_height = $rowDimension->getRowHeight();
				}
				$css['table.sheet' . $sheetIndex . ' tr.row' . $row]['height'] = $pt_height . 'pt';
				if ($rowDimension->getVisible() === false) {
					$css['table.sheet' . $sheetIndex . ' tr.row' . $row]['display'] = 'none';
					$css['table.sheet' . $sheetIndex . ' tr.row' . $row]['visibility'] = 'hidden';
				}
			}
		}

		// Cache
		if (is_null($this->_cssStyles)) {
			$this->_cssStyles = $css;
		}

		// Return
		return $css;
	}

	/**
	 * Create CSS style
	 *
	 * @param	PHPExcel_Style 		$pStyle			PHPExcel_Style
	 * @return	array
	 */
	private function _createCSSStyle(PHPExcel_Style $pStyle) {
		// Construct CSS
		$css = '';

		// Create CSS
		$css = array_merge(
			$this->_createCSSStyleAlignment($pStyle->getAlignment())
			, $this->_createCSSStyleBorders($pStyle->getBorders())
			, $this->_createCSSStyleFont($pStyle->getFont())
			, $this->_createCSSStyleFill($pStyle->getFill())
		);

		// Return
		return $css;
	}

	/**
	 * Create CSS style (PHPExcel_Style_Alignment)
	 *
	 * @param	PHPExcel_Style_Alignment 		$pStyle			PHPExcel_Style_Alignment
	 * @return	array
	 */
	private function _createCSSStyleAlignment(PHPExcel_Style_Alignment $pStyle) {
		// Construct CSS
		$css = array();

		// Create CSS
		$css['vertical-align'] = $this->_mapVAlign($pStyle->getVertical());
		if ($textAlign = $this->_mapHAlign($pStyle->getHorizontal())) {
			$css['text-align'] = $textAlign;
		}

		// Return
		return $css;
	}

	/**
	 * Create CSS style (PHPExcel_Style_Font)
	 *
	 * @param	PHPExcel_Style_Font 		$pStyle			PHPExcel_Style_Font
	 * @return	array
	 */
	private function _createCSSStyleFont(PHPExcel_Style_Font $pStyle) {
		// Construct CSS
		$css = array();

		// Create CSS
		if ($pStyle->getBold()) {
			$css['font-weight'] = 'bold';
		}
		if ($pStyle->getUnderline() != PHPExcel_Style_Font::UNDERLINE_NONE && $pStyle->getStrikethrough()) {
			$css['text-decoration'] = 'underline line-through';
		} else if ($pStyle->getUnderline() != PHPExcel_Style_Font::UNDERLINE_NONE) {
			$css['text-decoration'] = 'underline';
		} else if ($pStyle->getStrikethrough()) {
			$css['text-decoration'] = 'line-through';
		}
		if ($pStyle->getItalic()) {
			$css['font-style'] = 'italic';
		}

		$css['color']		= '#' . $pStyle->getColor()->getRGB();
		$css['font-family']	= '\'' . $pStyle->getName() . '\'';
		$css['font-size']	= $pStyle->getSize() . 'pt';

		// Return
		return $css;
	}

	/**
	 * Create CSS style (PHPExcel_Style_Borders)
	 *
	 * @param	PHPExcel_Style_Borders 		$pStyle			PHPExcel_Style_Borders
	 * @return	array
	 */
	private function _createCSSStyleBorders(PHPExcel_Style_Borders $pStyle) {
		// Construct CSS
		$css = array();

		// Create CSS
		$css['border-bottom']	= $this->_createCSSStyleBorder($pStyle->getBottom());
		$css['border-top']		= $this->_createCSSStyleBorder($pStyle->getTop());
		$css['border-left']		= $this->_createCSSStyleBorder($pStyle->getLeft());
		$css['border-right']	= $this->_createCSSStyleBorder($pStyle->getRight());

		// Return
		return $css;
	}

	/**
	 * Create CSS style (PHPExcel_Style_Border)
	 *
	 * @param	PHPExcel_Style_Border		$pStyle			PHPExcel_Style_Border
	 * @return	string
	 */
	private function _createCSSStyleBorder(PHPExcel_Style_Border $pStyle) {
		// Construct HTML
		$css = '';

		// Create CSS
		$css .= $this->_mapBorderStyle($pStyle->getBorderStyle()) . ' #' . $pStyle->getColor()->getRGB();

		// Return
		return $css;
	}

	/**
	 * Create CSS style (PHPExcel_Style_Fill)
	 *
	 * @param	PHPExcel_Style_Fill		$pStyle			PHPExcel_Style_Fill
	 * @return	array
	 */
	private function _createCSSStyleFill(PHPExcel_Style_Fill $pStyle) {
		// Construct HTML
		$css = array();

		// Create CSS
		$value = $pStyle->getFillType() == PHPExcel_Style_Fill::FILL_NONE ?
			'white' : '#' . $pStyle->getStartColor()->getRGB();
		$css['background-color'] = $value;

		// Return
		return $css;
	}

	/**
	 * Generate HTML footer
	 */
	public function generateHTMLFooter() {
		// Construct HTML
		$html = '';
		$html .= '  </body>' . "\r\n";
		$html .= '</html>' . "\r\n";

		// Return
		return $html;
	}

	/**
	 * Generate table header
	 *
	 * @param 	PHPExcel_Worksheet	$pSheet		The worksheet for the table we are writing
	 * @return	string
	 * @throws	Exception
	 */
	private function _generateTableHeader($pSheet) {
		$sheetIndex = $pSheet->getParent()->getIndex($pSheet);

		// Construct HTML
		$html = '';

		if (!$this->_useInlineCss) {
			$gridlines = $pSheet->getShowGridLines() ? ' gridlines' : '';
			$html .= '    <table border="0" cellpadding="0" cellspacing="0" class="sheet' . $sheetIndex . $gridlines . '">' . "\r\n";
		} else {
			$style = isset($this->_cssStyles['table']) ?
				$this->_assembleCSS($this->_cssStyles['table']) : '';

			if ($this->_isPdf && $pSheet->getShowGridLines()) {
				$html .= '    <table border="1" cellpadding="0" cellspacing="0" style="' . $style . '">' . "\r\n";
			} else {
				$html .= '    <table border="0" cellpadding="0" cellspacing="0" style="' . $style . '">' . "\r\n";
			}
		}

		// Write <col> elements
		$highestColumnIndex = PHPExcel_Cell::columnIndexFromString($pSheet->getHighestColumn()) - 1;
		for ($i = 0; $i <= $highestColumnIndex; ++$i) {
			if (!$this->_useInlineCss) {
				$html .= '        <col class="col' . $i . '">' . "\r\n";
			} else {
				$style = isset($this->_cssStyles['table.sheet' . $sheetIndex . ' col.col' . $i]) ?
					$this->_assembleCSS($this->_cssStyles['table.sheet' . $sheetIndex . ' col.col' . $i]) : '';
				$html .= '        <col style="' . $style . '">' . "\r\n";
			}
		}

		// Return
		return $html;
	}

	/**
	 * Generate table footer
	 *
	 * @throws	Exception
	 */
	private function _generateTableFooter() {
		// Construct HTML
		$html = '';
		$html .= '    </table>' . "\r\n";

		// Return
		return $html;
	}

	/**
	 * Generate row
	 *
	 * @param	PHPExcel_Worksheet 	$pSheet			PHPExcel_Worksheet
	 * @param	array				$pValues		Array containing cells in a row
	 * @param	int					$pRow			Row number (0-based)
	 * @return	string
	 * @throws	Exception
	 */
	private function _generateRow(PHPExcel_Worksheet $pSheet, $pValues = null, $pRow = 0) {
		if (is_array($pValues)) {
			// Construct HTML
			$html = '';

			// Sheet index
			$sheetIndex = $pSheet->getParent()->getIndex($pSheet);

			// TCPDF and breaks
			if ($this->_isPdf && count($pSheet->getBreaks()) > 0) {
				$breaks = $pSheet->getBreaks();

				// check if a break is needed before this row
				if (isset($breaks['A' . $pRow])) {
					// close table: </table>
					$html .= $this->_generateTableFooter();

					// insert page break
					$html .= '<tcpdf method="AddPage" />';

					// open table again: <table> + <col> etc.
					$html .= $this->_generateTableHeader($pSheet);
				}
			}

			// Write row start
			if (!$this->_useInlineCss) {
				$html .= '        <tr class="row' . $pRow . '">' . "\r\n";
			} else {
				$style = isset($this->_cssStyles['table.sheet' . $sheetIndex . ' tr.row' . $pRow])
					? $this->_assembleCSS($this->_cssStyles['table.sheet' . $sheetIndex . ' tr.row' . $pRow]) : '';

				$html .= '        <tr style="' . $style . '">' . "\r\n";
			}

			// Write cells
			$colNum = 0;
			foreach ($pValues as $cell) {
				if (!$this->_useInlineCss) {
					$cssClass = '';
					$cssClass = 'column' . $colNum;
				} else {
					$cssClass = array();
					if (isset($this->_cssStyles['table.sheet' . $sheetIndex . ' td.column' . $colNum])) {
						$this->_cssStyles['table.sheet' . $sheetIndex . ' td.column' . $colNum];
					}
				}
				$colSpan = 1;
				$rowSpan = 1;
				$writeCell = true;	// Write cell

				// initialize
				$cellData = '';

				// PHPExcel_Cell
				if ($cell instanceof PHPExcel_Cell) {

					// Value
					if ($cell->getValue() instanceof PHPExcel_RichText) {
						// Loop through rich text elements
						$elements = $cell->getValue()->getRichTextElements();
						foreach ($elements as $element) {
							// Rich text start?
							if ($element instanceof PHPExcel_RichText_Run) {
								$cellData .= '<span style="' . $this->_assembleCSS($this->_createCSSStyleFont($element->getFont())) . '">';

								if ($element->getFont()->getSuperScript()) {
									$cellData .= '<sup>';
								} else if ($element->getFont()->getSubScript()) {
									$cellData .= '<sub>';
								}
							}

							// Convert UTF8 data to PCDATA
							$cellText = $element->getText();
							$cellData .= htmlspecialchars($cellText);

							if ($element instanceof PHPExcel_RichText_Run) {
								if ($element->getFont()->getSuperScript()) {
									$cellData .= '</sup>';
								} else if ($element->getFont()->getSubScript()) {
									$cellData .= '</sub>';
								}

								$cellData .= '</span>';
							}
						}
					} else {
						if ($this->_preCalculateFormulas) {
							$cellData = PHPExcel_Style_NumberFormat::toFormattedString(
								$cell->getCalculatedValue(),
								$pSheet->getParent()->getCellXfByIndex( $cell->getXfIndex() )->getNumberFormat()->getFormatCode(),
								array($this, 'formatColor')
							);
						} else {
							$cellData = PHPExcel_Style_NumberFormat::ToFormattedString(
								$cell->getValue(),
								$pSheet->getParent()->getCellXfByIndex( $cell->getXfIndex() )->getNumberFormat()->getFormatCode(),
								array($this, 'formatColor')
							);
						}
					}

					// replace leading spaces on each line with &nbsp;
					$cellData = $this->_convertNbsp($cellData);

					// convert newline "\n" to '<br>'
					$cellData = str_replace("\n", '<br/>', $cellData);

					// Extend CSS class?
					if (!$this->_useInlineCss) {
						$cssClass .= ' style' . $cell->getXfIndex();
						$cssClass .= ' ' . $cell->getDataType();
					} else {
						if (isset($this->_cssStyles['td.style' . $cell->getXfIndex()])) {
							$cssClass = array_merge($cssClass, $this->_cssStyles['td.style' . $cell->getXfIndex()]);
						}

						// General horizontal alignment: Actual horizontal alignment depends on dataType
						$sharedStyle = $pSheet->getParent()->getCellXfByIndex( $cell->getXfIndex() );
						if ($sharedStyle->getAlignment()->getHorizontal() == PHPExcel_Style_Alignment::HORIZONTAL_GENERAL
							&& isset($this->_cssStyles['.' . $cell->getDataType()]['text-align']))
						{
							$cssClass['text-align'] = $this->_cssStyles['.' . $cell->getDataType()]['text-align'];
						}
					}
				} else {
					$cell = new PHPExcel_Cell(
						PHPExcel_Cell::stringFromColumnIndex($colNum),
						($pRow + 1),
						'',
						PHPExcel_Cell_DataType::TYPE_NULL,
						$pSheet
					);
				}

				// Hyperlink?
				if ($cell->hasHyperlink() && !$cell->getHyperlink()->isInternal()) {
					$cellData = '<a href="' . htmlspecialchars($cell->getHyperlink()->getUrl()) . '" title="' . htmlspecialchars($cell->getHyperlink()->getTooltip()) . '">' . $cellData . '</a>';
				}

				// Should the cell be written or is it swallowed by a rowspan or colspan?
				$writeCell = ! ( isset($this->_isSpannedCell[$pSheet->getParent()->getIndex($pSheet)][$pRow + 1][$colNum])
							&& $this->_isSpannedCell[$pSheet->getParent()->getIndex($pSheet)][$pRow + 1][$colNum] );

				// Colspan and Rowspan
				$colspan = 1;
				$rowspan = 1;
				if (isset($this->_isBaseCell[$pSheet->getParent()->getIndex($pSheet)][$pRow + 1][$colNum])) {
					$spans = $this->_isBaseCell[$pSheet->getParent()->getIndex($pSheet)][$pRow + 1][$colNum];
					$rowSpan = $spans['rowspan'];
					$colSpan = $spans['colspan'];
				}

				// Write
				if ($writeCell) {
					// Column start
					$html .= '          <td';
						if (!$this->_useInlineCss) {
							$html .= ' class="' . $cssClass . '"';
						} else {
							//** Necessary redundant code for the sake of PHPExcel_Writer_PDF **
							// We must explicitly write the width of the <td> element because TCPDF
							// does not recognize e.g. <col style="width:42pt">
							$width = 0;
							$columnIndex = PHPExcel_Cell::columnIndexFromString($cell->getColumn()) - 1;
							for ($i = $columnIndex; $i < $columnIndex + $colSpan; ++$i) {
								if (isset($this->_columnWidths[$sheetIndex][$i])) {
									$width += $this->_columnWidths[$sheetIndex][$i];
								}
							}
							$cssClass['width'] = $width . 'pt';

							// We must also explicitly write the height of the <td> element because TCPDF
							// does not recognize e.g. <tr style="height:50pt">
							if (isset($this->_cssStyles['table.sheet' . $sheetIndex . ' tr.row' . $pRow]['height'])) {
								$height = $this->_cssStyles['table.sheet' . $sheetIndex . ' tr.row' . $pRow]['height'];
								$cssClass['height'] = $height;
							}
							//** end of redundant code **

							$html .= ' style="' . $this->_assembleCSS($cssClass) . '"';
						}
						if ($colSpan > 1) {
							$html .= ' colspan="' . $colSpan . '"';
						}
						if ($rowSpan > 1) {
							$html .= ' rowspan="' . $rowSpan . '"';
						}
					$html .= '>';

					// Image?
					$html .= $this->_writeImageTagInCell($pSheet, $cell->getCoordinate());

					// Cell data
					$html .= $cellData;

					// Column end
					$html .= '</td>' . "\r\n";
				}

				// Next column
				++$colNum;
			}

			// Write row end
			$html .= '        </tr>' . "\r\n";

			// Return
			return $html;
		} else {
			throw new Exception("Invalid parameters passed.");
		}
	}

	/**
	 * Takes array where of CSS properties / values and converts to CSS string
	 *
	 * @param array
	 * @return string
	 */
	private function _assembleCSS($pValue = array())
	{
		$pairs = array();
		foreach ($pValue as $property => $value) {
			$pairs[] = $property . ':' . $value;
		}
		$string = implode('; ', $pairs);

		return $string;
	}

    /**
     * Get Pre-Calculate Formulas
     *
     * @return boolean
     */
    public function getPreCalculateFormulas() {
    	return $this->_preCalculateFormulas;
    }

    /**
     * Set Pre-Calculate Formulas
     *
     * @param boolean $pValue	Pre-Calculate Formulas?
     * @return PHPExcel_Writer_HTML
     */
    public function setPreCalculateFormulas($pValue = true) {
    	$this->_preCalculateFormulas = $pValue;
    	return $this;
    }

    /**
     * Get images root
     *
     * @return string
     */
    public function getImagesRoot() {
    	return $this->_imagesRoot;
    }

    /**
     * Set images root
     *
     * @param string $pValue
     * @return PHPExcel_Writer_HTML
     */
    public function setImagesRoot($pValue = '.') {
    	$this->_imagesRoot = $pValue;
    	return $this;
    }

    /**
     * Get use inline CSS?
     *
     * @return boolean
     */
    public function getUseInlineCss() {
    	return $this->_useInlineCss;
    }

    /**
     * Set use inline CSS?
     *
     * @param boolean $pValue
     * @return PHPExcel_Writer_HTML
     */
    public function setUseInlineCss($pValue = false) {
    	$this->_useInlineCss = $pValue;
    	return $this;
    }

	/**
	 * Converts a string so that spaces occuring at beginning of each new line are replaced by &nbsp;
	 * Example: "  Hello\n to the world" is converted to "&nbsp;&nbsp;Hello\n&nbsp;to the world"
	 *
	 * @param string $pValue
	 * @return string
	 */
	private function _convertNbsp($pValue = '')
	{
		$explodes = explode("\n", $pValue);
		foreach ($explodes as $explode) {
			$matches = array();
			if (preg_match('/^( )+/', $explode, $matches)) {
				$explode = str_repeat('&nbsp;', strlen($matches[0])) . substr($explode, strlen($matches[0]));
			}
			$implodes[] = $explode;
		}

		$string = implode("\n", $implodes);
		return $string;
	}

	/**
	 * Add color to formatted string as inline style
	 *
	 * @param string $pValue Plain formatted value without color
	 * @param string $pFormat Format code
	 * @return string
	 */
	public function formatColor($pValue, $pFormat)
	{
		// Color information, e.g. [Red] is always at the beginning
		$color = null; // initialize
		$matches = array();

		$color_regex = '/^\\[[a-zA-Z]+\\]/';
		if (preg_match($color_regex, $pFormat, $matches)) {
			$color = str_replace('[', '', $matches[0]);
			$color = str_replace(']', '', $color);
			$color = strtolower($color);
		}

		// convert to PCDATA
		$value = htmlspecialchars($pValue);

		// color span tag
		if ($color !== null) {
			$value = '<span style="color:' . $color . '">' . $value . '</span>';
		}

		return $value;
	}

	/**
	 * Calculate information about HTML colspan and rowspan which is not always the same as Excel's
	 */
	private function _calculateSpans()
	{
		// Identify all cells that should be omitted in HTML due to cell merge.
		// In HTML only the upper-left cell should be written and it should have
		//   appropriate rowspan / colspan attribute
		$sheetIndexes = $this->_sheetIndex !== null ?
			array($this->_sheetIndex) : range(0, $this->_phpExcel->getSheetCount() - 1);

		foreach ($sheetIndexes as $sheetIndex) {
			$sheet = $this->_phpExcel->getSheet($sheetIndex);

			$candidateSpannedRow  = array();

			// loop through all Excel merged cells
			foreach ($sheet->getMergeCells() as $cells) {
				list($cells, ) = PHPExcel_Cell::splitRange($cells);
				$first = $cells[0];
				$last  = $cells[1];

				list($fc, $fr) = PHPExcel_Cell::coordinateFromString($first);
				$fc = PHPExcel_Cell::columnIndexFromString($fc) - 1;

				list($lc, $lr) = PHPExcel_Cell::coordinateFromString($last);
				$lc = PHPExcel_Cell::columnIndexFromString($lc) - 1;

				// loop through the individual cells in the individual merge
				for ($r = $fr; $r <= $lr; ++$r) {
					// also, flag this row as a HTML row that is candidate to be omitted
					$candidateSpannedRow[$r] = $r;

					for ($c = $fc; $c <= $lc; ++$c) {
						if ( !($c == $fc && $r == $fr) ) {
							// not the upper-left cell (should not be written in HTML)
							$this->_isSpannedCell[$sheetIndex][$r][$c] = array(
								'baseCell' => array($fr, $fc),
							);
						} else {
							// upper-left is the base cell that should hold the colspan/rowspan attribute
							$this->_isBaseCell[$sheetIndex][$r][$c] = array(
								'xlrowspan' => $lr - $fr + 1, // Excel rowspan
								'rowspan'   => $lr - $fr + 1, // HTML rowspan, value may change
								'xlcolspan' => $lc - $fc + 1, // Excel colspan
								'colspan'   => $lc - $fc + 1, // HTML colspan, value may change
							);
						}
					}
				}
			}

			// Identify which rows should be omitted in HTML. These are the rows where all the cells
			//   participate in a merge and the where base cells are somewhere above.
			$countColumns = PHPExcel_Cell::columnIndexFromString($sheet->getHighestColumn());
			foreach ($candidateSpannedRow as $rowIndex) {
				if (isset($this->_isSpannedCell[$sheetIndex][$rowIndex])) {
					if (count($this->_isSpannedCell[$sheetIndex][$rowIndex]) == $countColumns) {
						$this->_isSpannedRow[$sheetIndex][$rowIndex] = $rowIndex;
					};
				}
			}

			// For each of the omitted rows we found above, the affected rowspans should be subtracted by 1
			if ( isset($this->_isSpannedRow[$sheetIndex]) ) {
				foreach ($this->_isSpannedRow[$sheetIndex] as $rowIndex) {
					$adjustedBaseCells = array();
					for ($c = 0; $c < $countColumns; ++$c) {
						$baseCell = $this->_isSpannedCell[$sheetIndex][$rowIndex][$c]['baseCell'];

						if ( !in_array($baseCell, $adjustedBaseCells) ) {

							// subtract rowspan by 1
							--$this->_isBaseCell[$sheetIndex][ $baseCell[0] ][ $baseCell[1] ]['rowspan'];
							$adjustedBaseCells[] = $baseCell;
						}
					}
				}
			}

			// TODO: Same for columns
		}

		// We have calculated the spans
		$this->_spansAreCalculated = true;
	}

}
