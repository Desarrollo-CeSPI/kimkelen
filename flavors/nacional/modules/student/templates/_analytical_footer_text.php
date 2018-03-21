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

<?php use_helper('Date') ?>

    <div class="header-text">
        <div class="footer-text">
        <?php if($student->getOriginSchool()):?>
          Certificado de Educación Primaria expedido por: <?php echo BaseCustomOptionsHolder::getInstance('SectorOriginSchoolType')->getStringFor($student->getOriginSchool()->getSector()) ?>  
          <?php echo ($student->getOriginSchool()->getSector() != SectorOriginSchoolType::SECTOR_UNLP)? ' - Provincia de ' .  $student->getOriginSchool()->getCity()->getDepartment()->getState() : ''?>
        <?php endif ?> 
        </div>
        <?php if ($analytical->has_completed_career()): ?>
            <div class="footer-text">Completó sus estudios secundarios. <?php echo $analytical->get_career_student()->getCareer()->getCareerName(); ?>. </div>
        <?php else: ?>
            <div class="footer-text">Para terminar sus estudios secundarios deberá aprobar:
            <?php if ($analytical->has_missing_subjects() ): ?>
                <?php $missing_subjects = $analytical->get_missing_subjects(); end($missing_subjects); $last_key = key($missing_subjects);?>
                <?php foreach ($analytical->get_missing_subjects() as $key => $subject): ?> <?php if (0!=$key): if ($key === $last_key): ?>y<?php else: ?>,<?php endif;endif; ?>
                <?php echo $subject->getSubjectName(); ?> de <?php echo __('Year '. $subject->getYear()); ?><?php if ($key === $last_key): ?>.<?php endif; ?>
                <?php endforeach; ?>
            <?php endif; ?>
            <?php if ($analytical->has_remaining_years() ): ?>
                Todo <?php echo implode(', ', array_map('__', $analytical->get_remaining_years_string())); ?>.
            <?php endif; ?>
            </div>
            <div class="footer-text">Certificado de Estudios Incompleto.</div>
        <?php endif; ?>
            <div class="footer-text">Para que conste y a pedido del interesado, se expide el presente certificado confrontado con los registros y actas originales por el <?php echo __('Departamento de alumnos'); ?>, en la ciudad de <?php echo __('escuela_ciudad'); ?>, a los <?php echo date('d'); ?> días del mes de <?php echo format_date(time(), 'MMMM'); ?> de <?php echo date('Y'); ?>.</div>

    </div>
