<?php

/*
 * This file is part of the symfony package.
 * (c) Fabien Potencier <fabien.potencier@symfony-project.com>
 * (c) Jonathan H. Wage <jonwage@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * @package    symfony
 * @subpackage doctrine
 * @author     Fabien Potencier <fabien.potencier@symfony-project.com>
 * @author     Jonathan H. Wage <jonwage@gmail.com>
 * @version    SVN: $Id: sfFormDoctrine.class.php 7845 2008-03-12 22:36:14Z fabien $
 */

/**
 * sfFormDoctrine is the base class for forms based on Doctrine objects.
 *
 * @package    symfony
 * @subpackage form
 * @author     Fabien Potencier <fabien.potencier@symfony-project.com>
 * @author     Jonathan H. Wage <jonwage@gmail.com>
 * @version    SVN: $Id: sfFormDoctrine.class.php 7845 2008-03-12 22:36:14Z fabien $
 */
abstract class sfFormDoctrine extends sfForm
{
  protected
    $isNew  = true,
    $object = null;

  /**
   * Constructor.
   *
   * @param BaseObject A Doctrine object used to initialize default values
   * @param array      An array of options
   * @param string     A CSRF secret (false to disable CSRF protection, null to use the global CSRF secret)
   *
   * @see sfForm
   */
  public function __construct($object = null, $options = array(), $CSRFSecret = null)
  {
    $class = $this->getModelName();
    if (!$object)
    {
      $this->object = new $class();
    }
    else
    {
      if (!$object instanceof $class)
      {
        throw new sfException(sprintf('The "%s" form only accepts a "%s" object.', get_class($this), $class));
      }

      $this->object = $object;
      $this->isNew = !$this->object->exists();
    }

    parent::__construct(array(), $options, $CSRFSecret);

    $this->updateDefaultsFromObject();
  }

  /**
   * Returns the default connection for the current model.
   *
   * @return Connection A database connection
   */
  public function getConnection()
  {
    return Doctrine_Manager::getInstance()->getConnectionForComponent($this->getModelName());
  }

  /**
   * Returns the current model name.
   */
  abstract public function getModelName();

  /**
   * Returns true if the current form embeds a new object.
   *
   * @return Boolean true if the current form embeds a new object, false otherwise
   */
  public function isNew()
  {
    $this->isNew = !$this->object->exists();

    return $this->isNew;
  }

  /**
   * Embeds i18n objects into the current form.
   *
   * @param array   $cultures   An array of cultures
   * @param string  $decorator  A HTML decorator for the embedded form
   */
  public function embedI18n($cultures, $decorator = null)
  {
    if (!$this->isI18n())
    {
      throw new sfException(sprintf('The model "%s" is not internationalized.', $this->getModelName()));
    }

    $class = $this->getI18nFormClass();
    foreach ($cultures as $culture)
    {
      $i18nObject = $this->object->Translation[$culture];
      $i18n = new $class($i18nObject);
      unset($i18n['id'], $i18n['lang']);

      $this->embedForm($culture, $i18n, $decorator);
    }
  }

  /**
   * Returns the current object for this form.
   *
   * @return BaseObject The current object.
   */
  public function getObject()
  {
    return $this->object;
  }

  /**
   * Binds the current form and save the to the database in one step.
   *
   * @param  array      An array of tainted values to use to bind the form
   * @param  array      An array of uploaded files (in the $_FILES or $_GET format)
   * @param  Connection An optional Doctrine Connection object
   *
   * @return Boolean    true if the form is valid, false otherwise
   */
  public function bindAndSave($taintedValues, $taintedFiles = null, $con = null)
  {
    $this->bind($taintedValues, $taintedFiles);
    if ($this->isValid())
    {
      $this->save($con);

      return true;
    }

    return false;
  }

  /**
   * Saves the current object to the database.
   *
   * The object saving is done in a transaction and handled by the doSave() method.
   *
   * If the form is not valid, it throws an sfValidatorError.
   *
   * @param Connection An optional Connection object
   *
   * @return BaseObject The current saved object
   *
   * @see doSave()
   */
  public function save($con = null)
  {
    if (!$this->isValid())
    {
      throw $this->getErrorSchema();
    }

    if (is_null($con))
    {
      $con = $this->getConnection();
    }

    try
    {
      $con->beginTransaction();

      $this->doSave($con);

      $con->commit();
    }
    catch (Exception $e)
    {
      $con->rollback();

      throw $e;
    }

    return $this->object;
  }

  /**
   * Updates the values of the object with the cleaned up values.
   *
   * @param  array $values An array of values
   *
   * @return BaseObject The current updated object
   */
  public function updateObject($values = null)
  {
    if (is_null($values))
    {
      $values = $this->values;
    }

    $values = $this->processValues($values);

    $this->object->fromArray($values);

    // embedded forms
    $this->updateObjectEmbeddedForms($values);

    return $this->object;
  }

  /**
   * Updates the values of the objects in embedded forms.
   *
   * @param array $values An array of values
   * @param array $forms  An array of forms
   */
  public function updateObjectEmbeddedForms($values, $forms = null)
  {
    if (is_null($forms))
    {
      $forms = $this->embeddedForms;
    }

    foreach ($forms as $name => $form)
    {
      if (!isset($values[$name]) || !is_array($values[$name]))
      {
        continue;
      }

      if ($form instanceof sfFormDoctrine)
      {
        $form->updateObject($values[$name]);
      }
      else
      {
        $this->updateObjectEmbeddedForms($values[$name], $form->getEmbeddedForms());
      }
    }
  }

  /**
   * Processes cleaned up values with user defined methods.
   *
   * To process a value before it is used by the updateObject() method,
   * you need to define an updateXXXColumn() method where XXX is the PHP name
   * of the column.
   *
   * The method must return the processed value or false to remove the value
   * from the array of cleaned up values.
   *
   * @return array An array of cleaned up values processed by the user defined methods
   */
  public function processValues($values = null)
  {
    // see if the user has overridden some column setter
    $valuesToProcess = $values;
    foreach ($valuesToProcess as $field => $value)
    {
      $method = sprintf('update%sColumn', self::camelize($field));

      if (method_exists($this, $method))
      {
        if (false === $ret = $this->$method($value))
        {
          unset($values[$field]);
        }
        else
        {
          $values[$field] = $ret;
        }
      }
      else
      {
        // save files
        if ($this->validatorSchema[$field] instanceof sfValidatorFile)
        {
          $values[$field] = $this->processUploadedFile($field, null, $valuesToProcess);
        }
      }
    }

    return $values;
  }

  /**
   * Returns true if the current form has some associated i18n objects.
   *
   * @return Boolean true if the current form has some associated i18n objects, false otherwise
   */
  public function isI18n()
  {
    return $this->getObject()->getTable()->hasTemplate('Doctrine_Template_I18n');
  }

  /**
   * Returns the name of the i18n model.
   *
   * @return string The name of the i18n model
   */
  public function getI18nModelName()
  {
    return $this->getObject()->getTable()->getTemplate('Doctrine_Template_I18n')->getI18n()->getOption('className');
  }

  /**
   * Returns the name of the i18n form class.
   *
   * @return string The name of the i18n form class
   */
  public function getI18nFormClass()
  {
    return $this->getI18nModelName() . 'Form';
  }

  /**
   * Renders a form tag suitable for the related Doctrine object.
   *
   * The method is automatically guessed based on the Doctrine object:
   *
   *  * if the object is new, the method is POST
   *  * if the object already exists, the method is PUT
   *
   * @param  string $url         The URL for the action
   * @param  array  $attributes  An array of HTML attributes
   *
   * @return string An HTML representation of the opening form tag
   *
   * @see sfForm
   */
  public function renderFormTag($url, array $attributes = array())
  {
    if (!isset($attributes['method']))
    {
      $attributes['method'] = $this->isNew() ? 'post' : 'put';
    }

    return parent::renderFormTag($url, $attributes);
  }

  /**
   * Updates and saves the current object.
   *
   * If you want to add some logic before saving or save other associated objects,
   * this is the method to override.
   *
   * @param Connection An optional Connection object
   */
  protected function doSave($con = null)
  {
    if (is_null($con))
    {
      $con = $this->getConnection();
    }

    $this->updateObject();

    $this->object->save($con);

    // embedded forms
    $this->saveEmbeddedForms($con);
  }

  /**
   * Saves embedded form objects.
   *
   * @param Connection $con   An optional Connection object
   * @param array      $forms An array of forms
   */
  public function saveEmbeddedForms($con = null, $forms = null)
  {
    if (is_null($con))
    {
      $con = $this->getConnection();
    }

    if (is_null($forms))
    {
      $forms = $this->embeddedForms;
    }

    foreach ($forms as $form)
    {
      if ($form instanceof sfFormDoctrine)
      {
        $form->getObject()->save($con);
        $form->saveEmbeddedForms($con);
      }
      else
      {
        $this->saveEmbeddedForms($con, $form->getEmbeddedForms());
      }
    }
  }

  /**
   * Updates the default values of the form with the current values of the current object.
   */
  protected function updateDefaultsFromObject()
  {
    // update defaults for the main object
    if ($this->isNew())
    {
      $this->setDefaults(array_merge($this->object->toArray(false), $this->getDefaults()));
    }
    else
    {
      $this->setDefaults(array_merge($this->getDefaults(), $this->object->toArray(false)));
    }

    $defaults = $this->getDefaults();
    foreach ($this->embeddedForms as $name => $form)
    {
      if ($form instanceof sfFormDoctrine)
      {
        $form->updateDefaultsFromObject();
        $defaults[$name] = $form->getDefaults();
      }
    }
    $this->setDefaults($defaults);
  }

  /**
   * Saves the uploaded file for the given field.
   *
   * @param  string $field The field name
   * @param  string $filename The file name of the file to save
   * @param  array  $values An array of values
   *
   * @return string The filename used to save the file
   */
  protected function processUploadedFile($field, $filename = null, $values = null)
  {
    if (!$this->validatorSchema[$field] instanceof sfValidatorFile)
    {
      throw new LogicException(sprintf('You cannot save the current file for field "%s" as the field is not a file.', $field));
    }

    if (is_null($values))
    {
      $values = $this->values;
    }

    if (isset($values[$field.'_delete']) && $values[$field.'_delete'])
    {
      $this->removeFile($field);

      return '';
    }

    if (!$values[$field])
    {
      // this is needed if the form is embedded, in which case 
      // the parent form has already changed the value of the field
      $oldValues = $this->getObject()->getModified(true, false);

      return isset($oldValues[$field]) ? $oldValues[$field] : $this->object->$field;
    }

    // we need the base directory
    if (!$this->validatorSchema[$field]->getOption('path'))
    {
      return $values[$field];
    }

    $this->removeFile($field);

    return $this->saveFile($field, $filename, $values[$field]);
  }

  /**
   * Removes the current file for the field.
   *
   * @param string $field The field name
   */
  protected function removeFile($field)
  {
    if (!$this->validatorSchema[$field] instanceof sfValidatorFile)
    {
      throw new LogicException(sprintf('You cannot remove the current file for field "%s" as the field is not a file.', $field));
    }

    $directory = $this->validatorSchema[$field]->getOption('path');
    if ($directory && is_file($file = $directory.'/'.$this->getObject()->$field))
    {
      unlink($file);
    }
  }

  /**
   * Saves the current file for the field.
   *
   * @param  string          $field    The field name
   * @param  string          $filename The file name of the file to save
   * @param  sfValidatedFile $file     The validated file to save
   *
   * @return string The filename used to save the file
   */
  protected function saveFile($field, $filename = null, sfValidatedFile $file = null)
  {
    if (!$this->validatorSchema[$field] instanceof sfValidatorFile)
    {
      throw new LogicException(sprintf('You cannot save the current file for field "%s" as the field is not a file.', $field));
    }
    if (is_null($file))
    {
      $file = $this->getValue($field);
    }

    $method = sprintf('generate%sFilename', $field);

    if (!is_null($filename))
    {
      return $file->save($filename);
    }
    else if (method_exists($this->object, $method))
    {
      return $file->save($this->object->$method($file));
    }
    else
    {
      return $file->save();
    }
  }

  protected function camelize($text)
  {
    return sfToolkit::pregtr($text, array('#/(.?)#e' => "'::'.strtoupper('\\1')", '/(^|_|-)+(.)/e' => "strtoupper('\\2')"));
  }
}
