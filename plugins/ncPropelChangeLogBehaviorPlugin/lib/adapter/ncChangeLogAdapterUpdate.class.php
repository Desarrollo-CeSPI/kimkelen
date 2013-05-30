<?php

class ncChangeLogAdapterUpdate extends ncChangeLogAdapter
{
  protected
    $changeLog = null;
  /************************
   *      Own methods     *
   ***********************/

  protected function createChangeLog()
  {
    foreach ($this->getChanges() as $value)
    {
      $this->changeLog[$value['field']] = new ncChangeLogUpdateChange($value['field'], $value['old'], $value['new'], $this);
    }
  }

  /**
   * Retrieves the changes
   *
   * @return Array The affected changes
   */
  public function getChangeLog()
  {
    if (is_null($this->changeLog))
      $this->createChangeLog();
    return $this->changeLog;
  }

  public function __construct($entry)
  {
    parent::__construct($entry);
  }

  protected function getChanges()
  {
    return $this->entry->getObjectChanges();
  }


  /**************************
   *        Format!         *
   **************************/



  /**
   * Retrieves the HTML representation of the class name.
   * It may transform the class name values (eg. translation)
   *
   * @return String HTML representation of the className.
   */
  public function renderClassName()
  {
    return $this->translate($this->getClassName());
  }


  /**
   * Retrieves the HTML representation of the changes
   *
   * @return String HTML representation of the changes.
   */
  public function render()
  {
    return $this->getFormatter()->formatUpdate($this);
  }

  /**
   * Retrieves the HTML representation
   * to be shown in a ncChangeLogEntry listing
   */
  public function renderList($url = null)
  {
    return $this->getFormatter()->formatListUpdate($this, $url);
  }
}

?>
