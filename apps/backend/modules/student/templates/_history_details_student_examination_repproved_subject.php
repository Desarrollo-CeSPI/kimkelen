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
<div class="history_details">
  <h2><?php echo $student_examination_repproved_subject->getExaminationRepprovedSubject()->getExaminationRepproved()->getName() ?></h2>

  <div class="info_div">
    <strong><?php echo __("School year") ?></strong> <em><?php echo $student_examination_repproved_subject->getExaminationRepprovedSubject()->getExaminationRepproved()->getSchoolYear() ?></em>
  </div>
  <?php if ($student_examination_repproved_subject->getDate()): ?>
  <div class="info_div">
    <strong><?php echo __("Examination date") ?></strong> <em><?php echo $student_examination_repproved_subject->getDate('d/m/Y') ?></em>
  </div>
  <?php endif; ?>
  <?php if ($student_examination_repproved_subject->getFolioNumber()): ?>
  <div class="info_div">
    <strong><?php echo __("Folio number") ?></strong> <em><?php echo $student_examination_repproved_subject->getFolioNumber() ?></em>
  </div>
  <?php endif; ?>
    <?php if (!$student_examination_repproved_subject->getIsAbsent()): ?>
    <div class="info_div">
      <strong><?php echo __("Mark") ?></strong> <em><?php echo ($mark = $student_examination_repproved_subject->getMark()) ? $mark : "-" ?></em>
    </div>
  <?php else: ?>
    <div class="info_div">
      <strong><?php echo __("Is absent") ?></strong>
    </div>
  <?php endif ?>
  <div class="info_div">
    <strong><?php echo __("Status") ?></strong> <em><?php echo __($student_examination_repproved_subject->getExaminationRepprovedSubject()->getIsClosed() ? $student_examination_repproved_subject->getResultString() : "Repproved examination subject is not closed yet.") ?></em>
  </div>
</div>
