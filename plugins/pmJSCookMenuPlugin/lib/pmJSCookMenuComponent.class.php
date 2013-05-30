<?php

/**
 * Abstract class that represents the Component in the Composite pattern.
 * Provides the basic functionality of Composite and Leaf objects.
 *
 * @author Patricio Mac Adden <pmacadden@cespi.unlp.edu.ar>
 */
abstract class pmJSCookMenuComponent
{
  /**
   * The credentials.
   * Just authenticated users that has these credentials should
   * be able to use this component.
   * @var array
   */
  protected $credentials;
  
  /**
   * The component description.
   * @var string
   */
  protected $description;
  
  /**
   * The component icon.
   * @var string
   */
  protected $icon;
  
  /**
   * The component title.
   * @var string
   */
  protected $title;
  
  /**
   * The component target (_blank, _self, etc.).
   * @var string
   */
  protected $target;
  
  /**
   * The component link.
   * @var string
   */
  protected $url;
  
  /**
   * Constructor.
   */
  public function __construct()
  {
    $this->credentials = array();
    $this->icon = "";
    $this->title = "";
    $this->target = "_self";
    $this->url = "";
  }
  
  /**
   * Renders the component.
   *
   * @return string
   */
  abstract public function render();
  
  /**
	 * Set the value of credentials attribute.
	 * 
	 * @param array $v The credentials array
	 * @return pmJSCookMenuComponent The current object (for fluent API support)
	 */
  public function setCredentials($v)
  {
    $this->credentials = $v;
    
    return $this;
  }

  /**
	 * Get the credentials attribute value.
	 * 
	 * @return string
	 */
  public function getCredentials()
  {
    return $this->credentials;
  }
  
  /**
	 * Set the value of description attribute.
	 * 
	 * @param string $v The new description
	 * @return pmJSCookMenuComponent The current object (for fluent API support)
	 */
  public function setDescription($v)
  {
    $this->description = $v;
    
    return $this;
  }

  /**
	 * Get the description attribute value.
	 * 
	 * @return string
	 */
  public function getDescription()
  {
    return $this->description;
  }
  
  /**
	 * Set the value of icon attribute.
	 * 
	 * @param string $v The new icon
	 * @return pmJSCookMenuComponent The current object (for fluent API support)
	 */
  public function setIcon($v)
  {
    $this->icon = $v;
    
    return $this;
  }
  
  /**
	 * Get the icon attribute value.
	 * 
	 * @return string
	 */
  public function getIcon()
  {
    return $this->icon;
  }
  
  /**
	 * Set the value of target attribute.
	 * 
	 * @param string $v The new target
	 * @return pmJSCookMenuComponent The current object (for fluent API support)
	 */
  public function setTarget($v)
  {
    $this->target = $v;
    
    return $this;
  }
  
  /**
	 * Get the target attribute value.
	 * 
	 * @return string
	 */
  public function getTarget()
  {
    return $this->target;
  }
  
  /**
	 * Set the value of title attribute.
	 * 
	 * @param string $v The new title
	 * @return pmJSCookMenuComponent The current object (for fluent API support)
	 */
  public function setTitle($v)
  {
    $this->title = $v;
    
    return $this;
  }

  /**
	 * Get the title attribute value.
	 * 
	 * @return string
	 */
  public function getTitle()
  {
    return $this->title;
  }
  
  /**
	 * Set the value of url attribute.
	 * 
	 * @param string $v The new url
	 * @return pmJSCookMenuComponent The current object (for fluent API support)
	 */
  public function setUrl($v)
  {
    $this->url = $v;
    
    return $this;
  }
  
  /**
	 * Get the url attribute value.
	 * 
	 * @return string
	 */
  public function getUrl()
  {
    return $this->url;
  }
}