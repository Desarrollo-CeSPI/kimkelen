<?php
 /**  
  * crWidgetFormJsTree is a tree select widget. It is coded using jstree js library.
  * More information at library homesite: http://jstree.com
  *
  * This widget needs some JavaScript to work. So, you need to include the JavaScripts
  * files returned by the getJavaScripts() method. 
  *
  *
  * @author Christian A. Rodriguez <car at cespi.unlp.edu.ar>
  */
class crWidgetFormJsTree extends sfWidgetForm {
  /**
   * Constructor options
   *
   * Required options:
   *  * tree: Array representing a tree. It will be converted to Json with json_encode. Each array 
   *    element must have the following properties:
   *      data: display name of node
   *      attr: array of attributes: 
   *        id:  Most important attribute. Represents identification for this node
   *        rel: Type of node. This is not required. You can change icon for different types. See
   *             tree_node_icons_per_type option
   *      children: optional. It is an array of nodes like this one specified
   *    Sample tree array:
   *    array(
   *      array(
   *        "data" => "Node 01",
   *        "attr" => array( "id" => 1, "rel" => "folder"),
   *        "children" => array(
   *          array ( 
   *            "data" => "Child 01",
   *            "attr" => array( "id" => 2 ),
   *          ),
   *        ),
   *      ),
   *      array(
   *        "data" => "Node 02",
   *        "attr" => array( "id" => 3, ),
   *      ),
   *      )
   *
   * Available options:
   *  * clear_selection_icon: text/icon to clear current selection
   *  * no_value_text: Text to show when no value is selected
   *  * tree_options: jstree core options.  See http://www.jstree.com/documentation/core
   *  * tree_plugins: Should not be changed. It is an option for subclases
   *  * tree_show_icons: Determines if tree node icons are visible
   *  * tree_show_dots: Determines if lines between tree nodes are visible
   *  * tree_node_icons_per_type: Array of types with custom icons. For example:
   *      array( 
   *        'default' => '/images/mi_icon.png',
   *        'folder' => '/images/mi_folder_icon.png'
   *      )
   *      default type is applied to every node that does not specify type
   *      Type is specified in json data, as part of attr object, as rel property. For example:
   *  * not_selectable_types: array of types that will not be allowed to be selected
   *  * prefix_tree_node_id: name used to generate node ids in DOM
   *     
   *
   * @param array $options     An array of options
   * @param array $attributes  An array of default HTML attributes
   *
   * @see sfWidgetForm
   */
  protected function configure($options = array(), $attributes = array()) {
    sfContext::getInstance()->getConfiguration()->loadHelpers(array('JavascriptBase'));

    $this->addRequiredOption('tree');
    $this->addOption('clear_selection_icon',  image_tag('/sfPropelPlugin/images/delete.png', array('alt'=>'delete', 'title' => 'delete'))); 
    $this->addOption('no_value_text','Please select a value');
    $this->addOption('tree_options', new stdClass());
    $this->addOption('tree_plugins', '[ "themes", "ui", "json_data", "types" ]');
    $this->addOption('tree_show_icons', true); 
    $this->addOption('tree_show_dots',  true); 
    $this->addOption('tree_node_icons_per_type',  array());
    $this->addOption('not_selectable_types',  array());
    $this->addOption('prefix_tree_node_id', 'change_me');
    $this->addOption('show_value_callback', false);
    $this->addOption('theme', 'classic');
  }

  /**
   * Merge javascripts for this widget with those scripts needed by widget option
   *
   * @return array
   */
  public function getJavaScripts() {
    return array_merge(
            parent::getJavaScripts(),
            array('/dcReloadedFormExtraPlugin/js/jstree/jquery.jstree.js'));
  }

  
  /**
  * Return options for types: which icon will be display for each type and if setted, which
  * types are selectable 
  *
  * @return string
  */
  protected function getJsTreeTypeOptions() {
    $asoc = $this->getOption('tree_node_icons_per_type');
    $not_selectable_types = $this->getOption('not_selectable_types');
    $ret = '';
    if ( empty($asoc) )
    {
      $asoc=array();
      foreach($not_selectable_types as $v) $asoc[$v]=null;
    }
    foreach ($asoc as $type=>$icon_img) {
      $ret.= (empty($ret)?'':', ') . sprintf (" %s: { icon: { image: '%s' } ", $type , $icon_img);
      if ( in_array($type, $not_selectable_types)) {
        $ret.= ", hover_node: false, select_node : function () { return false; }";
      }
      $ret.="}";
    }
    return sprintf("{%s}", $ret);
  }

 /**
  * Returns a string representation of a node id. Because node ids can repeat, we need a way
  * of make an effort of generate unique ids
  *
  * @return string
  */
  protected function generateNodeId($node)
  {
    $id = is_array($node)? ($node['attr']['id']) :$node;
    if (empty($id) ) return null;
    return sprintf("%s_%s_%s",
      $this->getPrefix(), 
      $this->getOption('prefix_tree_node_id'), 
      $id);
  }

 /**
  * Format a tree array in a proper format supported by jstree
  *
  * @return array
  */
  protected function getTree( $tree=null ) {
    if ( $is_root = ($tree == null)) {
      $tree = $this->getOption('tree');
    }
    $ret =array();
    foreach ($tree as $node) {
      $node['metadata'] =  array ('id' => $node['attr']['id']);
      $node['attr']['id']=$this->generateNodeId($node);
      if ( array_key_exists('children', $node)) {
        $node['children']=$this->getTree($node['children']);
      }
      $ret[]=$node;
    }
    return $is_root? array('data'=>$ret): $ret;
  }

 /**
  * json_encode wrapper 
  *
  * @return string
  */
  protected function toJson($data)
  {
    return json_encode($data);
  }

 /**
  * Returns jstree core options. Subclases redefine this method
  *
  * @return array
  */
  protected function getCoreOptions($value) {
    return $this->getOption('tree_options');
  }


  /**
   * Returns jsTree options for core components and plugins
   *
   * @return string
   */
  protected function getJsTreeOptions( $value) {
    $core = $this->getCoreOptions($value);
    $plugins = $this->getOption('tree_plugins');
    return strtr("{core: %core%, plugins: %plugins%, json_data: %tree% , themes: { theme : '%theme%', dots: %dots%, icons: %icons% }, ui: { select_limit: 1, initially_select: [ '%value%' ], selected_parent_close: false, selected_parent_open: false }, types: { types:  %types%  } }",array(
      '%core%'    => $this->toJson($core),
      '%plugins%' => empty($plugins)?'[ ]':$plugins,
      '%tree%'    => $this->toJson( $this->getTree()),
      '%dots%'    => $this->toJson($this->getOption('tree_show_dots')),
      '%icons%'   => $this->toJson($this->getOption('tree_show_icons')),
      '%value%'   => $this->generateNodeId($value), 
      '%types%'   => $this->getJsTreeTypeOptions(),
      '%theme%'   => $this->getOption('theme'),
    ));
  }

  /**
   * Returns javascript code to be executed for tree selected node
   *
   * @return string
   */
  protected function getJsTreeSelectEvent($this_id, $tree_id, $js_selected_node, $label_to_update) {
    return strtr ("jQuery('#%me%').val(%selected_node%.data('id')); jQuery('#%label_to_update%').html('<span class=\'label found\'>'+%selected_node%.children('a').text()+'</span>').append(jQuery('<a class=\'tree-clear\'>%clear_icon%</a>').click(function() {jQuery('#%tree_id%').jstree('deselect_all'); %deselect_js% }));",array(
          '%me%'              => $this_id, 
          '%tree_id%'         => $tree_id, 
          '%selected_node%'   => $js_selected_node,
          '%label_to_update%' => $label_to_update,
          '%clear_icon%'      => $this->getOption('clear_selection_icon'),
          '%deselect_js%'     => $this->getJsTreeDeSelectEvent($this_id, $label_to_update),
    ));
  }

  /**
   * Returns javascript code to be executed for tree deselected node
   *
   * @return string
   */
  protected function getJsTreeDeSelectEvent($this_id, $label_to_update) {
    return strtr ("jQuery('#%me%').val(''); jQuery('#%label_to_update%').html('<span class=\'label not-found\'>%text_label%</span>');",array(
          '%me%'            => $this_id, 
          '%label_to_update%' => $label_to_update,
          '%text_label%' => $this->getOption('no_value_text'),
    ));
  }

  /**
   * Returns javascript code to draw tree
   *
   * @return string
   */
  protected function getJsScript($this_id, $value, $tree_id, $selected_label_to_update ) {
    return javascript_tag( strtr("jQuery(document).ready(function () {
        jQuery('#%tree_id%').jstree( %options% ).
        bind('select_node.jstree', function (event, data) {
            %on_select%
        }).
        bind('deselect_node.jstree', function (event, data) {
            %on_deselect%
        });
        });", array(
      '%tree_id%'   => $tree_id,
      '%options%'   => $this->getJsTreeOptions($value),
      '%on_select%' => $this->getJsTreeSelectEvent($this_id, $tree_id, 'data.rslt.obj',$selected_label_to_update), 
      '%on_deselect%' => $this->getJsTreeDeSelectEvent($this_id, $selected_label_to_update), 
    )));
  }

  /**
  * Prefix used for html ids needed for jstree to work
  *
  * @return array
  */
  protected function getPrefix() {
    return 'cr_js_tree';
  }
  
  public function getStylesheets()
  {
    return array_merge(parent::getStylesheets(), array(
      '/dcReloadedFormExtraPlugin/css/pm_widget_form_propel_input_by_code.css' => 'all',
      '/dcReloadedFormExtraPlugin/css/cr_widget_form_js_tree.css' => 'all'
    ));
  }

 /**
   * Renders a HTML content tag.
   *
   * @param string $name
   * @param <type> $value
   * @param array $attributes
   * @param array $errors
   */
  public function render($name, $value = null, $attributes = array(), $errors = array()) {
    $display_container_id = sprintf("%s_display_%s", $this->getPrefix(), $this->generateId($name));
    $tree_container_id = sprintf("%s_tree_%s", $this->getPrefix(), $this->generateId($name));
    $hidden = new sfWidgetFormInputHidden();
    $get_value = $this->getOption('show_value_callback');
    return 
        strtr('<div>%hidden%<div id="%display_container%" class="treelabel"><span class="%text_label_class%">%text%<span></div><div id="%tree_container%"></div></div>%script%',array(
              '%display_container%' => $display_container_id,
              '%text_label_class%'  => $value == null? 'label not-found': 'label found',
              '%text%'              => $value == null? $this->getOption('no_value_text'): ( $get_value?call_user_func($get_value,$value):$value),
              '%hidden%'            => $hidden->render($name, $value, $attributes , $errors ),
              '%tree_container%'    => $tree_container_id,
              '%script%'            => $this->getJsScript($this->generateId($name), $value, $tree_container_id, $display_container_id )
        ));
  }

}
