<?php 
/*
 * Kimkëlen - School Management Software
 * Copyright (C) 2013 CeSPI - UNLP <desarrollo@cespi.unlp.edu.ar>
 *
 * This file is part of Kimkëlen.
 *
 * Kimkëlen is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License v2.0 as published by
 * the Free Software Foundation.
 *
 * Kimkëlen is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with Kimkëlen.  If not, see <http://www.gnu.org/licenses/gpl-2.0.html>.
 */ ?>
<table style="width: 100%">
            <thead>
              <tr>
                <th><input id="sf_admin_list_batch_actions" type="checkbox" onClick="jQuery('.sf_admin_batch_checkbox').click()"></th>
                <th><?php echo __('Student') ?></th>
                <th><?php echo __('Day') ?></th>
                <th><?php echo __('Absence value') ?></th>
                <th><?php echo __('Actions') ?></th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($student_attendances as $student_attendance):?>
                <tr class="student_attendance<?php $student_attendance->getStudentAttendanceJustification() and print '_justificated'?>">
                  <td><input class="sf_admin_batch_checkbox" type="checkbox" value="<?php echo $student_attendance->getId()?>" name="ids[]"></td>
                  <td><?php echo $student_attendance->getStudent()?>  <?php include_partial('changelog', array('student_attendance_justification' =>$student_attendance->getStudentAttendanceJustification()))?>  </td>
                  <td><?php echo $student_attendance->getDay()?></td>
                  <td><?php echo $student_attendance->getValueString()?> </td>
                  <td>
                    <ul class="sf_admin_td_actions">
                      <li id="sf_admin_action_justificate"><?php echo link_to(__('Justificate'),'attendance_justification/justificate?id=' . $student_attendance->getId())?></li>
                      <?php if($student_attendance->getStudentAttendanceJustification() && $student_attendance->getStudentAttendanceJustification()->getDocument()):?>
                        <li id="sf_admin_action_justificate_download_document"><?php echo link_to(__('Download Document'),'mainBackend/downloableDocument?id=' . $student_attendance->getStudentAttendanceJustification()->getId())?></li>
                      <?php endif?>
                      <?php if($student_attendance->getStudentAttendanceJustification()
                              && $student_attendance->getStudentAttendanceJustification()->canDelete()
                              && $sf_user->hasCredential('edit_attendance_justification')):?>
                        <li class="sf_admin_action_delete"> <?php echo link_to(__('Delete'), 'attendance_justification/delete?id=' . $student_attendance->getStudentAttendanceJustification()->getId(), array('confirm' => 'Are you sure?'))?></li>
                      <?php endif?>
                    </ul>
                  </td>
                </tr>
              <?php endforeach;?>
            </tbody>
          </table>