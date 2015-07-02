<?php
/*
 *  $Id: Configurable.php 5801 2009-06-02 17:30:27Z piccoloprincipe $
 *
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS
 * "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT
 * LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR
 * A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT
 * OWNER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL,
 * SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT
 * LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES; LOSS OF USE,
 * DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON ANY
 * THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT
 * (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE
 * OF THIS SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
 *
 * This software consists of voluntary contributions made by many individuals
 * and is licensed under the LGPL. For more information, see
 * <http://www.phpdoctrine.org>.
 */

/**
 * Doctrine_Configurable
 * the base for Doctrine_Table, Doctrine_Manager and Doctrine_Connection
 *
 * @package     Doctrine
 * @subpackage  Configurable
 * @license     http://www.opensource.org/licenses/lgpl-license.php LGPL
 * @link        www.phpdoctrine.org
 * @since       1.0
 * @version     $Revision: 5801 $
 * @author      Konsta Vesterinen <kvesteri@cc.hut.fi>
 */
abstract class Doctrine_Configurable extends Doctrine_Locator_Injectable
{
    /**
     * @var array $attributes               an array of containing all attributes
     */
    protected $attributes = array();

    /**
     * @var Doctrine_Configurable $parent   the parent of this component
     */
    protected $parent;

    /**
     * @var array $_impl                    an array containing concrete implementations for class templates
     *                                      keys as template names and values as names of the concrete
     *                                      implementation classes
     */
    protected $_impl = array();
    
    /**
     * @var array $_params                  an array of user defined parameters
     */
    protected $_params = array();

    /**
     * getAttributeFromString
     *
     * Will accept the name of an attribute and return the attribute value
     * Example: ->getAttributeFromString('portability') will be converted to Doctrine::ATTR_PORTABILITY
     * and returned
     *
     * @param string $stringAttributeName 
     * @return void
     */
    public function getAttributeFromString($stringAttributeName)
    {
      if (is_string($stringAttributeName)) {
          $upper = strtoupper($stringAttributeName);

          $const = 'Doctrine::ATTR_' . $upper; 

          if (defined($const)) {
              return constant($const);
          } else {
              throw new Doctrine_Exception('Unknown attribute: "' . $stringAttributeName . '"');
          }
      } else {
        return false;
      }
    }

    /**
     * getAttributeValueFromString
     *
     * Will get the value for an attribute by the string name
     * Example: ->getAttributeFromString('portability', 'all') will return Doctrine::PORTABILITY_ALL
     *
     * @param string $stringAttributeName 
     * @param string $stringAttributeValueName 
     * @return void
     */
    public function getAttributeValueFromString($stringAttributeName, $stringAttributeValueName)
    {
        $const = 'Doctrine::' . strtoupper($stringAttributeName) . '_' . strtoupper($stringAttributeValueName);

        if (defined($const)) {
            return constant($const);
        } else {
            throw new Doctrine_Exception('Unknown attribute value: "' . $const . '"');
        }
    }

    /**
     * setAttribute
     * sets a given attribute
     *
     * <code>
     * $manager->setAttribute(Doctrine::ATTR_PORTABILITY, Doctrine::PORTABILITY_ALL);
     *
     * // or
     *
     * $manager->setAttribute('portability', Doctrine::PORTABILITY_ALL);
     *
     * // or
     *
     * $manager->setAttribute('portability', 'all');
     * </code>
     *
     * @param mixed $attribute              either a Doctrine::ATTR_* integer constant or a string
     *                                      corresponding to a constant
     * @param mixed $value                  the value of the attribute
     * @see Doctrine::ATTR_* constants
     * @throws Doctrine_Exception           if the value is invalid
     * @return void
     */
    public function setAttribute($attribute, $value)
    {
        if (is_string($attribute)) {
            $stringAttribute = $attribute;
            $attribute = $this->getAttributeFromString($attribute);
            $this->_state = $attribute;
        }

        if (is_string($value) && isset($stringAttribute)) {
            $value = $this->getAttributeValueFromString($stringAttribute, $value);
        }

        switch ($attribute) {
            case Doctrine::ATTR_FETCHMODE:
                throw new Doctrine_Exception('Deprecated attribute. See http://www.phpdoctrine.org/documentation/manual?chapter=configuration');
            case Doctrine::ATTR_LISTENER:
                $this->setEventListener($value);
                break;
            case Doctrine::ATTR_COLL_KEY:
                if ( ! ($this instanceof Doctrine_Table)) {
                    throw new Doctrine_Exception("This attribute can only be set at table level.");
                }
                if ($value !== null && ! $this->hasField($value)) {
                    throw new Doctrine_Exception("Couldn't set collection key attribute. No such field '$value'.");
                }
                break;
            case Doctrine::ATTR_CACHE:
            case Doctrine::ATTR_RESULT_CACHE:
            case Doctrine::ATTR_QUERY_CACHE:
                if ($value !== null) {
                    if ( ! ($value instanceof Doctrine_Cache_Interface)) {
                        throw new Doctrine_Exception('Cache driver should implement Doctrine_Cache_Interface');
                    }
                }
                break;
            case Doctrine::ATTR_VALIDATE:
            case Doctrine::ATTR_QUERY_LIMIT:
            case Doctrine::ATTR_QUOTE_IDENTIFIER:
            case Doctrine::ATTR_PORTABILITY:
            case Doctrine::ATTR_DEFAULT_TABLE_TYPE:
            case Doctrine::ATTR_EMULATE_DATABASE:
            case Doctrine::ATTR_USE_NATIVE_ENUM:
            case Doctrine::ATTR_DEFAULT_SEQUENCE:
            case Doctrine::ATTR_EXPORT:
            case Doctrine::ATTR_DECIMAL_PLACES:
            case Doctrine::ATTR_LOAD_REFERENCES:
            case Doctrine::ATTR_RECORD_LISTENER:
            case Doctrine::ATTR_THROW_EXCEPTIONS:
            case Doctrine::ATTR_DEFAULT_PARAM_NAMESPACE:
            case Doctrine::ATTR_AUTOLOAD_TABLE_CLASSES:
            case Doctrine::ATTR_MODEL_LOADING:
            case Doctrine::ATTR_RESULT_CACHE_LIFESPAN:
            case Doctrine::ATTR_QUERY_CACHE_LIFESPAN:
            case Doctrine::ATTR_RECURSIVE_MERGE_FIXTURES;
            case Doctrine::ATTR_USE_DQL_CALLBACKS;
            case Doctrine::ATTR_AUTO_ACCESSOR_OVERRIDE;
            case Doctrine::ATTR_HYDRATE_OVERWRITE;

                break;
            case Doctrine::ATTR_SEQCOL_NAME:
                if ( ! is_string($value)) {
                    throw new Doctrine_Exception('Sequence column name attribute only accepts string values');
                }
                break;
            case Doctrine::ATTR_FIELD_CASE:
                if ($value != 0 && $value != CASE_LOWER && $value != CASE_UPPER)
                    throw new Doctrine_Exception('Field case attribute should be either 0, CASE_LOWER or CASE_UPPER constant.');
                break;
            case Doctrine::ATTR_SEQNAME_FORMAT:
            case Doctrine::ATTR_IDXNAME_FORMAT:
            case Doctrine::ATTR_TBLNAME_FORMAT:
                if ($this instanceof Doctrine_Table) {
                    throw new Doctrine_Exception('Sequence / index name format attributes cannot be set'
                                               . 'at table level (only at connection or global level).');
                }
                break;
            default:
                throw new Doctrine_Exception("Unknown attribute.");
        }

        $this->attributes[$attribute] = $value;
    }

    public function getParams($namespace = null)
    {
    	if ($namespace == null) {
    	    $namespace = $this->getAttribute(Doctrine::ATTR_DEFAULT_PARAM_NAMESPACE);
    	}
    	
    	if ( ! isset($this->_params[$namespace])) {
    	    return null;
    	}

        return $this->_params[$namespace];
    }
    
    public function getParamNamespaces()
    {
        return array_keys($this->_params);
    }

    public function setParam($name, $value, $namespace = null) 
    {
    	if ($namespace == null) {
    	    $namespace = $this->getAttribute(Doctrine::ATTR_DEFAULT_PARAM_NAMESPACE);
    	}
    	
    	$this->_params[$namespace][$name] = $value;
    	
    	return $this;
    }
    
    public function getParam($name, $namespace = null) 
    {
    	if ($namespace == null) {
    	    $namespace = $this->getAttribute(Doctrine::ATTR_DEFAULT_PARAM_NAMESPACE);
    	}
    	
        if ( ! isset($this->_params[$namespace][$name])) {
            if (isset($this->parent)) {
                return $this->parent->getParam($name, $namespace);
            }
            return null;
        }
        
        return $this->_params[$namespace][$name];
    }
    /**
     * setImpl
     * binds given class to given template name
     *
     * this method is the base of Doctrine dependency injection
     *
     * @param string $template      name of the class template
     * @param string $class         name of the class to be bound
     * @return Doctrine_Configurable    this object
     */
    public function setImpl($template, $class)
    {
        $this->_impl[$template] = $class;

        return $this;
    }

    /**
     * getImpl
     * returns the implementation for given class
     *
     * @return string   name of the concrete implementation
     */
    public function getImpl($template)
    {
        if ( ! isset($this->_impl[$template])) {
            if (isset($this->parent)) {
                return $this->parent->getImpl($template);
            }
            return null;
        }
        return $this->_impl[$template];
    }
    
    
    public function hasImpl($template)
    {
        if ( ! isset($this->_impl[$template])) {
            if (isset($this->parent)) {
                return $this->parent->hasImpl($template);
            }
            return false;
        }
        return true;
    }

    /**
     * @param Doctrine_EventListener $listener
     * @return void
     */
    public function setEventListener($listener)
    {
        return $this->setListener($listener);
    }

    /**
     * addRecordListener
     *
     * @param Doctrine_EventListener_Interface|Doctrine_Overloadable $listener
     * @return mixed        this object
     */
    public function addRecordListener($listener, $name = null)
    {
        if ( ! isset($this->attributes[Doctrine::ATTR_RECORD_LISTENER]) ||
             ! ($this->attributes[Doctrine::ATTR_RECORD_LISTENER] instanceof Doctrine_Record_Listener_Chain)) {

            $this->attributes[Doctrine::ATTR_RECORD_LISTENER] = new Doctrine_Record_Listener_Chain();
        }
        $this->attributes[Doctrine::ATTR_RECORD_LISTENER]->add($listener, $name);

        return $this;
    }

    /**
     * getListener
     *
     * @return Doctrine_EventListener_Interface|Doctrine_Overloadable
     */
    public function getRecordListener()
    {
        if ( ! isset($this->attributes[Doctrine::ATTR_RECORD_LISTENER])) {
            if (isset($this->parent)) {
                return $this->parent->getRecordListener();
            }
            return null;
        }
        return $this->attributes[Doctrine::ATTR_RECORD_LISTENER];
    }

    /**
     * setListener
     *
     * @param Doctrine_EventListener_Interface|Doctrine_Overloadable $listener
     * @return Doctrine_Configurable        this object
     */
    public function setRecordListener($listener)
    {
        if ( ! ($listener instanceof Doctrine_Record_Listener_Interface)
            && ! ($listener instanceof Doctrine_Overloadable)
        ) {
            throw new Doctrine_Exception("Couldn't set eventlistener. Record listeners should implement either Doctrine_Record_Listener_Interface or Doctrine_Overloadable");
        }
        $this->attributes[Doctrine::ATTR_RECORD_LISTENER] = $listener;

        return $this;
    }

    /**
     * addListener
     *
     * @param Doctrine_EventListener_Interface|Doctrine_Overloadable $listener
     * @return mixed        this object
     */
    public function addListener($listener, $name = null)
    {
        if ( ! isset($this->attributes[Doctrine::ATTR_LISTENER]) ||
             ! ($this->attributes[Doctrine::ATTR_LISTENER] instanceof Doctrine_EventListener_Chain)) {

            $this->attributes[Doctrine::ATTR_LISTENER] = new Doctrine_EventListener_Chain();
        }
        $this->attributes[Doctrine::ATTR_LISTENER]->add($listener, $name);

        return $this;
    }

    /**
     * getListener
     *
     * @return Doctrine_EventListener_Interface|Doctrine_Overloadable
     */
    public function getListener()
    {
        if ( ! isset($this->attributes[Doctrine::ATTR_LISTENER])) {
            if (isset($this->parent)) {
                return $this->parent->getListener();
            }
            return null;
        }
        return $this->attributes[Doctrine::ATTR_LISTENER];
    }

    /**
     * setListener
     *
     * @param Doctrine_EventListener_Interface|Doctrine_Overloadable $listener
     * @return Doctrine_Configurable        this object
     */
    public function setListener($listener)
    {
        if ( ! ($listener instanceof Doctrine_EventListener_Interface)
            && ! ($listener instanceof Doctrine_Overloadable)
        ) {
            throw new Doctrine_EventListener_Exception("Couldn't set eventlistener. EventListeners should implement either Doctrine_EventListener_Interface or Doctrine_Overloadable");
        }
        $this->attributes[Doctrine::ATTR_LISTENER] = $listener;

        return $this;
    }

    /**
     * returns the value of an attribute
     *
     * @param integer $attribute
     * @return mixed
     */
    public function getAttribute($attribute)
    {
        if (is_string($attribute)) {
            $upper = strtoupper($attribute);

            $const = 'Doctrine::ATTR_' . $upper; 

            if (defined($const)) {
                $attribute = constant($const);
                $this->_state = $attribute;
            } else {
                throw new Doctrine_Exception('Unknown attribute: "' . $attribute . '"');
            }
        }

        $attribute = (int) $attribute;

        if ($attribute < 0) {
            throw new Doctrine_Exception('Unknown attribute.');
        }

        if (isset($this->attributes[$attribute])) {
            return $this->attributes[$attribute];
        }
        
        if (isset($this->parent)) {
            return $this->parent->getAttribute($attribute);
        }
        return null;
    }

    /**
     * getAttributes
     * returns all attributes as an array
     *
     * @return array
     */
    public function getAttributes()
    {
        return $this->attributes;
    }

    /**
     * sets a parent for this configurable component
     * the parent must be configurable component itself
     *
     * @param Doctrine_Configurable $component
     * @return void
     */
    public function setParent(Doctrine_Configurable $component)
    {
        $this->parent = $component;
    }

    /**
     * getParent
     * returns the parent of this component
     *
     * @return Doctrine_Configurable
     */
    public function getParent()
    {
        return $this->parent;
    }
}
