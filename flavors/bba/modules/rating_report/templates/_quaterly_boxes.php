<?php for ($i = 1; $i < $marks_count; $i++): ?>
  <div class="teacher_signature_box">
    <div class="titletable"><span><?php echo $i . '°C' ?></span></div>
    <div style="margin-top: 16%">
      <div align="center">_________________________</div>
      <div align="center"><?php echo __('Professor signature') ?></div>
      <br>
      <div align="center"><?php echo __('Fecha') ?> _____ / _____ / _____ </div>
    </div>
  </div>
<?php endfor; ?>

<div class="teacher_signature_box">
	<div class="titletable"><span>Ex. Final</span></div>
	<div style="margin-top: 16%">
		<div align="center">_________________________</div>
		<div align="center"><?php echo __('Professor signature') ?></div>
		<br>
		<div align="center"><?php echo __('Fecha') ?> _____ / _____ / _____ </div>
	</div>
</div>