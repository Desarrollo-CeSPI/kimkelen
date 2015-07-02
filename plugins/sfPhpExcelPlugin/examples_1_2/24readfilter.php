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
 * @license    http://www.gnu.org/licenses/old-licenses/lgpl-2.1.txt	LGPL
 * @version    1.6.7, 2009-04-22
 */

/* Modified by Bertrand Zuchuat */
require_once 'symfony.inc.php';

require_once 'PHPExcel/Reader/IReadFilter.php';

// Check prerequisites
if (!file_exists("06largescale.xlsx")) {
	exit("Please run 06largescale.php first.\n");
}

class MyReadFilter implements PHPExcel_Reader_IReadFilter
{
	public function readCell($column, $row, $worksheetName = '') {
		// Read title row and rows 20 - 30
		if ($row == 1 || ($row >= 20 && $row <= 30)) {
			return true;
		}
		
		return false;
	}
}


echo date('H:i:s') . " Load from Excel2007 file\n";
$objReader = PHPExcel_IOFactory::createReader('Excel2007');
$objReader->setReadFilter( new MyReadFilter() );
$objPHPExcel = $objReader->load("06largescale.xlsx");

echo date('H:i:s') . " Remove unnecessary rows\n";
$objPHPExcel->getActiveSheet()->removeRow(2, 18);

echo date('H:i:s') . " Write to Excel2007 format\n";
$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
$objWriter->save(str_replace('.php', '.xlsx', __FILE__));


// Echo memory peak usage
echo date('H:i:s') . " Peak memory usage: " . (memory_get_peak_usage(true) / 1024 / 1024) . " MB\r\n";

// Echo done
echo date('H:i:s') . " Done writing files.\r\n";
