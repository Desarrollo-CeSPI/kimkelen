<?php
 /**
  * crWidgetFormJsTreeAjax is a tree select widget extending crWidgetFormJsTree to provide
  * nodes load asynchornously using ajax
  *
  * @see crWidgetFormJsTree
  *
  * @author Christian A. Rodriguez <car at cespi.unlp.edu.ar>
  */
class crWidgetFormJsTreeAjax extends crWidgetFormJsTree {
  /**
   * Constructor options
   *
   * Required options:
   *  * url: Ajax service called using POST method, and sending the following parameters:
   *    - operation: which is one of:
   *      + get_children: this call is to fetch children nodes of specified node in paramter node_id
   *      + get_root: this call is to fetch root nodes of tree
   *    - node_id: id of node to get children of
   *
   *    Nodes returned by this url action must be in json format, as described in the following exmaple:
   *    [
   *      { data: 'Folder Node name',
   *        attr: { id: 1 , rel: 'folder'},
   *        state: 'closed'
   *      },
   *      { data: 'File Node name',
   *        attr: { id: 2 },
   *      }
   *    ]
   *    In this example, first node will be an ajax node because of the specified state. The second node will not
   *    trigger an ajax callback.
   *    See also, the rel attribute that will be used to change node properties for folder type nodes
   *
   *  * get_path_to_node_callback: function to retrieve path up to node as array. Use in forms that set default
   *    value and should be displayed open up to selected node
   *
   * Available options:
   *  Same as parent class. @see crWidgetFormJsTree.
   *
   * @param array $options     An array of options
   * @param array $attributes  An array of default HTML attributes
   *
   * @see crWidgetFormJsTree
   */
  protected function configure($options = array(), $attributes = array()) {
    $this->addOption('tree',array());
    $this->addOption('initially_open', true);
    parent::configure($options, $attributes);
    $this->addRequiredOption('url');
    $this->addRequiredOption('get_path_to_node_callback');
  }

 /**
  * Returns needed javascript in order to replace json static tree of crWidgetFormJsTree by
  * an ajax call to a service that provides the json returned output as explained at the begining
  * of this file
  *
  * @return string
  */
  protected function getTree( $tree=null ) {
    return array (
        'ajax' => array (
          'type' => 'POST',
          'url' => $this->getOption('url'),
          'data' => '#JSTREE_AJAX_CALLBACK#',
          'success' => '#JSTREE_AJAX_SUCCESS#',
        ),
    );
  }

 /**
  * Core options must be overwritten so we can tell jstree to open nodes in path to selected node
  * This is useful for this widget in an edit context, where a value needs to be displayed.
  *
  * @return array
  */
  protected function getCoreOptions($value) {
    $options = $this->getOption('tree_options');
    if ($this->getOption('initially_open'))
    {
      $options->initially_open = array_map (array($this, 'generateNodeId'),  $this->getPathUpTo($value));
    }
    else
    {
      $options->initially_open = array();
    }
    return $options;
  }

 /**
  * Returns the result of calling a callback that will return a path in tree to selected node
  *
  * @return array
  */
  private function getPathUpTo($id)
  {
    return call_user_func($this->getOption('get_path_to_node_callback'), $id);
  }

 /**
  * Returns a javascript string used to build parameters that will be send to service ajax URL
  *
  * @return string
  */
  protected function getJstreeAjaxCallback() {
    return 'function (n) {
            return {
                operation: n == -1? "get_root":"get_children",
                node_id: n == -1? -1 : n.data("id")
                   };
                }';
  }

 /**
  * Returns a javascript string used to process returned nodes as json by the ajax service called
  * We process each node returned to reset html id, trying to make it unique in DOM
  *
  * @return string
  */
  protected function getJstreeAjaxSuccessCallback() {
    return sprintf('function (data) { jQuery(data).each(function (i,o){ o.metadata={id: o.attr.id}; o.attr.id="%s_%s_"+ o.attr.id;}); }',
            $this->getPrefix(),
            $this->getOption('prefix_tree_node_id'));
  }

 /**
  * Overwite parent function to add these two callbacks in a valid format. The problem is that json_encode
  * breaks javascript function format, because it adds quotes to returned values
  *
  * @return string
  */
  protected function toJson($data) {
    return strtr(parent::toJson($data), array(
      '"#JSTREE_AJAX_CALLBACK#"'  =>  $this->getJstreeAjaxCallback(),
      '"#JSTREE_AJAX_SUCCESS#"'   =>  $this->getJstreeAjaxSuccessCallback()));
  }

}
