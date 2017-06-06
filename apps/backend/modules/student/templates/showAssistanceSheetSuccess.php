<?php /*
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
<?php use_helper('Javascript', 'Object', 'I18N', 'Asset') ?>
<?php use_stylesheet('/sfPropelRevisitedGeneratorPlugin/css/global.css') ?>
<?php use_stylesheet('/sfPropelRevisitedGeneratorPlugin/css/extended.css') ?>

<div id="sf_admin_container">
	<h1>
    <?php echo __("Assistance sheet for %student%", array("%student%" => $student)) ?>
	</h1>
	<ul class="sf_admin_actions">
        <li class ="sf_admin_action_list"><?php echo link_to(__('Back'), $back_url); ?></li>    
	</ul>
	<div id="sf_admin_content">
	<?php foreach ($student_career_school_years as $student_career_school_year): ?>
	<?php $school_year = $student_career_school_year->getSchoolYear(); ?>
		<?php foreach ($student_career_school_year->getDivisions() as $division): ?>
		<table style="width: 100%">
            <thead>
              <tr>
                <th><?php echo __('Day') ?></th>
                <th><?php echo __('Absence value') ?></th>
                <th><?php echo __('Is justified') ?></th>
                
                <th><?php echo __('Justification type id') ?></th>
                <th><?php echo __('Subject') ?></th>
				<th><?php echo __('Description') ?></th>
                <th><?php echo __('Actions') ?></th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($student->getAbsencesReport($student_career_school_year->getCareerSchoolYearId()) as $absence):?>
                <tr class="student_attendance<?php if($absence->hasJustification()) echo '_justificated'?>" >
                  <td><?php echo $absence->getFormattedDay();?></td>
                  <td><?php echo $absence->getValueString();?> </td>
                  <td><?php echo ($justification = $absence->getStudentAttendanceJustification()) ? 'Sí' : 'No' ?></td>
                  <td><?php echo ($type = $absence->getStudentAttendanceJustification()) ? $absence->getStudentAttendanceJustification()->getJustificationType() : '-' ?></td>
                  <td><?php echo ($course_subject = $absence->getCourseSubject()) ? $absence->getCourseSubject() : '-' ?></td>
                  <td><?php echo ($justification = $absence->getStudentAttendanceJustification()) ? $absence->getStudentAttendanceJustification()->getObservation() : '-' ?></td>
                  <td>
                    <ul class="sf_admin_td_actions">
						<?php if(!is_null($absence->getStudentAttendanceJustification()) && $absence->getStudentAttendanceJustification()->getDocument()):?>
							<li id="sf_admin_action_justificate_download_document"><?php echo link_to(__('Download Document'),'mainBackend/downloableDocument?id=' . $absence->getStudentAttendanceJustification()->getId())?></li>
						<?php endif;?>
                    </ul>
                  </td>
                </tr>
              <?php endforeach;?>
            </tbody>
         </table>
		<?php endforeach ?>
	<?php endforeach ?>
		<table style="width: 100%">
			<tfoot>
				<tr>
				  <td colspan="7" class="report-total">
					<?php echo __('Total') . ': ' . round($student->getTotalAbsencesReport($division->getCareerSchoolYearId(), false), 2) ?>
				  </td>
				</tr>
				<tr>
					<td colspan="7" class="report-total">
					<?php echo __('Total unjustified') . ': ' . round($student->getTotalAbsencesReport($division->getCareerSchoolYearId()), 2) ?>
					</td>
				</tr>
				<tr>
					<td colspan="7" class="report-total">
					<?php echo __('Total justified') . ': ' . round($student->getTotalJustificatedAbsencesReport($division->getCareerSchoolYearId()), 2) ?>
					</td>
				</tr>
			</tfoot>
		</table>
		<ul class="sf_admin_actions">
                    <li class ="sf_admin_action_list"><?php echo link_to(__('Back'), $back_url); ?></li>
		</ul>
	</div>
 </div>	
