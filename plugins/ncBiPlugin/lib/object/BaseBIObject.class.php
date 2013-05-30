<?php

/**
 * BaseBIObject
 *
 * @author José Nahuel Cuesta Luengo <ncuesta@cespi.unlp.edu.ar>
 */
abstract class BaseBIObject
{
  /**
   * @var mixed
   */
  protected $identifier = null;

  /**
   * @var array
   */
  protected $properties = array();

  /**
   * Constructor.
   *
   * @param mixed $identifier An identifier for this object, parseable with setIdentifier().
   * @param array $properties An optional array of properties for this object.
   */
  public function __construct($identifier, $properties = array())
  {
    $this->setIdentifier($identifier);
    
    $this->setProperties($this->getDefaultProperties());
    $this->mergeProperties($properties);
  }

  /**
   * Set the identifier for this BI Object.
   * Return this object for a fluent API.
   *
   * @param  mixed $identifier The identifier to set.
   *
   * @return BaseBIObject
   */
  public function setIdentifier($identifier)
  {
    $this->identifier = $identifier;

    return $this;
  }

  /**
   * Get the identifier of this BI Object.
   *
   * @return mixed
   */
  public function getIdentifier()
  {
    return $this->identifier;
  }

  /**
   * Set the properties for this BI Object.
   * Return this object for a fluent API.
   *
   * @param  array $properties The properties to set.
   *
   * @return BaseBIObject
   */
  public function setProperties(array $properties)
  {
    $this->properties = $properties;

    return $this;
  }

  /**
   * Merge $properties with the current properties of this BI Object.
   * Return this object for a fluent API.
   * 
   * @param  array $properties The properties to merge.
   *
   * @return BaseBIObject
   */
  public function mergeProperties(array $properties)
  {
    if (!is_array($this->properties))
    {
      $this->properties = array();
    }
    
    $this->properties = array_merge($this->properties, $properties);

    return $this;
  }

  /**
   * Get the properties of this BI Object.
   *
   * @return array
   */
  public function getProperties()
  {
    return $this->properties;
  }

  /**
   * Set a property to this BI Object.
   * Return this object for a fluent API.
   * 
   * @param  string $name  The name of the property to set.
   * @param  mixed  $value The value to set to the property $name.
   *
   * @return BaseBIObject
   */
  public function setProperty($name, $value)
  {
    $this->properties[$name] = $value;

    return $this;
  }

  /**
   * Get a property of this BI Object, or a default value.
   * 
   * @param  string $name    The name of the property.
   * @param  mixed  $default The default value to return when $name property isn't set.
   *
   * @return mixed
   */
  public function getProperty($name, $default = null)
  {
    return array_key_exists($name, $this->properties) ? $this->properties[$name] : $default;
  }

  /**
   * Get an array with the parameters set to this BI Object.
   * Any property whose name is not among the admitted properties
   * is considered a parameter.
   *
   * @see getAdmittedProperties()
   *
   * @return array
   */
  public function getParameters()
  {
    $parameters = array();

    $diff = array_diff(array_keys($this->properties), $this->getAdmittedProperties());

    foreach ($diff as $name)
    {
      $parameters[$name] = $this->getProperty($name);
    }

    return $this->addParameters($parameters);
  }

  /**
   * Download this BI Object and return its content as a string.
   *
   * @return string
   */
  public function download()
  {
    return BIServerClient::create()->get($this);
  }

  /**
   * Get the default properties for this kind of BI Objects.
   *
   * @abstract
   * 
   * @return array
   */
  abstract protected function getDefaultProperties();

  /**
   * Get the names of the admitted properties for this object that are not parameters.
   * 
   * @abstract
   *
   * @return array
   */
  abstract protected function getAdmittedProperties();

  /**
   * Add any additional parameters that are inferred from the properties of this BI Object
   * and return the resulting array.
   *
   * @abstract
   *
   * @return array
   */
  abstract protected function addParameters(array $parameters);

  /**
   * Get the relative path in the server for this kind of BI Objects.
   * 
   * @abstract
   *
   * @return string
   */
  abstract public function getRelativeServerPath();
  
}
