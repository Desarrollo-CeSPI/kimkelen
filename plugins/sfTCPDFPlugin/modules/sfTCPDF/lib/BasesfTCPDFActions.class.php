<?php

/**
 * Base sfTCPDF actions for demos.
 *
 * @package    sfTCPDFPlugin
 * @author     Vernet Loïc aka COil <qrf_coil@yahoo.fr>
 * @since      1.6.0 - 16 march 2007
 */

class BasesfTCPDFActions extends sfActions
{
  /**
   * Hello world test.
   */
  public function executeTest()
  {
    $config = sfTCPDFPluginConfigHandler::loadConfig();
    
    // pdf object
    $pdf = new sfTCPDF();

    // settings
    $pdf->SetFont("FreeSerif", "", 12);
    $pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
    $pdf->setHeaderFont(array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
    $pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, PDF_HEADER_TITLE, PDF_HEADER_STRING);
    $pdf->setFooterFont(array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));
    $pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
    $pdf->SetFooterMargin(PDF_MARGIN_FOOTER);

    // init pdf doc
    $pdf->AliasNbPages();
    $pdf->AddPage();
    $pdf->Cell(80, 10, "Hello World !!! & é € U û いろは");

    // output
    $pdf->Output();
    return sfView::NONE;
  }

  /**
   * Full test.
   */
  public function executeTest2()
  {
    $config = sfTCPDFPluginConfigHandler::loadConfig();
    sfTCPDFPluginConfigHandler::includeLangFile($this->getUser()->getCulture());

    $doc_title    = "test title";
    $doc_subject  = "test description";
    $doc_keywords = "test keywords";
    $htmlcontent  = "&lt; € &euro; &#8364; &amp; è &egrave; &copy; &gt;<br /><h1>heading 1</h1><h2>heading 2</h2><h3>heading 3</h3><h4>heading 4</h4><h5>heading 5</h5><h6>heading 6</h6>ordered list:<br /><ol><li><b>bold text</b></li><li><i>italic text</i></li><li><u>underlined text</u></li><li><a href=\"http://www.tecnick.com\">link to http://www.tecnick.com</a></li><li>test break<br />second line<br />third line</li><li><font size=\"+3\">font + 3</font></li><li><small>small text</small></li><li>normal <sub>subscript</sub> <sup>superscript</sup></li></ul><hr />table:<br /><table border=\"1\" cellspacing=\"1\" cellpadding=\"1\"><tr><th>#</th><th>A</th><th>B</th></tr><tr><th>1</th><td bgcolor=\"#cccccc\">A1</td><td>B1</td></tr><tr><th>2</th><td>A2 € &euro; &#8364; &amp; è &egrave; </td><td>B2</td></tr><tr><th>3</th><td>A3</td><td><font color=\"#FF0000\">B3</font></td></tr></table><hr />image:<br /><img src=\"sfTCPDFPlugin/images/logo_example.png\" alt=\"test alt attribute\" width=\"100\" height=\"100\" border=\"0\" />";

    //create new PDF document (document units are set by default to millimeters)
    $pdf = new sfTCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true);

    // set document information
    $pdf->SetCreator(PDF_CREATOR);
    $pdf->SetAuthor(PDF_AUTHOR);
    $pdf->SetTitle($doc_title);
    $pdf->SetSubject($doc_subject);
    $pdf->SetKeywords($doc_keywords);

    $pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, PDF_HEADER_TITLE, PDF_HEADER_STRING);

    //set margins
    $pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);

    //set auto page breaks
    $pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);
    $pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
    $pdf->SetFooterMargin(PDF_MARGIN_FOOTER);
    $pdf->setImageScale(PDF_IMAGE_SCALE_RATIO); //set image scale factor

    $pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
    $pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));

    $pdf->setLanguageArray($l); //set language items

    //initialize document
    $pdf->AliasNbPages();
    $pdf->AddPage();

    // set barcode
    $pdf->SetBarcode(date("Y-m-d H:i:s", time()));

    // output some HTML code
    $pdf->writeHTML($htmlcontent, true, 0);

    // output two html columns
    $first_column_width = 80;
    $current_y_position = $pdf->getY();
    $pdf->writeHTMLCell($first_column_width, 0, 0, $current_y_position, "<b>hello</b>", 0, 0, 0);
    $pdf->writeHTMLCell(0, 0, $first_column_width, $current_y_position, "<i>world</i>", 0, 1, 0);

    // output some content
    $pdf->Cell(0,10,"TEST Bold-Italic Cell",1,1,'C');

    // output some UTF-8 test content
    $pdf->AddPage();
    $pdf->SetFont("FreeSerif", "", 12);

    $utf8text = file_get_contents(K_PATH_CACHE. "utf8test.txt", false); // get utf-8 text form file
    $pdf->SetFillColor(230, 240, 255, true);
    $pdf->Write(5,$utf8text, '', 1);

    // remove page header/footer
    $pdf->setPrintHeader(false);
    $pdf->setPrintFooter(false);

    // Two HTML columns test
    $pdf->AddPage();
    $right_column = "<b>right column</b> right column right column right column right column
    right column right column right column right column right column right column
    right column right column right column right column right column right column";
    $left_column = "<b>left column</b> left column left column left column left column left
    column left column left column left column left column left column left column
    left column left column left column left column left column left column left
    column";
    $first_column_width = 80;
    $second_column_width = 80;
    $column_space = 20;
    $current_y_position = $pdf->getY();
    $pdf->writeHTMLCell($first_column_width, 0, 0, 0, $left_column, 1, 0, 0);
    $pdf->Cell(0);
    $pdf->writeHTMLCell($second_column_width, 0, $first_column_width+$column_space, $current_y_position, $right_column, 0, 0, 0);

    // add page header/footer
    $pdf->setPrintHeader(true);
    $pdf->setPrintFooter(true);

    $pdf->AddPage();

    // Multicell test
    $pdf->MultiCell(40, 5, "A test multicell line 1\ntest multicell line 2\ntest multicell line 3", 1, 'J', 0, 0);
    $pdf->MultiCell(40, 5, "B test multicell line 1\ntest multicell line 2\ntest multicell line 3", 1, 'J', 0);
    $pdf->MultiCell(40, 5, "C test multicell line 1\ntest multicell line 2\ntest multicell line 3", 1, 'J', 0, 0);
    $pdf->MultiCell(40, 5, "D test multicell line 1\ntest multicell line 2\ntest multicell line 3", 1, 'J', 0, 2);
    $pdf->MultiCell(40, 5, "F test multicell line 1\ntest multicell line 2\ntest multicell line 3", 1, 'J', 0);

    //Close and output PDF document
    $pdf->Output();

    return sfView::NONE;
  }
}