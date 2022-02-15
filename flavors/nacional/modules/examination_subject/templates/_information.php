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
<div class="info_div"><strong><?php echo __("Subject") ?></strong> <em><?php echo $examination_subject->getCareerSubjectSchoolYear()->getCareerSubject() ?> - <?php echo $examination_subject->getCareerSubjectSchoolYear()->getCareerSubject()->getCareer()->getCareerName()?></em></div>
<div class="info_div" style="color:red"><?php echo $examination_subject->getIsClosed()? __('Closed'):'' ?></div>


