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
<div class="report-header">
	<div class="header_row">
		<div class="title" id="header_analytical_data_left">
		</div>
                <div class="title" id="header_analytical_data_center">
                    <div class="analytical_info">
                      	<div class="analytical_form">
                            <?php echo ($form) ? $form['certificate_number']->renderRow() : '' ?>
                        </div>
                        <div class="analytical_form_info">   
                            <?php echo __('Certificado N°'); ?>
                            <span class="detail"><?php echo (isset($analytic) && $analytic->getCertificateNumber()?$analytic->getCertificateNumber():__('S/N')); ?></span> 	
			</div>
                        <div class="analytic_info">
                            <label><?php echo __("Legajo N°") ?>: </label>
                            <span class="detail"><?php echo $career_student->getStudent()->getGlobalFileNumber(); ?></span>
			</div>
                        <div class="analytic_info">
                            <label><?php echo __("Curso") ?>: </label>
                            <span class="detail">
                                <?php $scsy = $career_student->getStudent()->getLastStudentCareerSchoolYear();?>
                                <?php if ($scsy->getStatus() == StudentCareerSchoolYearStatus::WITHDRAWN ):?>
                                    <?php $d = $career_student->getStudent()->getLastStudentCareerSchoolYearCoursed()->getDivisions(); echo ($d[0]) ? str_replace(" ", "°", $d[0]) . " " .$career_student->getStudent()->getStudentOrientationString() :'';?></span>
                                <?php else: ?>
                                    <?php $d = $scsy->getDivisions(); echo ($d[0]) ? str_replace(" ", "°", $d[0]) . " " .$career_student->getStudent()->getStudentOrientationString() :'';?></span>
                                <?php endif;?>
                                
			</div>
                    </div>
                    
                     <?php echo image_tag("kimkelen_logo_analitico.png", array( 'class'=>'school_logo', 'absolute' => true, 'width' => 390, 'height' => 70)) ?>
		</div>
		<div id="header_analytical_data_right">	    
		</div>
	</div>

	<div class="header_row">
                <?php if(isset($dipregep_number)):?>
                    <?php include_partial('analytical_header_text', array('student' => $career_student->getStudent(), 'career_student' => $career_student,'form' => $form, 'dipregep_number' => $dipregep_number)) ?>
                <?php else: ?>
		<?php if (!isset($form)) $form = null; ?>
                    <?php include_partial('analytical_header_text', array('student' => $career_student->getStudent(), 'career_student' => $career_student,'form' => $form)) ?>
                <?php endif;?>
	</div>
</div>
