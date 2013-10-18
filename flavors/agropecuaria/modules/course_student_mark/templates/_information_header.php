<div class="report-header">
  <div class="logo"><?php echo image_tag("kimkelen_logo.png", array('absolute' => true)) ?></div>
  <div class="logo_fvet"><?php echo image_tag("fvet.jpeg", array('absolute' => true)) ?></div>
  <div class="school-name">Escuela de Educación Técnico Profesional </div>
  <div class="school-name">de Nivel Medio en Producción Agropecuaria y Agroalimentaria</div>
  <div class="school-name">Facultad de Ciencias Veterinarias de la UBA</div>
  <div class="header_row">
    <h2><?php echo __('Print califications'); ?></h2>
    <div class="title"><?php echo __('School year') ?>: </div>
    <div class="orientation"><?php echo $course->getSchoolYear() ?></div>
    <div class="title"><?php echo __('Nivel') ?>: </div>
    <div class="course"><?php echo $course->getYear() ?></div>
    <?php if (!(is_null($course->getDivision()))): ?>
      <div class="title"><?php echo __('Division') ?>: </div>
      <div class="course"><?php echo $course->getDivision()->getDivisionTitle(); ?></div>
    <?php endif; ?>
    <div class="title"><?php echo __('Subject') ?>: </div>
    <div class="orientation"><?php echo $course->getSubjectsStr(); ?></div>
  </div>
  <div class="header_row">
    <div class="title"><?php echo __('Teacher') ?>: </div>
    <div class="orientation"><?php echo $course->getTeachersStr() ?></div>
  </div>
</div>