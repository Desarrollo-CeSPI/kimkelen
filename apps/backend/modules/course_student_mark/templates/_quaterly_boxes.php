<?php foreach (range(1, $marks_count) as $number): ?>
  <div class="teacher_signature_box">
    <div class="titletable"><span><?php echo $number . '°C' ?></span></div>
    <div style="margin-top: 16%">
      <div align="center">_________________________</div>
      <div align="center"><?php echo __('Professor signature') ?></div>
      <br>
      <div align="center"><?php echo __('Fecha') ?> _____ / _____ / _____ </div>
    </div>
  </div>
<?php endforeach; ?>