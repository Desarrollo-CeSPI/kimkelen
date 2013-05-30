<?php

ini_set('include_path', ini_get('include_path'). PATH_SEPARATOR .dirname(__FILE__) . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'lib'. DIRECTORY_SEPARATOR .'PHPExcel');

require_once(dirname(__FILE__).'/../lib/PHPExcel/PHPExcel/IOFactory.php');
require_once(dirname(__FILE__).'/../lib/PHPExcel/PHPExcel.php');
require_once(dirname(__FILE__).'/../lib/sfPhpExcel.class.php');