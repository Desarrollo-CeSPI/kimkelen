<?php use_helper("I18N", "JavascriptBase") ?>

<?php if ($total_results): ?>

  <ul id="jquery_search_results">
    <?php foreach ($results as $result): ?>
      <li id="result_<?php echo $result->getId() ?>">
        <?php echo $result ?>

        <script>          
    	    <?php echo $js_var_name ?>.getSelectLink(<?php echo $result->getId() ?>, "<?php echo $result ?>");
        </script>
    
      </li>
    <?php endforeach ?>
  </ul>
  
  <?php include_partial("dc_ajax/pmWidgetFormJQuerySearchPagination", array("results" => $results, "total_results" => $total_results, "js_var_name" => $js_var_name, "previous_page" => $previous_page, "next_page" => $next_page, "limit" => $limit)) ?>

<?php else: ?>
  
  <script>
    <?php echo $js_var_name ?>.displayNoResultsFoundLabel();
  </script>
  
<?php endif ?>