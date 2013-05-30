<?php 
/*
 * Kimkëlen - School Management Software
 * Copyright (C) 2013 CeSPI - UNLP <desarrollo@cespi.unlp.edu.ar>
 *
 * This file is part of Kimkëlen.
 *
 * Kimkëlen is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License v2.0 as published by
 * the Free Software Foundation.
 *
 * Kimkëlen is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with Kimkëlen.  If not, see <http://www.gnu.org/licenses/gpl-2.0.html>.
 */ ?>
<?php use_helper('Javascript') ?>
<?php use_stylesheet('home') ?>
<?php use_stylesheet('pagination.css') ?>
<?php use_stylesheet('search.css', 'last') ?>
<?php use_javascript('pagination.js') ?>
<?php use_javascript('search_pagination') ?>


<div id="home_container" class="search_results_container">
  <h1><?php echo __('Search results') ?></h1>
  
  <form action="<?php echo url_for('search') ?>" method="post">
    <input type="text" name="query" id="search_query" value="<?php echo $query ?>" />
    <input type="submit" value="<?php echo __('Search') ?>" id="search_submit" />
  </form>

  <input type="hidden" value="<?php echo sfConfig::get('app_search_items_per_page', 10) ?>" id="items_per_page">

  <h3><?php echo (count($objects) == 0 ) ? __('The search dont have any result') : __('The search has %number% results', array('%number%' => count($objects)))?></h3>
  
  <div id="hiddenresult" style="display: none;">
    <?php foreach ($objects as $object): ?>
      <div class="result">
        <?php include_partial('search/'. sfInflector::underscore(get_class($object)). '_info', array('object' => $object)) ?>
      </div>
    <?php endforeach?>
  </div>


  <div id="Pagination"></div>

  <br style="clear:both;" />
  
  <div id="Searchresult">
  </div>

</div>

<script type="text/javascript">
//<![CDATA[
  jQuery(initialize_pagination);
//]>
</script>