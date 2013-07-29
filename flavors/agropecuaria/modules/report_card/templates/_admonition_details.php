<div class="title"><?php echo __('Admonition details'); ?></div>

<?php $periods_array = CareerSchoolYearPeriodPeer::getPeriodsArrayForCourseType($division->getCourseType(), $division->getCareerSchoolYearId()); ?>

<div>
  <div class="admonition_details">
    <?php foreach ($periods_array as $short_name => $period): ?>
      <table class="gridtable">
        <thead>
          <tr>
            <td colspan="4" class="partial_average"><?php echo $period->getName() ?></td>
          </tr>
        </thead>
        <tbody>
          <?php if (StudentDisciplinarySanctionPeer::countStudentDisciplinarySanctionsForPeriod($student, $division->getSchoolYear(), $period)): ?>
            <tr>
              <th><?php echo __('Resolution date') ?></th>
              <th><?php echo __('Motivo') ?></th>
              <th><?php echo __('Disciplinary sanction type') ?></th>
              <th><?php echo __('Total') ?></th>
            </tr>
            <?php foreach (StudentDisciplinarySanctionPeer::retrieveStudentDisciplinarySanctionsForPeriod($student, $division->getSchoolYear(), $period) as $student_disciplinary_sanction): ?>

              <tr>
                <td><?php echo $student_disciplinary_sanction->getFormattedRequestDate(); ?></td>
                <td><?php echo $student_disciplinary_sanction->getDisciplinarySanctionType(); ?></td>
                <td><?php echo $student_disciplinary_sanction->getSanctionType(); ?></td>
                <td><?php echo $student_disciplinary_sanction->getValue(); ?></td>
              </tr>
            <?php endforeach; ?>
          </tbody>
          <tfoot>
            <tr>
              <td colspan ="4" class="total">Total <?php echo StudentDisciplinarySanctionPeer::countStudentDisciplinarySanctionsForPeriod($student, $division->getSchoolYear(), $period) ?></td>
            </tr>
          </tfoot>
        </table>
      <?php else: ?>
        <tr>
          <td style="text-align:left"><?php echo __("Student doesn't have any disciplinary sanctions.") ?></td>
        </tr></tbody></table>
      <?php endif; ?>

    <?php endforeach; ?>
  </div>
</div>

<?php include_partial('signature_boxes') ?>
<div style="clear:both;"></div>
<br>
<div class="date"><?php echo __('Issue date') ?> <?php echo date('d/m/Y') ?></div>
