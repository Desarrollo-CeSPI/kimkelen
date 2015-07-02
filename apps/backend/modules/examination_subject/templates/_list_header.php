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
<?php $examination = $pager->getParameter('examination'); ?>
<h2>Mesa de examen: <?php echo $examination?></h2>

<ul>

		<div class="info-box">
			<div class="info-box-title">
				<strong><?php echo link_to_function(__("Create examinations"), "jQuery('#not_created_examination_subjects').toggle();") ?></strong>
			</div>

			<div class="info-box-collapsable" style="display: block" id="not_created_examination_subjects" >

				<?php for ($i=1; $i <= CareerPeer::getMaxYear(); $i++): ?>
					<?php if ($examination->countExaminationSubjectsForYear($i) == 0 ): ?>
					  <li><?php echo link_to( __("Create examination subjects for %year%° year", array('%year%' => $i)), 'examination/createExaminationSubjects?year='. $i.'&id='.$examination->getId())?></li>
				  <?php endif; ?>
					<?php endfor; ?>
			</div>
		</div>
</ul>
