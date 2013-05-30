<?php

/**
 * sfTCPDF class.
 *
 * @package    sfTCPDFPlugin
 * @author     Vernet Loïc aka COil <qrf_coil]at[yahoo[dot]fr>
 * @link       http://sourceforge.net/projects/tcpdf/
 */

class sfTCPDF extends TCPDF
{
  /**
   * Instantiate TCPDF lib.
   *
   * @param string $orientation
   * @param string $unit
   * @param string $format
   * @param boolean $unicode
   * @param string $encoding
   */
  public function __construct($orientation = 'P', $unit = 'mm', $format = 'A4', $unicode = true, $encoding = "UTF-8")
  {
    parent::__construct($orientation, $unit, $format, $unicode, $encoding);
  }
}