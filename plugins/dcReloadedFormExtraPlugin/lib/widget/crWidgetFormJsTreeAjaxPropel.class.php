<?php
 /**  
  * crWidgetFormJsTreeAjaxPropel is a tree select widget extending 
  * crWidgetFormJsTreeAjax to provide nodes load asynchornously using ajax for 
  * a Propel Peer Table
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
  * @see crWidgetFormJsTreeAjax
  *
  * @author Christian A. Rodriguez <car at cespi.unlp.edu.ar>
  */
class crWidgetFormJsTreeAjaxPropel extends crWidgetFormJsTreeAjax {
 /**
  * Constructor options
  *
  * Required options:
  *   * peer_class:             class name for a peer table as string. 
  *                             For example: PersonPeer
  *   * peer_parent_id_column:  column name as string for access a parent object 
  *                             in hierarchy. For example: PARENT_ID
  * 
  * Available options:
  *   * peer_id_column:         column name as string for access id objects of 
  *                             peer_class objects. Default value: ID
  *   * criteria:               Criteria object. Default value: new Criteria()
  *   * root_nodes_criteria:    If this option is set, it will be used to retrieve root nodes.
  *   * peer_method:            method to retrieve objects: Default value doSelect
  *   * peer_count_method:      method to count objects: Default value doCount
  *   * peer_to_string_method:  method used to get text for each node. 
  *                             Default value: __toString
  *
  * Other options inherited from parent class. @see crWidgetFormJsTree
  *
  * @param array $options     An array of options
  * @param array $attributes  An array of default HTML attributes
  *
  * @see crWidgetFormJsTreeAjax
  */
  protected function configure($options = array(), $attributes = array()) {
    $this->addOption('tree',array());
    $this->addOption('url', url_for('@crJsTreePropel')); 
    $this->addOption('get_path_to_node_callback', array($this, 'getPathToNode'));
    parent::configure($options, $attributes);
    $this->addRequiredOption('peer_class');
    $this->addRequiredOption('peer_parent_id_column');
    $this->addOption('peer_id_column','ID');
    $this->addOption('criteria', new Criteria());
    $this->addOption('peer_method', 'doSelect');
    $this->addOption('peer_count_method', 'doCount'); 
    $this->addOption('peer_to_string_method', '__toString'); 
    $this->addOption('root_nodes_criteria', null); 
    $this->addOption('get_type_callback', null); 
    $this->addOption('show_value_callback', array($options['peer_class'],'retrieveByPk'));
  }

 /**
  * Magic function that returns a path from tree root to a node as array
  * 
  * @param string id    id of node to get path to
  * @return array
  */
  public function getPathToNode( $id = null )
  {
    if ($id != null)
    {
      $peer_class = $this->getOption('peer_class');
      $peer_parent_id_column = $this->getOption('peer_parent_id_column');
      $tableMap   = call_user_func(array($peer_class, 'getTableMap'));
      $instance = call_user_func( array($peer_class,'retrieveByPk'), $id);
      if ($instance != null)
      {
        $getParentIdMethod = 'get'.$tableMap->getColumn($peer_parent_id_column)->getPhpName();
        $parent_id = $instance->$getParentIdMethod();
        if ( $parent_id != null)
        {
          return array_merge (array($parent_id), $this->getPathToNode ($parent_id));
        }
      }
    }
    return array();
  }

 /**
  * Overwites javascript string used to build parameters that will be send to our ajax service.
  * We need to send lot of parameters to this service, and be careful to not be hacked. Security 
  * is considered with a required value that must be configured per application
  *
  * @return string
  */
  protected function getJstreeAjaxCallback() {
    return strtr(
          'function (n) { 
            return { 
                security_token: "%security_token%",
                operation: n == -1? "get_root":"get_children",
                node_id: n == -1? -1 : n.data("id"),
                peer_class: "%peer_class%",
                root_nodes_criteria: "%root_nodes_criteria%",
                peer_id_column: "%peer_id_column%",
                peer_parent_id_column: "%peer_parent_id_column%",
                criteria: "%criteria%",
                peer_method: "%peer_method%",
                peer_count_method: "%peer_count_method%",
                peer_to_string_method: "%peer_to_string_method%",
                get_type_callback: "%get_type_callback%",
                   };
                }',array(
              "%security_token%"            => $this->encode(crWidgetFormJsTreeAjaxPropel::getSecurityToken(
                                                    $this->getOption('peer_class'), 
                                                    $this->getOption('peer_parent_id_column'),
                                                    $this->getOption('peer_id_column'),
                                                    serialize($this->getOption('criteria')),
                                                    $this->getOption('peer_method'),
                                                    $this->getOption('peer_count_method'),
                                                    $this->getOption('peer_to_string_method'))),
              "%peer_class%"                => $this->encode($this->getOption('peer_class')),
              "%peer_id_column%"            => $this->encode($this->getOption('peer_id_column')),
              "%peer_parent_id_column%"     => $this->encode($this->getOption('peer_parent_id_column')),
              "%criteria%"                  => $this->encode(serialize($this->getOption('criteria'))),
              "%peer_method%"               => $this->encode($this->getOption('peer_method')),
              "%peer_count_method%"         => $this->encode($this->getOption('peer_count_method')),
              "%peer_to_string_method%"     => $this->encode($this->getOption('peer_to_string_method')),
              "%root_nodes_criteria%"       => $this->encode(serialize($this->getOption('root_nodes_criteria'))),
              "%get_type_callback%"         => $this->encode(serialize($this->getOption('get_type_callback'))),
            ));
  }

 /**
  * Returns $value encoded using base64 
  * 
  * @params string $value   value to encode
  * 
  * @return string
  */
  protected function encode($value) {
    if ( is_array($value) ) return null;
    return base64_encode($value);
  }

 /**
  * Build a unique secret key shared with service to provide some kind of security
  * This approach is Something like csrf_token
  *
  * @return string
  */
  public static function getSecurityToken($peer_class, $peer_parent_id_column, $peer_id_column, $criteria_serialized, $peer_method, $peer_count_method, $peer_to_string_method)
  {
    return sha1(
        $peer_class.      self::getSalt().    $peer_parent_id_column.
        $peer_id_column.  self::getSalt().    $criteria_serialized. 
        $peer_method.     self::getSalt().    $peer_count_method.
        $peer_to_string_method
    );
  }

  private static function getSalt() {
    $ret = sfConfig::get('app_crWidgetFormJsTreeAjaxPropel_salt', null);
    if ( $ret == null) throw new LogicException('crWidgetFormJsTreeAjaxPropel_salt application configuration value is not set');
    return $ret;
  }



}
