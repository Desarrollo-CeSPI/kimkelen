<div class="title"><?php echo __('Admonition details'); ?></div>

<?php $periods_array = CareerSchoolYearPeriodPeer::getPeriodsArrayForCourseType($division->getCourseType(), $division->getCareerSchoolYearId()); ?>

<div>
  <div class="admonition_details">
    <?php foreach ($periods_array as $short_name => $period): ?>
    <?php if (StudentDisciplinarySanctionPeer::countStudentDisciplinarySanctionsForPeriod($student, $division->getSchoolYear(), $period)): ?>
        <table class="gridtable">
          <thead>
            <tr>
              <td colspan="5" class="partial_average"><?php echo $period->getName() ?></td>
            </tr>
          </thead>
          <tbody>
            <tr>
              <th><?php echo __('Resolution date') ?></th>
              <th><?php echo __('Description') ?></th>
              <th><?php echo __('Motivo') ?></th>
              <th><?php echo __('Disciplinary sanction type') ?></th>
              <th><?php echo __('Total') ?></th>
            </tr>
            <?php foreach (StudentDisciplinarySanctionPeer::retrieveStudentDisciplinarySanctionsForPeriod($student, $division->getSchoolYear(), $period) as $student_disciplinary_sanction): ?>

              <tr>
                <td><?php echo $student_disciplinary_sanction->getFormattedRequestDate(); ?></td>
                <td><?php echo $student_disciplinary_sanction->getName(); ?></td>
                <td><?php echo $student_disciplinary_sanction->getDisciplinarySanctionType(); ?></td>
                <td><?php echo $student_disciplinary_sanction->getSanctionType(); ?></td>
                <td><?php echo $student_disciplinary_sanction->getValueString(); ?></td>
              </tr>
            <?php endforeach; ?>
          </tbody>
          <tfoot>
            <tr>
              <td colspan ="5" class="total">Total <?php echo StudentDisciplinarySanctionPeer::countStudentDisciplinarySanctionsForPeriod($student, $division->getSchoolYear(), $period) ?></td>
            </tr>
          </tfoot>
        </table>
    <?php endif; ?>

    <?php endforeach; ?>
  </div>
</div>

<?php include_partial('signature_boxes') ?>
