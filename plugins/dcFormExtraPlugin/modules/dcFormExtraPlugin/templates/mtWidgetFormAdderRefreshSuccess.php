<?php use_helper('Form') ?>
<?php echo options_for_select(mtUtility::convertToChoices($options, ($add_empty? array(null => '') : array()), $value_method, $key_method), $selected) ?>
