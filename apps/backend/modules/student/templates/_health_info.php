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
<?php use_helper('I18N') ?>

<?php if ($student->getHealthInfoString() == HealthInfoStatus::HEALTH_INFO_NO_COMMITED): ?>
	<div class="health"><strong> <?php echo __("Health card not received") ;?></strong></div>
<?php elseif($student->getHealthInfoString() == HealthInfoStatus::HEALTH_INFO_NO_SUITABLE):?>
	<div class="health"><strong> <?php echo __("No suitable"); ?></strong></div>
<?php elseif($student->getHealthInfoString() == HealthInfoStatus::HEALTH_INFO_NO_SUITABLE_ACCIDENT):?>
	<div class="health"><strong> <?php echo $student->getHealthInfoString(); ?></strong></div>
<?php endif; ?>	