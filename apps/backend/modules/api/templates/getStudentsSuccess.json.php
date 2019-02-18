

{<?php $nb1 = count($scsys); $j = 0; foreach ($scsys as $key => $value): ++$j ?>
"<?php echo $key ?>": <?php echo json_encode($value->asArray()).($nb1 == $j ? '' : ',') ?>
<?php endforeach; ?>
}
