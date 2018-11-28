<?php for ($i = 1; $i <= 2; $i++): ?>
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
<?php if ($marks_count == 3) : ?>
<div class="teacher_signature_box">
	<div class="titletable"><span>Ex. Final</span></div>
	<div style="margin-top: 16%">
		<div align="center">_________________________</div>
		<div align="center"><?php echo __('Professor signature') ?></div>
		<br>
		<div align="center"><?php echo __('Fecha') ?> _____ / _____ / _____ </div>
	</div>
</div>
<?php endif; ?>