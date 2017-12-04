
<div class="report-header">
    <div class="logo"><?php echo image_tag("logo-kimkelen-negro.png", array('absolute' => true)) ?></div>
    <div class="header_row">
      <h2><?php echo __('Print averages'); ?></h2>
      <div class="title2"><?php echo __('School year') ?>: </div>
      <div class="row-content"><?php echo $division->getSchoolYear() ?></div>
      <div class="title2"><?php echo __('Año/Nivel') ?>: </div>
      <div class="row-content"><?php echo $division->getYear() ?></div> 
      <div class="title2"><?php echo __('Division') ?>: </div>
      <div class="row-content"><?php echo $division->getDivisionTitle(); ?></div>
      <div class="row-content"></div>
    </div>
</div>
