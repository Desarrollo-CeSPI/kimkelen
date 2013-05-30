<?php


/**
 * Class mtWidgetFormEmbed
 *
 * author: mtorres
 * email:  torresmat <:at:> gmail <:dot:> com
 *
 * Este widget embebe automáticamente un número dinámico de formularios.
 *
 * Vea un ejemplo de su uso en
 *  $PLUGIN_ROOT/examples/mtWidgetFormEmbed-EXAMPLE
 *
 * Las opciones requeridas de configuración son:
 *   1. 'form_creation_method':
 *        Método estático usado para crear el formulario embebido. Es un arreglo del siguiente tipo:
 *          - array('ClassName', 'MethodName').
 *
 *        Este método es usado cuando:
 *          - El request viene por PUT o POST.
 *          - Se agregá un nuevo formulario a través de un XmlHttpRequest
 *
 *        La firma del método es:
 *
 *          sfForm static public funcName(array $taintedValues, array $extraParams);
 *
 *        Notar que si el primer parámetro $taintedValues no está vacío y el objeeto NO ES NUEVO,
 *        se deberá obtener el objeto de la base de datos y retornar el objeto ya guardado.
 *
 *   2. 'form_creation_method_params':
 *        El segundo parámetro del método 'form_creation_method'.
 *
 *   3. 'edit_form_creation_method':
 *        Método estático usado para crear el formulario embebido. Es un arreglo del tipo:
 *          - array('ClassName', 'MethodName').
 *
 *        Este método es usado para crear el formulario a partir de los objetos del parámetro 'objects'.
 *        Solo es usado si el método del request es 'GET'.
 *
 *        La firma del método debe ser:
 *
 *          sfForm static public function funcName($object)
 *
 *   4. 'child_form_name':
 *        Es el prefijo que recibirán los formularios embebidos como nombre.
 *
 *   5. 'parent_form':
 *        Es el formulario en donde se encuentra este widget.
 *
 *   6. 'objects':
 *        Los objetos de la base de datos para ser embebidos dentro del formulario.
 *        Si no existen objetos, debe de ser un array vacío.
 *
 * Las opciones de configuración no requeridas más importantes son:
 *   1. 'child_form_title_method':
 *        Es el título que se le dará al formulario embebido.
 *        Puede ser un string o el nombre de un método del formulario embebido.
 *
 *        La firma del método antes mencionado es:
 *
 *          String public function getFormTitle($taintedValues = array())
 *
 *   2. 'title':
 *        El título que recibirá el widget.
 *
 * Entonces, para que este widget funcione correctamente se deben implementar 3 métodos:
 *
 *
 *          Retornan los objetos de los formularios embebidos. Notar que si el objeto ya está
 *          guardado en la base de datos, debe obtenerlo de allí y no hacer un 'new ObjectClassName()'
 *
 *            sfForm static public funcName(array $taintedValues, array $extraParams);
 *            sfForm static public function funcName($object)
 *
 *          Retorna el string TITULO del formulario embebido.
 *
 *            String public function getFormTitle($taintedValues = array())
 *
 */
class mtWidgetFormEmbed extends sfWidgetForm
{
  protected $embeddedForms        = array();
  protected $choices              = null;

  public function configure($options = array(), $attributes = array())
  {
    sfContext::getInstance()->getConfiguration()->loadHelpers(array(
      'Asset',
      'Url',
      'Tag',
      'JavascriptBase',
    ));

    parent::configure($options, $attributes);
    /* Options used to create the embedded form */
    $this->addRequiredOption('form_creation_method');
    $this->addRequiredOption('edit_form_creation_method');
    $this->addRequiredOption('child_form_name');
    $this->addOption('child_form_title_method', '');
    $this->addOption('title', '');
    $this->addOption('form_creation_method_params', array());
    $this->addOption('form_formatter', 'table');
    $this->addOption('renderer_class', 'mtWidgetFormEmbedRenderer');
    $this->addOption('stylesheet', '/dcReloadedFormExtraPlugin/css/mtWidgetFormEmbed.css');
    $this->addOption('ajax-loader', image_tag('/dcReloadedFormExtraPlugin/images/ajax-loader-2.gif', array('class' => 'ajax-loader-image'))."&nbsp;Please, wait...");

    /* Options used to update the parent form */
    $this->addRequiredOption('parent_form');

    /* Initial object */
    $this->addRequiredOption('objects');

    /* Format options */
    $this->addOption('toolbar-add', true);
    $this->addOption('toolbar-add-image', '/dcReloadedFormExtraPlugin/images/plus.png');
    $this->addOption('toolbar-add-text', 'add');
    $this->addOption('toolbar-clean', true);
    $this->addOption('toolbar-clean-text', 'clean');
    $this->addOption('toolbar-clean-image', '/dcReloadedFormExtraPlugin/images/clean.png');
    $this->addOption('toolbar-reset', true);
    $this->addOption('toolbar-reset-text', 'reset');
    $this->addOption('toolbar-reset-image', '/dcReloadedFormExtraPlugin/images/update.png');
    $this->addOption('delete-button-image', '/dcReloadedFormExtraPlugin/images/close-action.png');
    $this->addOption('delete-button-text', 'delete');
    $this->addOption('after-add-js', '');
    $this->addOption('after-delete-js', '');
  }

  protected function renderDispatch()
  {
    $class  = $this->getOption('renderer_class');
    $args   = func_get_args();
    $method = array_shift($args);

    return call_user_func_array(array($class, $method), $args);
  }

  public function getChoices($value)
  {
    $values = $this->getValue($value);
    if (count($values) > 0)
    {
      return array_combine($values, $values);
    }
    return array();
  }

  public function getValue($value)
  {
    if (empty($value) || is_null($value) || !is_array($value))
    {
      $value = array();
      for ($i=0;$i<count($this->embeddedForms);$i++)
      {
        $value[$i] = $i;
      }
    }
    return $value;
  }

  public function render($name, $value = null, $attributes = array(), $errors = array())
  {
    $id    = $this->generateId($name);
    $value = !is_array($value)? array() : $value;

    $choiceWidget = new sfWidgetFormSelectMany(array('choices' => $this->getChoices($value), 'is_hidden' => true), array('style' => 'display: none'));
    $choiceHtml   = $choiceWidget->render($name, $this->getValue($value), $attributes, $errors);
    $embeddedForm = $this->renderEmbeddedForms($name);

    $html = $this->renderDispatch('render',
      $this->generateId($name),
      $this->renderDispatch('renderToolbar',
        $name,
        ($this->getOption('toolbar-add')
          ? $this->renderDispatch('renderToolbarButton',
              $this->getAddJsFunction($name),
              $this->getOption('toolbar-add-text'),
              image_path($this->getOption('toolbar-add-image')))
          : '')
        .
        ($this->getOption('toolbar-clean')
          ? $this->renderDispatch('renderToolbarButton',
            $this->getCleanJsFunction($name),
            $this->getOption('toolbar-clean-text'),
            image_path($this->getOption('toolbar-clean-image')))
          : '')
        .
        ($this->getOption('toolbar-reset')
          ? $this->renderDispatch('renderToolbarButton',
            $this->getResetJsFunction($name),
            $this->getOption('toolbar-reset-text'),
            image_path($this->getOption('toolbar-reset-image')))
          : '')
      ),
      $embeddedForm,
      $choiceHtml,
      $this->renderDispatch('renderTitle', $this->getOption('title')),
      $this->getOption('ajax-loader')
    );

    $html .= '<input type="hidden" value="'.htmlentities($embeddedForm, ENT_COMPAT, "UTF-8").'" id="_'.$id.'_original_forms" name="_'.$id.'_original_forms" />';
    $html .= '<input type="hidden" value="'.htmlentities($choiceHtml).'" id="_'.$id.'_original_select" name="_'.$id.'_original_select" />';

    return $html;
  }

  public function getFormTitle($form, $taintedValues = array())
  {
    $method = $this->getOption('child_form_title_method');
    if (!empty($method))
    {
      if (method_exists($form, $method))
      {
        return $form->$method($taintedValues);
      }
      return $method;
    }
    return '';
  }

  protected function getEmbededFormFormatter($form)
  {
    $class     = 'sfWidgetFormSchemaFormatter'.ucfirst($this->getOption('form_formatter'));
    $formatter = new $class($form->getWidgetSchema());
    $form->getWidgetSchema()->setFormFormatterName($this->getOption('form_formatter'));

    return $formatter;
  }

  public function renderEmbeddedForms($name)
  {
    sfContext::getInstance()->getConfiguration()->loadHelpers(array('Asset', 'Url'));

    $parentForm    = $this->getOption('parent_form');
    $taintedValues = $parentForm->getTaintedValues();
    $html = '';

    foreach ($this->embeddedForms as $formName => $form)
    {
      $formClonedWidget   = clone $parentForm->getWidget($formName);
      $formTempWidget     = new sfWidgetFormSchemaDecorator($form->getWidgetSchema(), $this->getEmbededFormFormatter($form)->getDecoratorFormat());
      $formWidgetId       = $formClonedWidget->generateId($formName);
      $formIndex          = $formName[strlen($formName)-1];
      $formTaintedValues  = is_array($taintedValues) && isset($taintedValues[$formName])? $taintedValues[$formName] : array();


      /* Renders form's code */
      $parentForm->setWidget($formName, $formTempWidget);

      $formHtml = $this->renderDispatch('renderForm',
        $this->renderDispatch('renderFormHeader',
          $this->renderDispatch('renderButtonRemoveForm', $this->generateId($name), $formIndex, image_path($this->getOption('delete-button-image')), $this->getOption('delete-button-text'), $this->getOption('after-delete-js')),
          $this->getFormTitle($form, $formTaintedValues)),
        $parentForm[$formName]->render(array()),
        get_javascripts_for_form($form).get_stylesheets_for_form($form)
      );
      $parentForm->setWidget($formName, $formClonedWidget);

      $html .= $formHtml;
    }

    return $html;
  }

  public function getStyleSheets()
  {
    return array_merge(parent::getStyleSheets(), array($this->getOption('stylesheet') => 'all'));
  }

  protected function retrievePreTaintedValues()
  {
    $parentName = str_replace(array(']'), array(''), $this->getOption('parent_form')->getName());

    $preTaintedValues = array();
    $parentNames = explode('[', $parentName);

    if (strlen($parentName) > 0 && count($parentNames) > 0)
    {
      $preTaintedValues = sfContext::getInstance()->getRequest()->getParameter(array_shift($parentNames));
      foreach ($parentNames as $name)
      {
        $preTaintedValues = isset($preTaintedValues[$name])? $preTaintedValues[$name] : array();
      }
    }

    return $preTaintedValues;
  }

  public function embedForms($widgetName)
  {
    if (in_array(sfContext::getInstance()->getRequest()->getMethod(), array(sfRequest::POST, sfRequest::PUT)))
    {
      $this->embedFormsFromRequest($widgetName);
    }
    else
    {
      $this->embedFormsFromObject($widgetName);
    }
  }

  public function embedFormsFromObject($widgetName)
  {
    $count = 0;
    foreach ($this->getOption('objects') as $o)
    {
      $form = call_user_func($this->getOption('edit_form_creation_method'), $o);
      $newFormName = $this->getOption('child_form_name').'_'.$count;
      $form->getWidgetSchema()->setNameFormat($this->getOption('parent_form')->getName().'['.$newFormName.'][%s]');
      $this->embedForm($form, $newFormName);
      $count++;
    }
    $this->choices = range(0, $count);
  }

  public function embedFormsFromRequest($widgetName)
  {
    $preTaintedValues = $this->retrievePreTaintedValues();
    $widgetName       = $this->generateId($widgetName);

    if (is_array($preTaintedValues) && isset($preTaintedValues[$widgetName]) && count($preTaintedValues[$widgetName]) > 0)
    {
      foreach ($preTaintedValues[$widgetName] as $formName)
      {
        $newFormName = $this->getOption('child_form_name').'_'.$formName;
        $form = call_user_func($this->getOption('form_creation_method'), $preTaintedValues[$newFormName], $this->getOption('form_creation_method_params'));
        $form->getWidgetSchema()->setNameFormat($this->getOption('parent_form')->getName().'['.$newFormName.'][%s]');
        $this->embedForm($form, $newFormName);
      }
    }
  }

  protected function embedForm($form, $formName)
  {
    $parentForm = $this->getOption('parent_form');

    $parentForm->embedForm($formName, $form, '');
    $parentForm->getWidget($formName)->setOption('is_hidden', true);
    unset($form['_csrf_token']);

    $this->embeddedForms[$formName] = $form;
  }

  public function getCleanJsFunction($name)
  {
    $id = $this->generateId($name);
    return "jQuery('#wrapper_$id div.mtWidgetFormEmbedWorkspace').html(''); jQuery('#$id').html('');";
  }

  public function getResetJsFunction($name)
  {
    $id = $this->generateId($name);
    return "jQuery('#wrapper_$id div.mtWidgetFormEmbedWorkspace').html(jQuery('#_".$id."_original_forms').val()); jQuery('#wrapper_$id div.mtWidgetFormEmbedSelectTag').html(jQuery('#_".$id."_original_select').val())";
  }

  public function getAddJsFunction($name)
  {
    $id = $this->generateId($name);
    sfContext::getInstance()->getConfiguration()->loadHelpers(array('Url'));

    $images = array(
      'delete-button-image' => array('image' => $this->getOption('delete-button-image'), 'text' => $this->getOption('delete-button-text'))
    );

    $afterDeleteJs = $this->getOption('after-delete-js');
    $afterAddJs    = $this->getOption('after-add-js');

    return "
      var count = 0;
      if (jQuery('#$id').find('option').length > 0)
      {
        count = parseInt(jQuery('#$id').find('option').last().attr('value')) + 1;
      }
      jQuery.ajax({ async: false,
                          type: 'POST',
                          complete: function (xhr, textStatus) {
                            jQuery('#wrapper_$id .mtWidgetFormEmbedWorkspace').append(xhr.responseText);
                            jQuery('#$id').append('<option selected=selected value='+count+'>'+count+'</option>');
                            jQuery('#wrapper_$id .mtWidgetFormEmbedAjaxLoader').hide();
                            {$afterAddJs}
                          },
                          beforeSend: function(jqXhr, settings)
                          {
                            jQuery('#wrapper_$id .mtWidgetFormEmbedAjaxLoader').show();
                          },
                          data: {
                                  'form_creation_method' : '".self::encode($this->getOption('form_creation_method'))."',
                                  'form_creation_method_params' : '".self::encode($this->getOption('form_creation_method_params'))."',
                                  'parent_form_name' : '".self::encode($this->getOption('parent_form')->getName())."',
                                  'child_form_name' : '".self::encode($this->getOption('child_form_name'))."',
                                  'title_method' : '".self::encode($this->getOption('child_form_title_method'))."',
                                  'child_count' : count,
                                  'widget_id' : '".self::encode($id)."',
                                  'images' : '".self::encode($images)."',
                                  'renderer_class' : '".$this->getOption('renderer_class')."',
                                  'form_formatter' : '".self::encode($this->getOption('form_formatter'))."',
                                  'after_delete_js' : '".self::encode($afterDeleteJs)."'
                                },
                          url: '".url_for('dc_ajax/mtWidgetFormEmbedAdd')."'
                        });";

  }

  static public function decode($string)
  {
    return unserialize(base64_decode($string));
  }

  static public function encode($object)
  {
    return base64_encode(serialize($object));
  }

  public function getEmbeddedForms()
  {
    return $this->embeddedForms;
  }
}
