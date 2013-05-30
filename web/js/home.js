function initialize_home()
{
  jQuery('.hide-me').slideUp(500);

  jQuery('#home_container .toggler').click(toggle_box);
}

function toggle_box()
{
  jQuery(this).toggleClass('toggler-open').parent('.box').find('.content').slideToggle(500);
}

