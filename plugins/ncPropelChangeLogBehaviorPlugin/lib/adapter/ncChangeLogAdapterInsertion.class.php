<?php

class ncChangeLogAdapterInsertion extends ncChangeLogAdapter
{
  protected
    $changeLog = array();

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
    return $this->getFormatter()->formatInsertion($this);
  }

  /**
   * Retrieves the HTML representation
   * to be shown in a ncChangeLogEntry listing
   */
  public function renderList($url = null)
  {
    return $this->getFormatter()->formatListInsertion($this, $url);
  }
}

?>
