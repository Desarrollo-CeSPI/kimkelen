<div class="title"><?php echo __('Career subject repproved details'); ?></div>
<div class="admonition_details">
  <table class="gridtable">
    <thead>
    <th><?php echo __('Subject'); ?></th>
    <th><?php echo __('Mark'); ?></th>
    <th><?php echo __('Instance'); ?></th>
    <th><?php echo __('Result'); ?></th>
    </thead>
    <tbody>
      <?php foreach ($examination_repproveds as $examination_repproved): ?>
        <tr>
          <td><?php echo $examination_repproved->getSubject() . ' ('. $examination_repproved->getCourseSubjectStudent()->getCourseSubject()->getCourse()->getSchoolYear() . ')' ?></td>
          <td><?php echo $examination_repproved->getMarksStr() ? $examination_repproved->getMarksStr() : '-' ?></td>
          <td><?php echo StudentExaminationRepprovedSubjectPeer::retrieveByStudentRepprovedCourseSubject($examination_repproved)->getExaminationRepprovedSubject()->getExaminationRepproved(); ?></td>
          <td><?php echo __(is_null($examination_repproved->getStudentApprovedCareerSubject())) ? 'Pendiente' : 'Aprobada' ?>
        </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
</div>