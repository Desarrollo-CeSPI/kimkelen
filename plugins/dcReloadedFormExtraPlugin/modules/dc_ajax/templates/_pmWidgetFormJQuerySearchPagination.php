<?php if (count($results) < $total_results): ?>
  
  <?php if ($previous_page >= 0): ?>
    
    <span id="jquery_search_navigation_previous">
      <?php echo link_to_function(__("Previous"), "$js_var_name.search($previous_page)") ?>
    </span>
    
  <?php endif ?>
  
  <?php if (count($results) == $limit): ?>
    
    <span id="jquery_search_navigation_next">
      <?php echo link_to_function(__("Next"), "$js_var_name.search($next_page)") ?>
    </span>
    
  <?php endif ?>
  
<?php endif ?>