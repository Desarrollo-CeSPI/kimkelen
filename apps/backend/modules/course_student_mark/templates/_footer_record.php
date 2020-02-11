<?php $evaluator_instance = SchoolBehaviourFactory::getEvaluatorInstance();?>
<div class="article-div">
    <div class="observation-box">
      <strong><?php echo __('Observations'); ?></strong>:
    </div>
  </div>
  <div>
    <div class="article-div">
      <strong><?php echo __('Total de alumnos'); ?>:</strong>
      <span class="little-box">
        <?php echo $record->countRecordDetailsForSheet($rs->getSheet()) ?>
      </span>

      <strong><?php echo __('Aprobados'); ?>:</strong>
      <span class="little-box">
        <?php echo $record->countRecordDetailsForSheetAndResult($rs->getSheet(),$evaluator_instance->getApprovedResult()) ?>
      </span>

      <strong><?php echo __('Aplazados'); ?>:</strong>
      <span class="little-box">
        <?php echo $record->countRecordDetailsForSheetAndResult($rs->getSheet(),$evaluator_instance->getDisapprovedResult()) ?>
      </span>

      <strong><?php echo __('Ausentes'); ?>:</strong>
      <span class="little-box">
        <?php echo $record->countRecordDetailsForSheetAndResult($rs->getSheet(),$evaluator_instance->getAbsentResult()) ?>
      </span>
    </div>
  </div>
  <br>
  <div class="record-footer">
    <div class="article-div">
      La Plata, __________ de ______________________ de __________
    </div>


    <div style="float: right" class="signature">
      <p class="signature-text">Profesor</p>
      <p class="signature-subtext">Firma y aclaración</p>
    </div>

    <div class="box-sheet">
        <span class="right min-size">
            Hoja <?php echo $rs->getSheet() . '/' . count($record->getRecordSheets())?>
        </span>
    </div>
  </div>
