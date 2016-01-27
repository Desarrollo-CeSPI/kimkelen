<?php

/**
 * changelog_helper actions.
 *
 * @package    symfony
 * @subpackage changelog_helper
 * @author     Your name here
 * @version    SVN: $Id: actions.class.php 12479 2008-10-31 10:54:40Z fabien $
 */
class changelog_helperActions extends sfActions
{
 /**
  * Executes index action
  *
  * @param sfRequest $request A request object
  */
  public function executeIndex(sfWebRequest $request)
  {
    $klass=$request->getParameter('klass');
    $id=$request->getParameter('id');
    $object = call_user_func(array($klass.'Peer','retrieveByPK'),$id);
    $this->getResponse()->setContent(ncChangelogRenderer::render($object));
    return sfView::NONE;
  }
}
