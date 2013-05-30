var dcFinder = {
  setCurrent: function(id, anchor) {
    var finder_list = $(id).up('.double_list').previousSiblings('.finder');
    $(finder_list[0]).down('.current').removeClassName('current');

    $(anchor).up(0).addClassName('current');
  }
}
