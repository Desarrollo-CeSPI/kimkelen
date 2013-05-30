<?php

/**
 * Represents the Leaf in the Composite pattern.
 * Represents a separator.
 *
 * @author Patricio Mac Adden <pmacadden@cespi.unlp.edu.ar>
 */
class pmJSCookMenuSeparator extends pmJSCookMenuComponent
{
  /**
   * Renders the pmJSCookMenuItem.
   *
   * @return string
   */
  public function render()
  {
    return "_cmSplit";
  }
}