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
 * @package    PHPExcel_Writer_Excel2007
 * @copyright  Copyright (c) 2006 - 2010 PHPExcel (http://www.codeplex.com/PHPExcel)
 * @license    http://www.gnu.org/licenses/old-licenses/lgpl-2.1.txt	LGPL
 * @version    ##VERSION##, ##DATE##
 */


/**
 * PHPExcel_Writer_Excel2007_StringTable
 *
 * @category   PHPExcel
 * @package    PHPExcel_Writer_Excel2007
 * @copyright  Copyright (c) 2006 - 2010 PHPExcel (http://www.codeplex.com/PHPExcel)
 */
class PHPExcel_Writer_Excel2007_StringTable extends PHPExcel_Writer_Excel2007_WriterPart
{
	/**
	 * Create worksheet stringtable
	 *
	 * @param 	PHPExcel_Worksheet 	$pSheet				Worksheet
	 * @param 	string[] 				$pExistingTable 	Existing table to eventually merge with
	 * @return 	string[] 				String table for worksheet
	 * @throws 	Exception
	 */
	public function createStringTable($pSheet = null, $pExistingTable = null)
	{
		if (!is_null($pSheet)) {
			// Create string lookup table
			$aStringTable = array();
			$cellCollection = null;
			$aFlippedStringTable = null;	// For faster lookup

			// Is an existing table given?
			if (!is_null($pExistingTable) && is_array($pExistingTable)) {
				$aStringTable = $pExistingTable;
			}

			// Fill index array
			$aFlippedStringTable = $this->flipStringTable($aStringTable);

	        // Loop through cells
	        $cellCollection = $pSheet->getCellCollection();
	        foreach ($cellCollection as $cell) {
	        	if (!is_object($cell->getValue()) &&
	        		!isset($aFlippedStringTable[$cell->getValue()]) &&
	        		!is_null($cell->getValue()) &&
	        		$cell->getValue() !== '' &&
	        		($cell->getDataType() == PHPExcel_Cell_DataType::TYPE_STRING || $cell->getDataType() == PHPExcel_Cell_DataType::TYPE_NULL)
	        	) {
	        			$aStringTable[] = $cell->getValue();
						$aFlippedStringTable[$cell->getValue()] = 1;

	        	} else if ($cell->getValue() instanceof PHPExcel_RichText &&
	        			   !isset($aFlippedStringTable[$cell->getValue()->getHashCode()]) &&
	        			   !is_null($cell->getValue())
	        	) {
	        		$aStringTable[] = $cell->getValue();
					$aFlippedStringTable[$cell->getValue()->getHashCode()] = 1;
	        	}
	        }

	        // Return
	        return $aStringTable;
		} else {
			throw new Exception("Invalid PHPExcel_Worksheet object passed.");
		}
	}

	/**
	 * Write string table to XML format
	 *
	 * @param 	string[] 	$pStringTable
	 * @return 	string 		XML Output
	 * @throws 	Exception
	 */
	public function writeStringTable($pStringTable = null)
	{
		if (!is_null($pStringTable)) {
			// Create XML writer
			$objWriter = null;
			if ($this->getParentWriter()->getUseDiskCaching()) {
				$objWriter = new PHPExcel_Shared_XMLWriter(PHPExcel_Shared_XMLWriter::STORAGE_DISK, $this->getParentWriter()->getDiskCachingDirectory());
			} else {
				$objWriter = new PHPExcel_Shared_XMLWriter(PHPExcel_Shared_XMLWriter::STORAGE_MEMORY);
			}

			// XML header
			$objWriter->startDocument('1.0','UTF-8','yes');

			// String table
			$objWriter->startElement('sst');
			$objWriter->writeAttribute('xmlns', 'http://schemas.openxmlformats.org/spreadsheetml/2006/main');
			$objWriter->writeAttribute('uniqueCount', count($pStringTable));

				// Loop through string table
				foreach ($pStringTable as $textElement) {
					$objWriter->startElement('si');

						if (! $textElement instanceof PHPExcel_RichText) {
							$textToWrite = PHPExcel_Shared_String::ControlCharacterPHP2OOXML( $textElement );
							$objWriter->startElement('t');
							if ($textToWrite !== trim($textToWrite)) {
								$objWriter->writeAttribute('xml:space', 'preserve');
							}
							$objWriter->writeRaw($textToWrite);
							$objWriter->endElement();
						} else if ($textElement instanceof PHPExcel_RichText) {
							$this->writeRichText($objWriter, $textElement);
						}

                    $objWriter->endElement();
				}

			$objWriter->endElement();

			// Return
			return $objWriter->getData();
		} else {
			throw new Exception("Invalid string table array passed.");
		}
	}

	/**
	 * Write Rich Text
	 *
	 * @param 	PHPExcel_Shared_XMLWriter		$objWriter 		XML Writer
	 * @param 	PHPExcel_RichText				$pRichText		Rich text
	 * @throws 	Exception
	 */
	public function writeRichText(PHPExcel_Shared_XMLWriter $objWriter = null, PHPExcel_RichText $pRichText = null)
	{
		// Loop through rich text elements
		$elements = $pRichText->getRichTextElements();
		foreach ($elements as $element) {
			// r
			$objWriter->startElement('r');

				// rPr
				if ($element instanceof PHPExcel_RichText_Run) {
					// rPr
					$objWriter->startElement('rPr');

						// rFont
						$objWriter->startElement('rFont');
						$objWriter->writeAttribute('val', $element->getFont()->getName());
						$objWriter->endElement();

						// Bold
						$objWriter->startElement('b');
						$objWriter->writeAttribute('val', ($element->getFont()->getBold() ? 'true' : 'false'));
						$objWriter->endElement();

						// Italic
						$objWriter->startElement('i');
						$objWriter->writeAttribute('val', ($element->getFont()->getItalic() ? 'true' : 'false'));
						$objWriter->endElement();

						// Superscript / subscript
						if ($element->getFont()->getSuperScript() || $element->getFont()->getSubScript()) {
							$objWriter->startElement('vertAlign');
							if ($element->getFont()->getSuperScript()) {
								$objWriter->writeAttribute('val', 'superscript');
							} else if ($element->getFont()->getSubScript()) {
								$objWriter->writeAttribute('val', 'subscript');
							}
							$objWriter->endElement();
						}

						// Strikethrough
						$objWriter->startElement('strike');
						$objWriter->writeAttribute('val', ($element->getFont()->getStrikethrough() ? 'true' : 'false'));
						$objWriter->endElement();

						// Color
						$objWriter->startElement('color');
						$objWriter->writeAttribute('rgb', $element->getFont()->getColor()->getARGB());
						$objWriter->endElement();

						// Size
						$objWriter->startElement('sz');
						$objWriter->writeAttribute('val', $element->getFont()->getSize());
						$objWriter->endElement();

						// Underline
						$objWriter->startElement('u');
						$objWriter->writeAttribute('val', $element->getFont()->getUnderline());
						$objWriter->endElement();

					$objWriter->endElement();
				}

				// t
				$objWriter->startElement('t');
				$objWriter->writeAttribute('xml:space', 'preserve');
				$objWriter->writeRaw(PHPExcel_Shared_String::ControlCharacterPHP2OOXML( $element->getText() ));
				$objWriter->endElement();

			$objWriter->endElement();
		}
	}

	/**
	 * Flip string table (for index searching)
	 *
	 * @param 	array	$stringTable	Stringtable
	 * @return 	array
	 */
	public function flipStringTable($stringTable = array()) {
		// Return value
		$returnValue = array();

		// Loop through stringtable and add flipped items to $returnValue
		foreach ($stringTable as $key => $value) {
			if (! $value instanceof PHPExcel_RichText) {
				$returnValue[$value] = $key;
			} else if ($value instanceof PHPExcel_RichText) {
				$returnValue[$value->getHashCode()] = $key;
			}
		}

		// Return
		return $returnValue;
	}
}
