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


/** Require FPDF library */
$k_path_url = dirname(__FILE__) . '/PDF';
require_once PHPEXCEL_ROOT . 'PHPExcel/Shared/PDF/tcpdf.php';

/**
 * PHPExcel_Writer_PDF
 *
 * @category   PHPExcel
 * @package    PHPExcel_Writer
 * @copyright  Copyright (c) 2006 - 2010 PHPExcel (http://www.codeplex.com/PHPExcel)
 */
class PHPExcel_Writer_PDF extends PHPExcel_Writer_HTML implements PHPExcel_Writer_IWriter {
	/**
	 * Temporary storage directory
	 *
	 * @var string
	 */
	private $_tempDir = '';

	/**
	 * Create a new PHPExcel_Writer_PDF
	 *
	 * @param 	PHPExcel	$phpExcel	PHPExcel object
	 */
	public function __construct(PHPExcel $phpExcel) {
		parent::__construct($phpExcel);
		$this->setUseInlineCss(true);
		$this->_tempDir = PHPExcel_Shared_File::sys_get_temp_dir();
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

		// Open file
		$fileHandle = fopen($pFilename, 'w');
		if ($fileHandle === false) {
			throw new Exception("Could not open file $pFilename for writing.");
		}

		// Set PDF
		$this->_isPdf = true;

		// Build CSS
		$this->buildCSS(true);

		// Generate HTML
		$html = '';
		//$html .= $this->generateHTMLHeader(false);
		$html .= $this->generateSheetData();
		//$html .= $this->generateHTMLFooter();

    	// Default PDF paper size
    	$paperSize = 'A4';
    	$orientation = 'P';

    	// Check for overrides
		if (is_null($this->getSheetIndex())) {
			$orientation = $this->_phpExcel->getSheet(0)->getPageSetup()->getOrientation() == 'landscape' ? 'L' : 'P';
		} else {
			$orientation = $this->_phpExcel->getSheet($this->getSheetIndex())->getPageSetup()->getOrientation() == 'landscape' ? 'L' : 'P';
		}

		// Create PDF
		$pdf = new TCPDF($orientation, 'pt', $paperSize);
		$pdf->setPrintHeader(false);
		$pdf->setPrintFooter(false);
		$pdf->AddPage();

		// Set the appropriate font
		$pdf->SetFont('freesans');
		//$pdf->SetFont('arialunicid0-chinese-simplified');
		//$pdf->SetFont('arialunicid0-chinese-traditional');
		//$pdf->SetFont('arialunicid0-korean');
		//$pdf->SetFont('arialunicid0-japanese');
		$pdf->writeHTML($html);

		// Document info
		$pdf->SetTitle($this->_phpExcel->getProperties()->getTitle());
		$pdf->SetAuthor($this->_phpExcel->getProperties()->getCreator());
		$pdf->SetSubject($this->_phpExcel->getProperties()->getSubject());
		$pdf->SetKeywords($this->_phpExcel->getProperties()->getKeywords());
		$pdf->SetCreator($this->_phpExcel->getProperties()->getCreator());

		// Write to file
		fwrite($fileHandle, $pdf->output($pFilename, 'S'));

		// Close file
		fclose($fileHandle);

		PHPExcel_Calculation::setArrayReturnType($saveArrayReturnType);
	}

	/**
	 * Get temporary storage directory
	 *
	 * @return string
	 */
	public function getTempDir() {
		return $this->_tempDir;
	}

	/**
	 * Set temporary storage directory
	 *
	 * @param 	string	$pValue		Temporary storage directory
	 * @throws 	Exception	Exception when directory does not exist
	 * @return PHPExcel_Writer_PDF
	 */
	public function setTempDir($pValue = '') {
		if (is_dir($pValue)) {
			$this->_tempDir = $pValue;
		} else {
			throw new Exception("Directory does not exist: $pValue");
		}
		return $this;
	}
}
