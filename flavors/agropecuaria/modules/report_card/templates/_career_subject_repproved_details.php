<div class="title"><?php echo __('Career subject repproved details'); ?></div>
<div class="admonition_details">
  <table class="gridtable">
    <thead>
      <tr>
        <th><?php echo __('Subject'); ?></th>
        <th><?php echo __('Examination date'); ?></th>
        <th><?php echo __('Instance'); ?></th>
        <th><?php echo __('Mark'); ?></th>
      </tr>
    </thead>
    <tbody>
      <?php foreach ($examination_repproveds as $examination_repproved): ?>
        <tr>
          <td rowspan="<?php echo count($examination_repproved->getStudentExaminationRepprovedSubjects()) ?>"><?php echo $examination_repproved->getSubject() ?>
            <span><?php echo '(' . $examination_repproved->getCourseSubjectStudent()->getCourseSubject()->getCourse()->getSchoolYear() . ')' ?>
              <div><?php echo __("Status") ?>: <span class="partial_average"><?php echo __(is_null($examination_repproved->getStudentApprovedCareerSubject())) ? 'Pendiente' : 'Aprobada' ?></span></div>
          </td>
          <?php foreach ($examination_repproved->getOrderedStudentExaminationRepprovedSubjects() as $sers): ?>
            <td><?php echo $sers->getDate('d/m/Y'); ?></td>
            <td><?php echo $sers->getExaminationRepprovedSubject()->getExaminationRepproved(); ?></td>
            <td><?php echo $sers->getValueString(); ?></td>
          </tr>
        <?php endforeach; ?>
      <?php endforeach; ?>
    </tbody>
  </table>
</div>



