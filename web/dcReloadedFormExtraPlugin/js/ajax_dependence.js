/* 
 * Singleton class that keeps track of widgets dependencies 
 */
var dcWidgetFormAjaxDependence={

  // Dictionary of objects with full information of widgets using this singleton
  widgets: $H(),
  
  /* Array of trees, each tree represents the relationship "observed by" being the root node the ancestor 
   * of every children 
   */
  forest: $A(),

  // Adds a new object to forest and dictionary of objects 
  add: function (id,observe_id,event_type,url,image,widget,update_id)
  {
    if (this.widgets.size()==0)
    {
      Event.observe(window,'load',this.updateOnLoad);
    }
    var addToForest=this.widgets.get(id)==undefined;
    var obj={
        id: id,
        observe_id: observe_id, 
        event_type: event_type, 
        url: url, 
        image: image, 
        widget: widget,
        update_id: update_id};
      this.widgets.set(id,$H(obj));
      if (addToForest)
      {
        this.addToForest(id,observe_id);
      }
  },
  // Returns an array of dependants elements of id
  getDependenciesFor: function (id)
  {
    var ret = $A();
    var subtree =null;
    var tree=this.forest.find(function(tr){ 
      return tr.find(function (e){
        return e.id==id;
      })!=null
    });
    if (tree!=null)
    {
      subtree=tree.find(function(e){
        return e.id==id;
      });
      subtree.getChildren().each(function (t){
        ret=ret.concat(t.toArray());
      });
    }
    return ret;
  },
  /* This method must be called after an ajax action writes its template and we need to disable
   * elements depending on the rewritten element 
   */
  updateDependencies: function (id)
  {
    this.getDependenciesFor(id).each(function(e){
      if ( ($(e.id) != null) && (dcWidgetFormAjaxDependence.initialLoading==0) )
      {
        $(e.id).disabled=true;
      }
      if ( e.observe_id==id )
      {
        var obj=dcWidgetFormAjaxDependence.widgets.get(e.id).toObject();
        Event.observe(obj.observe_id,obj.event_type,function(event){
          dcWidgetFormAjaxDependence.ajaxUpdate(obj.id, function () {
            dcWidgetFormAjaxDependence.updateDependencies(obj.id);
          });
        });
      }
    });  
    if (dcWidgetFormAjaxDependence.initialLoading > 0)
    {
      dcWidgetFormAjaxDependence.initialLoading--;
    }
  },
  // Fixes a problem with observed elements that are updated and do not call updateDependencies as it is required
  fixMyObservers: function (id)
  {
    var matches=this.widgets.findAll(function(w){ 
      return w.value.toObject().observe_id==id 
    });
    matches.each(function (pair){
      var id=pair.key;
      var obj=pair.value.toObject();
      var handler=function(event){
        dcWidgetFormAjaxDependence.ajaxUpdate(obj.id, function () {
          dcWidgetFormAjaxDependence.updateDependencies(obj.id);
        });
      };
      Event.observe(obj.observe_id,obj.event_type,handler);
      handler();
      });
  },
  // Adds a new relationship to our forest
  addToForest: function (id,observe_id)
  {
    var treeNode=new Tree({id: id,observe_id: observe_id})
    var node=null;
    var t=this.forest.find(function(tr){ 
      return tr.find(function (e){
        return e.id==observe_id;
      })!=null
    });
    if (t != null)
    {
      /* if there is a tree that contains the specified observe_id, we should add a new child for it
       * containing id as dependant of it
       */
      node=t.find(function (e){
        return e.id==observe_id
      });
      node.addChild(treeNode);
    }
    else
    {
      /* if there is no tree in forest relating to new element.... */
      this.forest.push(treeNode);
    }
    
    /* lets look for some root node that may depend on this id.... */
    t=dcWidgetFormAjaxDependence.forest.find(function(tr){ 
      return tr.find(function (e){
        return e.observe_id==id;
      })!=null
    });
    if ((t!=null)&&(t.getNode().observe_id==id))
    {
      //Change root node
      this.forest.without(t);
      treeNode.addChild(t);
      this.forest.push(treeNode);
    }
  },
  /* document onLoad event. It will initialize widgets for the first time and
   * create observers. The variable initialLoading try not to disable elements while
   * loading initially because disabling elements relates to elemets relationship broken by
   * some change
   */
  updateOnLoad: function ()
  {
    dcWidgetFormAjaxDependence.initialLoading=0;
    dcWidgetFormAjaxDependence.forest.each(function (t){
      dcWidgetFormAjaxDependence.initialLoading+=t.length();
    });
    dcWidgetFormAjaxDependence.forest.each(function(t){
      dcWidgetFormAjaxDependence.updateTreeOnLoad(t);
    });
    dcWidgetFormAjaxDependence.widgets.each(function (pair){
      var obj=pair.value.toObject();
      var id=pair.key;
      if ( $(obj.observe_id)!=null )
      {
        Event.observe(obj.observe_id,obj.event_type,function(){
          dcWidgetFormAjaxDependence.ajaxUpdate(id, function () {
            dcWidgetFormAjaxDependence.updateDependencies(id);
          });
        });
      }
    });
  },
  /* When initializing widgets, we need to do it in an ordered way. This order is defined by 
   * the observed by relationshiṕ mapped to each forest tree */
  updateTreeOnLoad: function (tree)
  {
    return this.ajaxUpdate(tree.getNode().id, function (){
      tree.getChildren().each(function(c){
        dcWidgetFormAjaxDependence.updateTreeOnLoad(c);
      });
    });
  },
  // Callback to make an ajax call for specified id 
  ajaxUpdate: function (id, callback)
  {
    var obj=this.widgets.get(id).toObject();
    new Ajax.Updater(obj.update_id, obj.url,{
      asynchronous:false,
      evalScripts:true,
      before: $(obj.update_id).update(obj.image),
      parameters: 'widget='+obj.widget+'&id='+id+'&observed_value='+($(obj.observe_id)==null?'':($F(obj.observe_id)==null?'':$F(obj.observe_id))),
      onComplete: callback
    });
  }

}

/* 
 * Tree Object
 */
function Tree(data)
{
  /* tree node data */
  this.data=data;
  /* children array */
  this.children=$A();

  /* returns data in this node */
  this.getNode=function()
  {
    return this.data;
  };

  /* Adds a new child node */
  this.addChild=function(tree)
  {
    this.children.push(tree);
  };

  /* returns the list of child tree nodes */
  this.getChildren=function()
  {
    return this.children;
  }

  /* finds a node that handler returns true */
  this.find=function (handler)
  {
    if (handler(this.getNode())) return this;
    return this.getChildren().find(function(c)
    {
      return handler(c.getNode());
    });
  }
  
  /* flatten tree into an array of nodes data */
  this.toArray=function()
  {
    var ret=$A();
    ret.push(this.getNode());
    this.getChildren().each(function (c){
      ret=ret.concat(c.toArray());
    });
    return ret;
  };

  this.length=function()
  {
    return this.toArray().length;
  }
}

