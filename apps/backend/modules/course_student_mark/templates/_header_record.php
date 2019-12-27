<div class="record-header">
    <div>
      <div class="logo"><?php echo image_tag("kimkelen_logo.png", array('absolute' => true, 'class' => 'logo_print_record')) ?></div>
    </div>
  </div>  
  <div style="text-align: center">
      <h2>Acta de <?php echo ($cs->getCourse()->isPathway())? 'Trayectoria' : 'Cursada'?></h2>
  </div>

  <div class="gray-background">
      <strong><?php echo __('Subject'); ?></strong>:
      <strong><?php echo $cs->getCareerSubjectSchoolYear()->getCareerSubject()->getSubject() . ' - ' . $cs->getCareerSubjectSchoolYear()->getYear() . ' año'  ?></strong>
      
      <span class="right"> 
          <strong><?php echo __('School year'); ?>:</strong> <?php echo $cs->getCourse()->getSchoolYear() ?> 
      </span>
  </div>
  <div class="gray-background">
    <span><strong><?php echo 'Acta N°: '; ?></strong>  <?php echo $record->getId(); ?> </span>
    <span class="right"><strong><?php echo 'Tomo: '; ?></strong><?php echo ($rs->getBook()) ? $rs->getBook() : ' _______________________ '; ?>     <strong> <?php echo 'Folio físico: '; ?></strong><?php echo ($rs->getPhysicalSheet())? $rs->getPhysicalSheet() : ' ________ '; ?></span>
  </div>
  
  <div class="white-background">
    <span><strong> <?php echo __('Teacher') ?>:</strong></span>
    <span>     <?php echo $record->getTeachers() ?> </span>
  </div>

