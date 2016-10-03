{<?php $nb1 = count($student); $j = 0; foreach ($student as $key => $value): ++$j ?>
"<?php echo $key ?>": <?php echo json_encode($value).($nb1 == $j ? '' : ',') ?>
<?php endforeach; ?>
}