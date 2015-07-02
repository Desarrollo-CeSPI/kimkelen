<?php

class dcFormExtraPluginActions extends sfActions
{
  public function executeMtWidgetFormAdderRefresh()
  {
    $peer_class  = $this->getRequestParameter('peer_class');
    $peer_method = $this->getRequestParameter('peer_method');
    $peer_params = $this->getRequestParameter('peer_params', array());

    $this->add_empty    = $this->getRequestParameter('add_empty');
    $this->value_method = $this->getRequestParameter('value_method', '__toString');
    $this->key_method   = $this->getRequestParameter('key_method', 'getPrimaryKey');
    $this->selected     = $this->getRequestParameter('selected', null);
    $this->options      = array();

    if (class_exists($peer_class) && method_exists($peer_class, $peer_method))
    {
      $this->options = call_user_func_array($peer_class.'::'.$peer_method, $peer_params);
    }
  }

  public function executeDcWidgetAjaxDependenceChanged (sfWebRequest $request)
  {
    if ($request->isMethod('post'))
    {
      sfContext::getInstance()->getConfiguration()->loadHelpers(array('Asset','Tag','JavascriptBase','Url', 'Javascript'));
      $id=$request->getParameter('id');
      $observed_value=$request->getParameter('observed_value');
      $this->widget=unserialize(base64_decode($request->getParameter('widget')));
      $this->getResponse()->setContent($this->widget->ajaxRender($observed_value));
    }
    return sfView::NONE;
  }

  public function executeDcWidgetFormSelectDoubleListFinderPropel(sfWebRequest $request)
  {
    $widget =  unserialize(base64_decode($request->getParameter('widget')));
    $values = unserialize(base64_decode($request->getParameter('values')));
    $values = is_null($values)?array():$values;
    $size = $request->getParameter('size');
    $letter = $request->getParameter('letter');

    $name = $request->getParameter('name');
    $method = $widget->getOption('method');
    $key_method = $widget->getOption('key_method');
    $column = strtoupper($widget->getOption('column'));
    $model = $widget->getOption('model').'Peer';
    $peer_method = $widget->getOption('peer_method');
    $connection = $widget->getOption('connection');

    if ( $request->hasParameter('custom_handler') )
    {
      $handler = unserialize(base64_decode($request->getParameter('custom_handler')));
      @call_user_func($handler, $widget, $values);
    }
    $c = $widget->getOption('criteria');
    $c->add(constant($model.'::'.$column), $letter . '%', Criteria::LIKE);
    $choices = array();
    foreach ( call_user_func(array($model,$peer_method),$c, $connection) as $choice)
    {
      if (!in_array($choice->getPrimaryKey(), $values))
        $choices[$choice->$key_method()] = $choice->$method();
    }
    $widget = new sfWidgetFormSelect(array('multiple' => true, 'choices' => $choices), array('size' => $size, 'class' => $widget->getOption('class_select')));

    return $this->renderText($widget->render($name));
  }


  public function executeMtWidgetFormEmbedAdd(sfWebRequest $request)
  {
    $parentFormName           = mtWidgetFormEmbed::decode($request->getParameter('parent_form_name'));
    $childFormName            = mtWidgetFormEmbed::decode($request->getParameter('child_form_name'));
    $formCreationMethod       = mtWidgetFormEmbed::decode($request->getParameter('form_creation_method'));
    $formCreationMethodParams = mtWidgetFormEmbed::decode($request->getParameter('form_creation_method_params'));
    $childFormTitleMethod     = mtWidgetFormEmbed::decode($request->getParameter('title_method'));
    $this->widgetId           = mtWidgetFormEmbed::decode($request->getParameter('widget_id'));
    $this->formFormatter      = mtWidgetFormEmbed::decode($request->getParameter('form_formatter'));
    $this->rendererClass      = $request->getParameter('renderer_class');
    $this->images             = mtWidgetFormEmbed::decode($request->getParameter('images'));
    $this->childCount         = $request->getParameter('child_count');

    if (!empty($childFormTitleMethod))
    {
      if (is_string($childFormTitleMethod))
      {
        $this->title = $childFormTitleMethod;
      }
      elseif (is_array($childFormTitleMethod))
      {
        $this->title = call_user_func($childFormTitleMethod);
      }
    }

    $this->form = call_user_func($formCreationMethod, $formCreationMethodParams);
    $this->form->getWidgetSchema()->setNameFormat("$parentFormName"."[".$childFormName."_".$this->childCount."][%s]");
    $this->form->getWidgetSchema()->setFormFormatterName($this->formFormatter);
    $this->formTitle = $this->getFormTitle($this->form, $childFormTitleMethod);

    unset($this->form['_csrf_token']);
  }

  protected function getFormTitle($form, $childFormTitleMethod)
  {
    $method = $childFormTitleMethod;
    if (!empty($method))
    {
      if (method_exists($form, $method))
      {
        return $form->$method();
        return call_user_func(array($form, $method));
      }
      return $method;
    }
    return '';
  }
}

?>
