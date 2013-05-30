 // This is a very simple demo that shows how a range of elements can
// be paginated.
// The elements that will be displayed are in a hidden DIV and are
// cloned for display. The elements are static, there are no Ajax
// calls involved.

/**
 * Callback function that displays the content.
 *
 * Gets called every time the user clicks on a pagination link.
 *
 * @param {int} page_index New Page index
 * @param {jQuery} jq the container with the pagination links as a jQuery object
 */
function pageselectCallback(page_index, jq)
{
  var max_per_page = jQuery('#Pagination').data('max_per_page');
  var length = jQuery('#Pagination').data('length');


  jQuery('#Searchresult').empty();
  var max_elem = Math.min((page_index + 1) * max_per_page, length);

  // Iterate through a selection of the content and build an HTML string
  for(var i = page_index * max_per_page; i < max_elem; i++)
  {
    var clone = jQuery('#hiddenresult div.result:eq('+i+')').clone();
    jQuery('#Searchresult').append(clone);
  }
  
  return false;
}

/**
 * Initialisation function for pagination
 */
function initPagination()
{
  // count entries inside the hidden content
  var num_entries = jQuery('#hiddenresult div.result').length;
  var max = jQuery('#items_per_page').val();  

  jQuery('#Pagination').data('max_per_page', max);
  jQuery('#Pagination').data('length', num_entries);
  
  // Create content inside pagination element
  jQuery("#Pagination").pagination(num_entries, {
    callback: pageselectCallback,
    items_per_page: max // Show only one item per page
  });
}

function initialize_pagination()
{
  jQuery(document).ready(function(){
    initPagination();
  });
}
