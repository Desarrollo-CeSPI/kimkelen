<?php

require_once dirname(__FILE__).'/../lib/bookGeneratorConfiguration.class.php';
require_once dirname(__FILE__).'/../lib/bookGeneratorHelper.class.php';

/**
 * book actions.
 *
 * @package    symfony
 * @subpackage book
 * @author     Your name here
 * @version    SVN: $Id: actions.class.php 12474 2008-10-31 10:41:27Z fabien $
 */
class bookActions extends autoBookActions
{
    
  public function executeCheckSheetBook(sfWebRequest $request)
  {
    $book_id = $request->getParameter('book_id');
    $physical_sheet = $request->getParameter('physical_sheet');
    
    $records_sheets = RecordSheetPeer::retrieveByPshysicalSheetAndBook($physical_sheet,$book_id);
     
    return $this->renderPartial('check_sheet_book',array('records_sheets' => $records_sheets));
  }
}
