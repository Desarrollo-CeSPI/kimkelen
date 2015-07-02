<?php
 /**  
  * crWidgetFormJsTreeAjaxPropelMerge is a tree select widget extending 
  * crWidgetFormJsTreeAjaxPropel to provide nodes load asynchornously using ajax for 
  * a Propel Peer Table with the feature of merging nodes of different Peer classes
  * It is in an experimental stage, because it is not well tested
  *
  ******************************************************************************
  * IMPORTANT
  ******************************************************************************
  * This widget requires to define an application configuration value to work:
  * Add in config/app.yml a text value to the following configuration label:
  * app_crWidgetFormJsTreeAjaxPropel_salt. For example:
  *   all:
  *     crWidgetFormJsTreeAjaxPropel:
  *       salt: s3cur3_my_w1dg3t
  *******************************************************************************
  *
  * @see crWidgetFormJsTreeAjaxPropel
  *
  * @author Christian A. Rodriguez <car at cespi.unlp.edu.ar>
  */
class crWidgetFormJsTreeAjaxPropelMerge extends crWidgetFormJsTreeAjaxPropel {
    var $relations;
 /**
  * Constructor options
  *
  * Required options:
  *   * peer_class:               associative array of keys representing types of tree nodes
  *                               and as value a peer class name as it is used in parent class
  *   * peer_class_value:         string representing which of peer_class elements will be the one used
  *                               for selecting values (this means that when this widget is in 
  *                               edit context, the received integer value, to which class will be associated).
  *   * peer_root_type:           type of root nodes
  *   * peer_type_relationships:  array representing relationships between nodes of different types and classes
  *     The following is an example of this widget instantiated for a case where offices are arranged hierarchically, 
  *     each one of them as part of dependencies that also are arrenaged as a hierarchy. The schema for this objects
  *     are as follow:
  *       Dependency:
  *         tableName: dependency
  *         columns:
  *           id:
  *           code:
  *             type: varchar(50)
  *             required: true
  *             index: unique
  *           name:
  *             type: varchar(255)
  *             required: true
  *           dependency_id:
  *             type: integer
  *             foreignClass: Dependency
  *             foreignReference: id
  *             onDelete: cascade
  *             required: false
  *       Office:
  *         tableName: office
  *         columns:
  *           id:
  *           code:
  *             type: varchar(50)
  *             required: false
  *           name:
  *             type: varchar(255)
  *             required: true
  *           dependency_id:
  *             type: integer
  *             foreignClass: Dependency
  *             foreignReference: id
  *             onDelete: cascade
  *             required: true
  *           office_id:
  *             description: Parent office (When this office is a sub-office)
  *             type: integer
  *             foreignClass: Office
  *             foreignReference: id
  *             onDelete: cascade
  *
  *   Now, lets see how this widget can be used to select an office in a tree of dependencies:
  *       new crWidgetFormJsTreeAjaxPropelMerge( array(
  *             'peer_class_value' => 'OfficePeer',
  *             'not_selectable_types' => array('dependency'),
  *             'tree_node_icons_per_type' => array (     //Comment of you have no icons
  *               'dependency' => image_tag('building'),
  *               'office' => image_tag('office'),
                ),
  *             'peer_class' => array(
  *                   'dependency' => 'DependencyPeer',
  *                   'office'     => 'OfficePeer',
  *             ),
  *             'peer_parent_id_column' => array(
  *                   'dependency'  => 'DEPENDENCY_ID',
  *                   'office'      => 'OFFICE_ID',
  *             ),
  *             'peer_root_type' => 'dependency',
  *             'criteria'  => array( // Comment if you don't want to filter any listed objects. It is here to show how to use
  *               'dependency' => $criteria_1,
  *               'office'  => $criteria_2),
  *             'peer_type_relationships' => array( 
  *                 'dependency'=> array( 
  *                   'dependency_office' => array(
  *                     'related_type' => 'office', 
  *                     'by_column'=> 'DEPENDENCY_ID'))),
  *           ));
  *
  *   Looking this example, we have two types: office and dependency. For each of these types we can specify custom options
  *
  * Available options:
  *   Other options inherited from parent class. @see crWidgetFormJsTree. 
  *   This widget will convert each crWidgetFormJsTreeAjaxPropel option to an array
  *   of values, one per each type specified in peer_class option
  *   It is useful to use the following options with this widget:
  *   - tree_node_icons_per_type
  *   - not_selectable_types
  *
  * @param array $options     An array of options
  * @param array $attributes  An array of default HTML attributes
  *
  * @see crWidgetFormJsTreeAjaxPropel
  */
  protected function configure($options = array(), $attributes = array()) {
    parent::configure($options, $attributes);
    $this->addOption('url', url_for('@crJsTreePropelMerge')); 
    $this->addRequiredOption('peer_class_value');
    $this->addRequiredOption('peer_root_type');
    $this->addRequiredOption('peer_type_relationships');
    $this->addOption('show_value_callback', array($options['peer_class_value'],'retrieveByPk'));

  }

  /* Have to overwite because we try to validate received options
   *
   */
  public function __construct($options = array(), $attributes = array())
  {
    parent::__construct($options, $attributes);
    $this->normalizeOptions();
  }

  private function _normalizeOption($option) {
    $types = array_keys($this->getOption('peer_class'));
    $value = $this->getOption($option);
    if ( is_array($value) ) {
      $diff = array_diff ( array_keys($value), $types);
      if ( count($diff)>0 ) {
        throw new LogicException( sprintf("'%s' must be have one id_column for each type specified", $option) );
      }
    }
    else {
      $opt=array();
      foreach($types as $t) {
        $opt[$t]=$value;
      }
      $this->setOption($option, $opt);
    }
  }

 /**
  * Do as many checks as we can
  */
  protected function normalizeOptions() {
    /* peer_class */
    if ( !is_array($this->getOption('peer_class')) ) {
      throw new LogicException( "'peer_class' must be an array");
    }
    $types = array_keys($this->getOption('peer_class'));
    if ( empty( $types) ) {
      throw new LogicException( "'peer_class' must be an array with at least one element");
    }
    /* peer_root_type */
    if (! in_array($this->getOption('peer_root_type'), $types) ) {
      throw new LogicException( "'peer_root_type' must be one of specified types");
    }
    $diff = array_diff ( array_keys($this->getOption('peer_class')), $types); 
    if ( count($diff)>0 ) {
      throw new LogicException( "'peer_class' must be have one peer_class for each type specified");
    }
    /* peer_parent_id_column */
    $diff = array_diff ( array_keys($this->getOption('peer_parent_id_column')), $types); 
    if ( count($diff)>0 ) {
      throw new LogicException( "'peer_parent_id_column' must be have one parent_id_column for each type specified");
    }
    /* peer_type_relationships */
    if ( !is_array($this->getOption('peer_type_relationships')) ) {
      throw new LogicException( "'peer_type_relationships' must be an array");
    }
    $this->relations = array();
    foreach($this->getOption('peer_type_relationships') as $relation_type => $relations) {
      if ( !is_array($relations) ) {
        throw new LogicException( "'peer_type_relationships' must be an array, and each element must be an array of relationships. See documentation");
      }
      foreach($relations as $name=>$relationship) {
        if ( ! array_key_exists('related_type', $relationship) || ! array_key_exists('by_column',  $relationship) ) {
          throw new LogicException( "'peer_type_relationships' has a relationship with unsupported format. See documentation");
        }
        $rtype = $relationship['related_type'];
        if ( !array_key_exists($rtype, $this->relations) ) {
          $this->relations[$rtype]=array(
            'type'        => $relation_type,
            'by_column'   => $relationship['by_column']
          );
        }
        else {
          throw new LogicException( "'peer_type_relationships' has two relations defined for the same type. See documentation");
        }
      }
    }
    /* Remaining options to be normalized */
    foreach (array('peer_id_column', 'criteria', 'peer_method', 'peer_count_method', 'peer_to_string_method') as $opt) {
      $this->_normalizeOption($opt);
    }
    
  }

 /** 
  * Ultra magic function to retrieve path from root up to node
  * Returned array change parent format
  *
  * @return array
  */
  public function getPathToNode( $id = null, $current_peer_class = null )
  {
    if ($id != null)
    {
      $peer_class = $current_peer_class == null?$this->getOption('peer_class_value'): $current_peer_class;
      $peer_type = array_search($peer_class, $this->getOption('peer_class'));
      $peer_parent_id_column = $this->getOption('peer_parent_id_column');
      $tableMap   = call_user_func(array($peer_class, 'getTableMap'));
      $instance = call_user_func( array($peer_class,'retrieveByPk'), $id);
      if ($instance != null) {
        $getParentIdMethod = 'get'.$tableMap->getColumn($peer_parent_id_column[$peer_type])->getPhpName();
        $parent_id = $instance->$getParentIdMethod();
        if ( $parent_id != null) {
          return array_merge (array(array('type' => $peer_type, 'id' => $parent_id)), $this->getPathToNode ($parent_id, $current_peer_class));
        }
        else {
          /* This is a relation between different peer models */
          if ( array_key_exists($peer_type, $this->relations) ) {
            $type = $this->relations[$peer_type]['type'];
            $getParentIdMethod = 'get'.$tableMap->getColumn($this->relations[$peer_type]['by_column'])->getPhpName();
            $parent_id = $instance->$getParentIdMethod();
            if ( $parent_id != null) {
              $peer_class = $this->getOption('peer_class');
              return array_merge( array(array('type' => $type, 'id' => $parent_id)), $this->getPathToNode($parent_id, $peer_class[$type]) );
            }  
          }
        }
      }
    }
    return array();
  }

  protected function getJstreeAjaxCallback() {
    $types = array_keys($this->getOption('peer_class'));
    return strtr(
          'function (n) { 
            return { 
                security_token: "%security_token%",
                operation: n == -1? "get_root":"get_children",
                node_id: n == -1? -1 : n.data("id"),
                node_type: n == -1? -1 : n.attr("rel"),
                peer_class: "%peer_class%",
                peer_id_column: "%peer_id_column%",
                peer_parent_id_column: "%peer_parent_id_column%",
                criteria: "%criteria%",
                root_nodes_criteria: "%root_nodes_criteria%",
                peer_method: "%peer_method%",
                peer_count_method: "%peer_count_method%",
                peer_to_string_method: "%peer_to_string_method%",
                peer_types: "%peer_types%",
                peer_root_type: "%peer_root_type%",
                peer_type_relationships: "%peer_type_relationships%",
                   };
                }',array(
              "%security_token%"            => $this->encode(crWidgetFormJsTreeAjaxPropel::getSecurityToken(
                                                    serialize($this->getOption('peer_class')), 
                                                    serialize($this->getOption('peer_parent_id_column')),
                                                    serialize($this->getOption('peer_id_column')),
                                                    serialize($this->getOption('criteria')),
                                                    serialize($this->getOption('peer_method')),
                                                    serialize($this->getOption('peer_count_method')),
                                                    serialize($this->getOption('peer_to_string_method')))),
              "%peer_class%"                => $this->encode(serialize($this->getOption('peer_class'))),
              "%peer_id_column%"            => $this->encode(serialize($this->getOption('peer_id_column'))),
              "%peer_parent_id_column%"     => $this->encode(serialize($this->getOption('peer_parent_id_column'))),
              "%criteria%"                  => $this->encode(serialize($this->getOption('criteria'))),
              "%root_nodes_criteria%"       => $this->encode(serialize($this->getOption('root_nodes_criteria'))),
              "%peer_method%"               => $this->encode(serialize($this->getOption('peer_method'))),
              "%peer_count_method%"         => $this->encode(serialize($this->getOption('peer_count_method'))),
              "%peer_to_string_method%"     => $this->encode(serialize($this->getOption('peer_to_string_method'))),
              "%peer_types%"                => $this->encode(serialize($types)),
              "%peer_root_type%"            => $this->encode(serialize($this->getOption('peer_root_type'))),
              "%peer_type_relationships%"   => $this->encode(serialize($this->getOption('peer_type_relationships'))),
            ));
  }

  protected function getJstreeAjaxSuccessCallback() {
    return sprintf('function (data) { jQuery(data).each(function (i,o){ o.metadata={id: o.attr.id}; o.attr.id="%s_%s_"+o.attr.rel+"_"+ o.attr.id;}); }',
            $this->getPrefix(),
            $this->getOption('prefix_tree_node_id'));
  }

  protected function generateNodeId($node)
  {
    if (empty($node) ) return null;
    if (is_int($node) ) {
      $node = array (
        'id'    => $node, 
        'type'  => array_search($this->getOption('peer_class_value'), $this->getOption('peer_class')),
      );
    }
    return sprintf("%s_%s_%s_%s",
      $this->getPrefix(),
      $this->getOption('prefix_tree_node_id'),
      $node['type'],
      $node['id']);
  }



}
