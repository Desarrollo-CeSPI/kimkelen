<?php

/**
 * Represents the Leaf in the Composite pattern.
 * Represents a menu item.
 *
 * @author Patricio Mac Adden <pmacadden@cespi.unlp.edu.ar>
 */
class pmJSCookMenuItem extends pmJSCookMenuComponent
{
  /**
   * Renders the pmJSCookMenuItem.
   *
   * @return string
   */
  public function render()
  {
    $context = sfContext::getInstance();
    $user = $context->getUser();
    
    $context->getConfiguration()->loadHelpers(array("Asset", "I18N", "Url"));
    
    $has_credentials = $user->hasCredential($this->getCredentials());
    
    $item = "";
    
    if ($has_credentials)
    {
      $description = __($this->getDescription());
      $icon = $this->getIcon() ? image_tag($this->getIcon()): "null";
      if ($icon != "null") $icon = "'$icon'";
      $target = $this->getTarget();
      $title = __($this->getTitle());
      $url = $this->getUrl() ? url_for($this->getUrl()) : "null";
      if ($url != "null") $url = "'$url'";
    
      $item = "[$icon, '$title', $url, '$target', '$description']";
    }
    
    return $item;
  }
}