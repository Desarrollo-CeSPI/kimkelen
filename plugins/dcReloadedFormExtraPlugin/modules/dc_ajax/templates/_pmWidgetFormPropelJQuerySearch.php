<?php use_helper("I18N", "JavascriptBase") ?>

<?php if ($total_objects): ?>

  <ul id="jquery_search_results">
    <?php foreach ($objects as $result): ?>
      <li id="result_<?php echo $result->$methodKey() ?>">
        <?php echo $result->$methodValue() ?>

        <script>          
    	    <?php echo $js_var_name ?>.getSelectLink(<?php echo $result->$methodKey() ?>, "<?php echo $result->$methodValue() ?>");
        </script>
    
      </li>
    <?php endforeach ?>
  </ul>
  
  <?php include_partial("dc_ajax/pmWidgetFormJQuerySearchPagination", array("results" => $objects, "total_results" => $total_objects, "js_var_name" => $js_var_name, "previous_page" => $previous_page, "next_page" => $next_page, "limit" => $limit)) ?>

<?php else: ?>
  
  <script>
    <?php echo $js_var_name ?>.displayNoResultsFoundLabel();
  </script>
  
<?php endif ?>
