//Se hace esta function por que el toogle no funciona en IE(
function toggleCorrelatives(classname)
{
  elem = jQuery(classname)[0];
  if (elem.style.display == 'none')
  {
    jQuery(classname).show();
  }
  else
  {
    jQuery(classname).hide();
  }
}
