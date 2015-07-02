<?php

/**
 * ajax actions.
 *
 * @package    testing
 * @subpackage ajax
 * @author     Your name here
 * @version    SVN: $Id: actions.class.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
class dc_ajaxActions extends sfActions
{
  public function executeGetPropelChoices(sfWebRequest $request)
  {
    $model = $request->getParameter("model");
    $objects = call_user_func(array($model."Peer", "doSelect"), new Criteria());

    $choices = array();
    foreach ($objects as $object)
    {
      $choices[$object->getId()] = $object->__toString();
    }

    return $this->renderText(json_encode($choices));
  }

  public function executeGetDoctrineChoices(sfWebRequest $request)
  {
    $model = $request->getParameter("model");
    $objects = Doctrine_Core::getTable($model)->findAll();

    $choices = array();
    foreach ($objects as $object)
    {
      $choices[$object->getId()] = $object->__toString();
    }

    return $this->renderText(json_encode($choices));
  }

  public function executeDcWidgetFormAjaxDependenceChanged(sfWebRequest $request)
  {
    if ($request->isMethod('post'))
    {
      sfContext::getInstance()->getConfiguration()->loadHelpers(array('Asset','Tag','JavascriptBase','Url'));

      $id = $request->getParameter('id');
      $observed_value = $request->getParameter('observed_value');
      $this->widget = unserialize(base64_decode($request->getParameter('widget')));
      $this->getResponse()->setContent($this->widget->ajaxRender($observed_value));
    }

    return sfView::NONE;
  }

  public function executeDcWidgetFormActivator(sfWebRequest $request)
  {
    if ($request->isMethod('post'))
    {
      $widget_from_request = $request->getParameter('widget');
      $widget_array    = dcWidgetFormActivator::decodeWidget($widget_from_request);
      $observed_values = $request->getParameter('observed_values');
      $this->getResponse()->setContent(call_user_func($widget_array['render_after_method'], $observed_values, $widget_array));
    }

    return sfView::NONE;
  }

  public function executeDcWidgetFormJQueryDependenceChanged(sfWebRequest $request)
  {
    if ($request->isMethod('post'))
    {
      $widget_from_request = $request->getParameter('widget');
      $widget = dcWidgetFormJQueryDependence::decodeWidget($widget_from_request);
      $observed_values = $request->getParameter('observed_values');
      $this->getResponse()->setContent($widget->renderAfterUpdate($observed_values));
    }

    return sfView::NONE;
  }

  public function executePmWidgetFormPropelJQuerySearch(sfWebRequest $request)
  {
    $this->search = $request->getParameter("search");
    $this->js_var_name = $request->getParameter("js_var_name");

    $this->page = $request->getParameter("page");
    $this->previous_page = $this->page - 1;
    $this->next_page = $this->page + 1;

    $this->options = unserialize(base64_decode($request->getParameter("serialized_options")));

    $results = array();

    $class = constant($this->options['model'].'::PEER');

    $criteria = null === $this->options['criteria'] ? new Criteria() : clone $this->options['criteria'];

    $columns = $this->options['column']; // column is an array or a string

     foreach (explode(' ', $this->search) as $key => $each_search)
    {

      if (is_array($columns))
      {

        for ($i = 0; $i < count($columns); $i++)
        {
          $column = strtoupper($columns[$i]);
          if ($i == 0 && $key == 0)
          {
            $criterion = $criteria->getNewCriterion(constant("$class::$column"), "%" . $each_search . "%", Criteria::LIKE);
          }
          else
          {
            $criterion->addOr($criteria->getNewCriterion(constant("$class::$column"), "%" . $each_search . "%", Criteria::LIKE));
          }
        }

        $criteria->add($criterion);
      }
      else
      {
        $column = strtoupper($columns);
        $criteria->add(constant("$class::$column"), "%" . $each_search . "%", Criteria::LIKE);
      }
    }
    if ($order = $this->options['order_by'])
    {
      $method = sprintf('add%sOrderByColumn', 0 === strpos(strtoupper($order[1]), 'ASC') ? 'Ascending' : 'Descending');
      $criteria->$method(call_user_func(array($class, 'translateFieldName'), sfInflector::camelize($order[0]), BasePeer::TYPE_PHPNAME, BasePeer::TYPE_COLNAME));
    }

    $this->total_objects = call_user_func(array($class, 'doCount'), $criteria, $this->options['connection']);

    if (isset($this->options['limit']))
    {
      $this->limit = $this->options["limit"];
      $criteria->setLimit($this->limit);
      $criteria->setOffset($this->page * $this->limit);
    }

    $this->objects = call_user_func(array($class, $this->options['peer_method']), $criteria, $this->options['connection']);

    $this->methodKey = $this->options['key_method'];
    if (!method_exists($this->options['model'], $this->methodKey))
    {
      throw new RuntimeException(sprintf('Class "%s" must implement a "%s" method to be rendered in a "%s" widget', $this->options['model'], $this->methodKey, "pmWidgetFormPropelJQuerySearch"));
    }

    $this->methodValue = $this->options['method'];
    if (!method_exists($this->options['model'], $this->methodValue))
    {
      throw new RuntimeException(sprintf('Class "%s" must implement a "%s" method to be rendered in a "%s" widget', $this->options['model'], $this->methodValue, "pmWidgetFormPropelJQuerySearch"));
    }

    return $this->renderPartial("dc_ajax/pmWidgetFormPropelJQuerySearch");
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
    $this->afterDeleteJs      = mtWidgetFormEmbed::decode($request->getParameter('after_delete_js'));
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

  public function executePmWidgetFormPropelInputByCode(sfWebRequest $request)
  {
    sfContext::getInstance()->getConfiguration()->loadHelpers(array('I18N'));

    $code = $request->getParameter('code');

    $widget_options = unserialize($request->getParameter('serialized_widget_options'));

    $model = $widget_options['model'];
    $column = $widget_options['column'];
    $criteria = $widget_options['criteria'];
    $method = $widget_options['method'];
    $peer_method = $widget_options['peer_method'];
    $object_not_found_text = $widget_options['object_not_found_text'];
    $object_not_found_text = __($object_not_found_text);

    if (is_null($criteria))
    {
      $criteria = new Criteria();
    }

    $criteria->add(constant($model.'Peer::'.strtoupper($column)), $code);

    $object = call_user_func(array($model.'Peer', $peer_method), $criteria);

    $text = !is_null($object) ? $object->$method() : $object_not_found_text;

    return $this->renderText("<span class=\"label ".(is_null($object) ? "not-" : "")."found\">$text</span>");
  }

  public function executePmWidgetFormPropelJQueryTokeninput(sfWebRequest $request)
  {
    sfContext::getInstance()->getConfiguration()->loadHelpers(array('I18N'));

    $q = $request->getParameter('q');

    $widget_options = unserialize(base64_decode($request->getParameter('serialized_widget_options')));

    $model = $widget_options['model'];
    $column = $widget_options['column'];
    $criteria = $widget_options['criteria'];
    $method = $widget_options['method'];
    $peer_method = $widget_options['peer_method'];
    $key_method = $widget_options['key_method'];

    if (is_null($criteria))
    {
      $criteria = new Criteria();
    }

    $criteria->add(constant($model.'Peer::'.strtoupper($column)), "%$q%", Criteria::LIKE);

    $objects = call_user_func(array($model.'Peer', $peer_method), $criteria);

    $results = array();
    foreach ($objects as $object)
    {
      $results[] = array(
        'id' => $object->$key_method(),
        'name' => strval($object)
      );
    }

    return $this->renderText(json_encode($results));
  }

 /*************************************************************************************************
  * crJsTree related actions and functions
  ************************************************************************************************/
 /**
  * Validates security token
  *
  * @return boolean
  */
  private function validateCrJsTreePropelSecurityToken(sfWebRequest $request ) {
    $security_token         = $this->decodeCrJsTreePropel( $request->getParameter('security_token'));
    $operation              = $this->decodeCrJsTreePropel( $request->getParameter('operation'));
    $peer_class             = $this->decodeCrJsTreePropel( $request->getParameter('peer_class'));
    $peer_parent_id_column  = $this->decodeCrJsTreePropel( $request->getParameter('peer_parent_id_column'));
    $peer_id_column         = $this->decodeCrJsTreePropel( $request->getParameter('peer_id_column'));
    $criteria_serialized    = $this->decodeCrJsTreePropel( $request->getParameter('criteria'));
    $peer_method            = $this->decodeCrJsTreePropel( $request->getParameter('peer_method'));
    $peer_count_method      = $this->decodeCrJsTreePropel( $request->getParameter('peer_count_method'));
    $peer_to_string_method  = $this->decodeCrJsTreePropel( $request->getParameter('peer_to_string_method'));
    return crWidgetFormJsTreeAjaxPropel::getSecurityToken($peer_class, $peer_parent_id_column, $peer_id_column, $criteria_serialized, $peer_method, $peer_count_method, $peer_to_string_method) == $security_token;
  }

 /**
  * decode crJsTreePropel data sent by widget
  *
  * @return string
  */
  private function decodeCrJsTreePropel($value) {
    return base64_decode($value);
  }

 /**
  * decode crJsTreePropelMerge data sent by widget
  *
  * @return string
  */
  private function decodeCrJsTreePropelMerge($value) {
    return unserialize(base64_decode($value));
  }

 /**
  * Prepare CrJsTreePropelNodes adding fields as expected by jsTree:
  *   array(
  *     'data'  => 'string name of node',
  *     'attr'  => array( 'id' => node_id ),
  *     'state' => 'closed' //only if has children
  *   );
  * Data is returned as array so it can be converted to json using json_encode
  *
  * @return array
  */
  private function prepareCrJsTreePropelNodes(
    array $array,
      $peer_class,
      $peer_parent_id_column,
      $peer_id_column,
      $criteria,
      $peer_method,
      $peer_count_method,
      $peer_to_string_method,
      $related_by_column = null,
      $get_type_callback = null
    ) {
    $ret=array();
    $tableMap = call_user_func(array($peer_class, 'getTableMap'));
    foreach ($array as $o)
    {
        $getId = 'get' . $tableMap->getColumn($peer_id_column)->getPhpName();
        $new = array( "data" => call_user_func(array($o,$peer_to_string_method)) , 'attr'=>array('id'=> $o->$getId()));
        $c = clone $criteria;
        $c->addAnd(constant($peer_class.'::'.$peer_parent_id_column), $o->$getId());
        if ( call_user_func( array( $peer_class, $peer_count_method), $c)  > 0 )
        {
          $new ['state']= 'closed';
        }
        if ( $get_type_callback != null) $new['attr']['rel']= call_user_func(array($o,$get_type_callback));
//        $new ['attr']['debug']= var_export(constant($peer_class.'::'.$peer_parent_id_column).'|'.$related_by_column,true);
        $ret[]=$new;
    }
    return $ret;
  }

 /**
  * Return one level of the tree from received $parent_id node
  *
  * @param int $parent_id                   id of parent selected node. This id was clicked and we have to return its children
  * @param string $peer_class               Peer class to use. For example: PersonPeer
  * @param string $peer_parent_id_column    Column of $peer_class that relates with parent node
  * @param string $peer_id_column           ID column of $peer_class
  * @param Criteria $criteria               Criteria object use for retrieving nodes (sorting, prefiltering, etc)
  * @param string $peer_method              Peer select method. Defaults to doSelect
  * @param string $peer_count_method        Peer count method. Defaults to doCount
  * @param string $peer_to_string_method    Peer instance toString method. Defaults to __toString
  * @param string $related_by_column        In case of merging node types (@see crWidgetFormJsTreeAjaxPropelMerge), this is the column to
  *                                         be used to select nodes depending of other nodes
  * @return array
  */
  private function getCrJsTreePropelAsOneLevelHierarchy(
      $parent_id,
      $peer_class,
      $peer_parent_id_column,
      $peer_id_column,
      $criteria,
      $root_nodes_criteria,
      $peer_method,
      $peer_count_method,
      $peer_to_string_method,
      $related_by_column = null,
      $get_type_callback = null
      ) {

    $c= clone $criteria;
    if ( $parent_id == null) {
      if (!is_null($root_nodes_criteria))
      {
        $c = clone $root_nodes_criteria;
      }
      else
      {
        $c->addAnd(constant($peer_class.'::'.$peer_parent_id_column), null, Criteria::ISNULL);
      }
    }
    else {
      $c->add(constant($peer_class.'::'.($related_by_column == null ?$peer_parent_id_column: $related_by_column)), $parent_id);
    }

    return $this->prepareCrJsTreePropelNodes(
      call_user_func( array( $peer_class, $peer_method), $c),
      $peer_class,
      $peer_parent_id_column,
      $peer_id_column,
      $criteria,
      $peer_method,
      $peer_count_method,
      $peer_to_string_method,
      $related_by_column,
      $get_type_callback
    );
  }

  /* XHR action to retrieve nodes for crJsTreePropel widget */
  public function executeCrJsTreePropel(sfWebRequest $request) {

    if ($request->isMethod('post') ) {

      /* Is a valid request or it was modified by hand? */
      if ( $this->validateCrJsTreePropelSecurityToken( $request)) {

        $security_token         = $this->decodeCrJsTreePropel( $request->getParameter('security_token'));
        $peer_class             = $this->decodeCrJsTreePropel( $request->getParameter('peer_class'));
        $peer_parent_id_column  = $this->decodeCrJsTreePropel( $request->getParameter('peer_parent_id_column'));
        $peer_id_column         = $this->decodeCrJsTreePropel( $request->getParameter('peer_id_column'));
        $criteria               = unserialize($this->decodeCrJsTreePropel( $request->getParameter('criteria')));
        $root_nodes_criteria    = unserialize($this->decodeCrJsTreePropel( $request->getParameter('root_nodes_criteria')));
        $peer_method            = $this->decodeCrJsTreePropel( $request->getParameter('peer_method'));
        $peer_count_method      = $this->decodeCrJsTreePropel( $request->getParameter('peer_count_method'));
        $peer_to_string_method  = $this->decodeCrJsTreePropel( $request->getParameter('peer_to_string_method'));
        $operation              = $request->getParameter('operation');
        $get_type_callback      = unserialize( $this->decodeCrJsTreePropel( $request->getParameter('get_type_callback')));

        switch ( $operation ) {
          case 'get_children':
            $id = $request->getParameter('node_id');
            break;
          case 'get_root':
          default:
            $id = null;
            break;
        }
        $nodes = $this->getCrJsTreePropelAsOneLevelHierarchy($id, $peer_class, $peer_parent_id_column, $peer_id_column, $criteria, $root_nodes_criteria, $peer_method, $peer_count_method, $peer_to_string_method, null, $get_type_callback);

        return $this->renderText( json_encode($nodes));
      }
      else {
        return $this->renderText( json_encode(array(array('data'=>'CrJsTreePropelSecurityToken violation', 'attr'=>array('id'=>-100)))));
      }
    }
    return sfView::NONE;
  }

 /**
  * Return one level of the tree from received $parent_id node considering possible merging of
  * different nodes
  *
  * @return array
  */
  private function getCrJsTreePropelMergeAsOneLevelHierarchy(
      $parent_id,
      $node_type,
      $peer_class,
      $peer_parent_id_column,
      $peer_id_column,
      $criteria,
      $root_nodes_criteria,
      $peer_method,
      $peer_count_method,
      $peer_to_string_method,
      $peer_types,
      $peer_root_type,
      $peer_type_relationships) {

    if ($parent_id == null )
    {
    /* in this case we have to get only root_nodes of type specified by $peer_root_type */
        $nodes = $this->getCrJsTreePropelAsOneLevelHierarchy($parent_id, $peer_class[$peer_root_type], $peer_parent_id_column[$peer_root_type], $peer_id_column[$peer_root_type], $criteria[$peer_root_type], $root_nodes_criteria, $peer_method[$peer_root_type], $peer_count_method[$peer_root_type], $peer_to_string_method[$peer_root_type]);
        foreach ($peer_type_relationships[$peer_root_type] as $name => $relationship)
        {
            $node_type = $relationship['related_type'];
            $nodes = $this->updateCrJsTreeMergeChildrenNodesWithRelated($nodes, $peer_class[$node_type], $relationship['by_column'], $criteria[$node_type], $peer_count_method[$node_type]);
        }
        return array_map(  create_function('$node','$node["attr"]["rel"]="'.$peer_root_type.'"; return $node;'), $nodes);
    }
    else
    {
    /* if is not a root node, we have to: first get nodes of same type of calling node */
        $nodes = $this->getCrJsTreePropelAsOneLevelHierarchy($parent_id, $peer_class[$node_type], $peer_parent_id_column[$node_type], $peer_id_column[$node_type], $criteria[$node_type], $root_nodes_criteria, $peer_method[$node_type],  $peer_count_method[$node_type], $peer_to_string_method[$node_type]);

        $nodes = array_map(  create_function('$node','$node["attr"]["rel"]="'.$node_type.'"; return $node;'), $nodes);

    /* Now we will check if there is a possible relation to get child nodes of different type */
        if ( array_key_exists ($node_type, $peer_type_relationships) ) {

          foreach ($peer_type_relationships[$node_type] as $name => $relationship) {
            $node_type = $relationship['related_type'];
            $nodes = $this->updateCrJsTreeMergeChildrenNodesWithRelated($nodes, $peer_class[$node_type], $relationship['by_column'], $criteria[$node_type], $peer_count_method[$node_type]);
            $related_root_criteria = clone $criteria[$node_type]; // related child nodes are root nodes the first time. Thats why we need to ask for NULL parent_ids
            $related_root_criteria->addAnd(constant($peer_class[$node_type].'::'.$peer_parent_id_column[$node_type]), null, Criteria::ISNULL);

            $related_nodes = $this->getCrJsTreePropelAsOneLevelHierarchy($parent_id, $peer_class[$node_type], $peer_parent_id_column[$node_type], $peer_id_column[$node_type],  $related_root_criteria, null, $peer_method[$node_type], $peer_count_method[$node_type], $peer_to_string_method[$node_type], $relationship['by_column']);
            //The following line fixes the count method for child nodes
            $related_nodes = $this->updateCrJsTreeMergeChildrenNodesWithRelated($related_nodes, $peer_class[$node_type], $peer_parent_id_column[$node_type], $criteria[$node_type], $peer_count_method[$node_type]);
            $nodes = array_merge ($nodes, array_map(  create_function('$node','$node["attr"]["rel"]="'.$node_type.'"; return $node;'), $related_nodes));
          }
        }
        return $nodes;
    }
  }

  private function updateCrJsTreeMergeChildrenNodesWithRelated($nodes, $peer_class, $peer_related_column, $criteria, $peer_count_method)
  {
    $ret = array();
    foreach ($nodes as $node)
    {
      if ( !isset ($node['state']) )
      {
        $related_id = $node['attr']['id'];
        $c = $criteria == null? new Criteria(): $criteria;
        $c->add(constant($peer_class.'::'.$peer_related_column), $related_id);
        if ( call_user_func(array($peer_class, $peer_count_method), $c) > 0 )
        {
          $node['state']='closed';
        }
      }
      $ret[]=$node;
    }
    return $ret;
  }
  /* XHR action to retrieve nodes for crJsTreePropelMerge widget */
  public function executeCrJsTreePropelMerge(sfWebRequest $request) {

    if ($request->isMethod('post') ) {

      /* Is a valid request or it was modified by hand? */
      if ( $this->validateCrJsTreePropelSecurityToken( $request)) {
        $peer_class             = $this->decodeCrJsTreePropelMerge( $request->getParameter('peer_class'));
        $peer_parent_id_column  = $this->decodeCrJsTreePropelMerge( $request->getParameter('peer_parent_id_column'));
        $peer_id_column         = $this->decodeCrJsTreePropelMerge( $request->getParameter('peer_id_column'));
        $criteria               = $this->decodeCrJsTreePropelMerge( $request->getParameter('criteria'));
        $peer_method            = $this->decodeCrJsTreePropelMerge( $request->getParameter('peer_method'));
        $peer_count_method      = $this->decodeCrJsTreePropelMerge( $request->getParameter('peer_count_method'));
        $peer_to_string_method  = $this->decodeCrJsTreePropelMerge( $request->getParameter('peer_to_string_method'));
        $peer_types             = $this->decodeCrJsTreePropelMerge( $request->getParameter('peer_types'));
        $peer_root_type         = $this->decodeCrJsTreePropelMerge( $request->getParameter('peer_root_type'));
        $peer_type_relationships= $this->decodeCrJsTreePropelMerge( $request->getParameter('peer_type_relationships'));
        $root_nodes_criteria    = unserialize($this->decodeCrJsTreePropel( $request->getParameter('root_nodes_criteria')));
        $operation              = $request->getParameter('operation');

        switch ( $operation ) {
          case 'get_children':
            $id = $request->getParameter('node_id');
            $node_type = $request->getParameter('node_type');
            break;
          case 'get_root':
          default:
            $id = null;
            $node_type = null;
            break;
        }
        $nodes = $this->getCrJsTreePropelMergeAsOneLevelHierarchy($id,$node_type, $peer_class, $peer_parent_id_column, $peer_id_column, $criteria, $root_nodes_criteria, $peer_method, $peer_count_method, $peer_to_string_method, $peer_types, $peer_root_type, $peer_type_relationships);
        return $this->renderText( json_encode($nodes));
      }
      else {
        return $this->renderText( json_encode(array(array('data'=>'CrJsTreePropelSecurityToken violation', 'attr'=>array('id'=>-100)))));
      }
    }
    return sfView::NONE;
  }
 /*************************************************************************************************
  * crJsTree related actions and functions
  ************************************************************************************************/

  public function executeCrSelectableWidget(sfWebRequest $request) {
    if ($request->isMethod('post') ) {
      $widget = base64_decode($request->getParameter('widget'));
      return $this->renderText($widget);
    }
    return sfView::NONE;
  }

}
