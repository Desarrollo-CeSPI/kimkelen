<?php

/**
 * autoNcchangelogentry actions.
 *
 * @package    sumarios
 * @subpackage ncchangelogentry
 * @author     ncuesta
 * @version    SVN: $Id: autoNcchangelogentryActions.class.php 30815 2010-09-03 13:09:38Z ncuesta $
 */
class autoNcchangelogentryActions extends sfActions
{
 /**
  * Executes index action.
  *
  * @param sfRequest $request A request object
  */
  public function executeIndex(sfWebRequest $request)
  {
    $this->object = $this->getObject($request);
    $this->forward404Unless($this->object);

    $this->nc_change_log_entries         = $this->object->getChangeLog();
    $this->related_nc_change_log_entries = $this->object->get1NRelatedChangeLog();
    $this->nn_related_nc_change_log_entries = $this->object->getNNRelatedChangeLog();

    $keys = array_keys($this->nc_change_log_entries);
    $this->class_name = count($this->nc_change_log_entries) > 0? $this->nc_change_log_entries[$keys[0]]->renderClassName() : get_class($this->object);
  }

  /**
   * Executes show action.
   *
   * @param sfWebRequest $request
   */
  public function executeShow(sfWebRequest $request)
  {
    $this->nc_change_log_entry = ncChangeLogEntryPeer::retrieveByPK($request->getParameter('id'));
    $this->forward404Unless($this->nc_change_log_entry);
    $this->nc_change_log_entry = $this->nc_change_log_entry->getAdapter();
    $this->object = $this->nc_change_log_entry->getObject();
  }

  /**
   * Use $request to obtain the object of the changeLog.
   * 
   * @param sfWebRequest $request
   */
  protected function getObject(sfWebRequest $request)
  {
    $this->class = $request->getParameter('class');
    if (class_exists($this->class))
    {
      $peer_class = constant($this->class.'::PEER');
      if (class_exists($peer_class))
      {
        return call_user_func(array($peer_class, 'retrieveByPK'), $request->getParameter('pk'));
      }
      
      throw new sfException('Unable to find class "'.$peer_class.'".');
    }

    throw new sfException('Unable to find class "'.$request->getParameter('class').'".');
  }
}
