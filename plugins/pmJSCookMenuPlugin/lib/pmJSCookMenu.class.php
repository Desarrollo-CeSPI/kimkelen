<?php

/**
 * Represents the Composite in the Composite pattern.
 * Represents a menu and all it's children.
 *
 * @author Patricio Mac Adden <pmacadden@cespi.unlp.edu.ar>
 */
class pmJSCookMenu extends pmJSCookMenuComponent
{
  /**
   * The composite children.
   * @var array
   */
  protected $children;
  
  /**
   * Indicates if the composite is a root.
   * @var boolean
   */
  protected $is_root;
  
  /**
   * Indicates the composite's orientation (just needed by root).
   * @var string
   */
  protected $orientation;
  
  /**
   * Indicates the composite's theme (just needed by root).
   * @var string
   */
  protected $theme;
    
  /**
   * Constructor.
   */
  public function __construct()
  {
    parent::__construct();
    
    $this->children = array();
    $this->is_root = false;
    $this->orientation = null;
    $this->theme = null;
  }
  
  /**
	 * Create an instance of pmJSCookMenu from a yaml file.
	 * 
	 * @param string $yaml_file The yaml file path
	 * @return pmJSCookMenu
	 */
  public static function createFromYaml($yaml_file)
  {
    $yaml = sfYaml::load($yaml_file);
    $yaml = array_pop($yaml);
    
    $root = new pmJSCookMenu();
    
    $root_attrs = array(
      "credentials",
      "description",
      "icon",
      "orientation",
      "target",
      "theme",
      "title",
      "url"
    );
    
    foreach ($root_attrs as $attr)
    {
      if (isset($yaml[$attr]))
      {
        $method = "set".ucfirst($attr);
        call_user_func(array($root, $method), $yaml[$attr]);
        unset($yaml[$attr]);
      }
    }
    
    if (isset($yaml["root"]) && $yaml["root"] == true)
    {
      $root->setRoot();
      unset($yaml["root"]);
    }
    
    $separator_count = 0;
    foreach ($yaml as $name => $arr_menu)
    {
      if ($name == "separator")
      {
        $item = new pmJSCookMenuSeparator();
        $root->addChild("$name$separator_count", $item);
        $separator_count++;
      }
      else
      {
        $item = self::createMenu($arr_menu);
        $root->addChild($name, $item);
      }
    }
    
    return $root;
  }
  
  /**
	 * Create a submenu from an array.
	 * 
	 * @param array $arr The array
	 * @return pmJSCookMenuComponent
	 */
  protected static function createMenu($arr)
  {
    $item = null;
    if (array_key_exists("menu", $arr))
    {
      $item = new pmJSCookMenu();
      $separator_count = 0;
      foreach ($arr["menu"] as $name => $submenu)
      {
        if ($name == "separator")
        {
          $sitem = new pmJSCookMenuSeparator();
          $item->addChild("$name$separator_count", $sitem);
          $separator_count++;
        }
        else
        {
          $sitem = self::createMenu($submenu);
          $item->addChild($name, $sitem);
        }
      }
    }
    else
    {
      $item = new pmJSCookMenuItem();
    }
    if (array_key_exists("credentials", $arr)) $item->setCredentials($arr["credentials"]);
    if (array_key_exists("description", $arr)) $item->setDescription($arr["description"]);
    if (array_key_exists("icon", $arr)) $item->setIcon($arr["icon"]);
    if (array_key_exists("target", $arr)) $item->setTarget($arr["target"]);
    if (array_key_exists("title", $arr)) $item->setTitle($arr["title"]);
    if (array_key_exists("url", $arr)) $item->setUrl($arr["url"]);
    
    return $item;
  }
  
  /**
	 * Get the children attribute value.
	 * 
	 * @return array
	 */
  public function getChildren()
  {
    return $this->children;
  }
  
  /**
	 * Get a child.
	 *
	 * @param string $name The child name
	 * @return mixed
	 */
  public function getChild($name)
  {
    $child = null;
    
    if (isset($this->children[$name]))
    {
      $child = $this->children[$name];
    }
    
    return $child;
  }

  /**
	 * Adds a child.
	 *
	 * @param string $name The child name
	 * @param pmJSCookMenuComponent $component The child
	 * @return pmJSCookMenu The current object (for fluent API support)
	 */
  public function addChild($name, pmJSCookMenuComponent $component)
  {
    $this->children[$name] = $component;
    
    return $this;
  }
  
  /**
	 * Removes a child.
	 *
	 * @param string $name The child name
	 * @return pmJSCookMenu The current object (for fluent API support)
	 */
  public function removeChild($name)
  {
    unset($this->children[$name]);
    
    return $this;
  }

  /**
	 * Set the current object as the root.
	 * 
	 * @return pmJSCookMenu The current object (for fluent API support)
	 */
  public function setRoot()
  {
    $this->is_root = true;
    
    return $this;
  }
  
  /**
	 * Unset the current object as the root.
	 * 
	 * @return pmJSCookMenu (for fluent API support)
	 */
  public function unsetRoot()
  {
    $this->is_root = false;
    
    return $this;
  }
  
  /**
	 * Returns if the current object is the root.
	 * 
	 * @return boolean
	 */
  public function isRoot()
  {
    return $this->is_root;
  }
  
  /**
	 * Set the value of orientation attribute.
	 * 
	 * @param string $v The new orientation
	 * @return pmJSCookMenu The current object (for fluent API support)
	 */
  public function setOrientation($v)
  {
    $this->orientation = $v;
    
    return $this;
  }
  
  /**
	 * Get the orientation attribute value.
	 * 
	 * @return string
	 */
  public function getOrientation()
  {
    return $this->orientation;
  }
  
  /**
	 * Set the value of theme attribute.
	 * 
	 * @param string $v The new theme
	 * @return pmJSCookMenu The current object (for fluent API support)
	 */
  public function setTheme($v)
  {
    $this->theme = $v;
    
    return $this;
  }
  
  /**
	 * Get the theme attribute value.
	 * 
	 * @return string
	 */
  public function getTheme()
  {
    return $this->theme;
  }
  
  /**
   * Renders the pmJSCookMenu.
   *
   * @return string
   */
  public function render()
  {
    $context = sfContext::getInstance();
    $user = $context->getUser();
    
    $context->getConfiguration()->loadHelpers(array("I18N", "Url"));
    
    $has_credentials = $user->hasCredential($this->getCredentials());
    
    $js = "";
    
    if ($has_credentials)
    {
      $orientation = $this->getOrientation();
      $theme = $this->getTheme();
    
      if ($this->isRoot())
      {
        $request = $context->getRequest();
        $uri_prefix = $request->getUriPrefix();
        $relative_url_root = $request->getRelativeUrlRoot();
       
        $js .= <<<EOF
<div id="jscookmenu"></div>
<script>
var theme = "$theme";
var my${theme}Base = "$uri_prefix$relative_url_root/pmJSCookMenuPlugin/images/$theme/";
var cmBase = my${theme}Base;

var jscookmenu = 
[
EOF;
      }
      else
      {
        $description = __($this->getDescription());
        $icon = $this->getIcon() ? image_tag($this->getIcon()): "null";
        if ($icon != "null") $icon = "'$icon'";
        $target = $this->getTarget();
        $title = __($this->getTitle());
        $url = $this->getUrl() ? url_for($this->getUrl()) : "null";
        if ($url != "null") $url = "'$url'";
    
        $js .= "[$icon, '$title', $url, '$target', '$description',";
      }
    
      foreach ($this->getChildren() as $child)
      {
        $js .= $child->render().",";
      }
    
      $js = substr($js, 0, -1);

      if ($this->isRoot())
      {
        $js .= <<<EOF
];

cmDraw("jscookmenu", jscookmenu, "$orientation", cm$theme);
</script>
EOF;
      }
      else
      {
        $js .= "]";
      }
    }
    
    return $js;
  }
}