<?php use_helper('I18N', 'Date') ?>
<?php use_stylesheet('/sfPropelRevisitedGeneratorPlugin/css/global.css') ?>
<?php use_stylesheet('/sfPropelRevisitedGeneratorPlugin/css/extended.css') ?>
<?php include_partial('pathway_commission/assets') ?>

<div id="sf_admin_container">
<?php include_partial('pathway_commission/flashes') ?>

  <div id="sf_admin_header">
  </div>

 <?php if (!empty($course_subjects)): ?>
  <h1><?php echo __('Course subjects for commission') ?></h1>
    <ul class="sf_admin_actions">
        <li class ="sf_admin_action_list"><?php echo link_to(__('Back'), $url); ?></li>
    </ul>
    <div id="related_courses">
      <table>
        <?php foreach ($course_subjects as $course_subject):?>
          <tr>
            <th><?php echo $course_subject ?></th>
            <?php $r = RecordPeer::retrieveByCourseOriginIdAndRecordType($course_subject->getId(), RecordType::COURSE); ?>        
            <td><?php echo (! is_null($r))? link_to(__($title), "course_student_mark/$action?id=" . $course->getId() . "&course_subject_id=" . $course_subject->getId()) : ''?></td>
          </tr>
        <?php endforeach?>
      </table>
    </div>
  <ul class="sf_admin_actions">
    <li class ="sf_admin_action_list"><?php echo link_to(__('Back'), $url); ?></li>
  </ul>
  <?php endif; ?>
</div>