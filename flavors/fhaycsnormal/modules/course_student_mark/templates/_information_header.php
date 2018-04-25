<div class="report-header">
  <div class="logo"><?php echo image_tag("logo-kimkelen.png", array('absolute' => true)) ?></div>
  <div class="header_row">
    <h2><?php echo __('Print califications'); ?></h2>
    <div class="title2"><?php echo __('School year') ?>: </div>
    <div class="row-content"><?php echo $course->getSchoolYear() ?></div>
    <div class="title2"><?php echo __('Año/Nivel') ?>: </div>
    <div class="row-content"><?php echo $course->getYear() ?></div>
    <?php if (!(is_null($course->getDivision()))): ?>
      <div class="title2"><?php echo __('Division') ?>: </div>
      <div class="row-content"><?php echo $course->getDivision()->getDivisionTitle(); ?></div>
    <?php endif; ?>
    <div class="title2"><?php echo __('Subject') ?>: </div>
    <div class="row-content"><?php echo $course->getSubjectsStr(); ?></div>
  </div>
  <div class="header_row">
    <div class="title2"><?php echo __('Teacher') ?>: </div>
    <div class="row-content"><?php echo $course->getTeachersStr() ?></div>
  </div>
</div>