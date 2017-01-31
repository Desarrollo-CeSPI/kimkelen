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
<?php use_stylesheet('/bootstrap/css/bootstrap.css') ?>	
<?php use_stylesheet('/frontend/css/main.css') ?>
<?php use_javascript('/frontend/js/scripts.js','last') ?>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/2.2.4/jquery.min.js"></script>
<script src="//netdna.bootstrapcdn.com/bootstrap/3.3.6/js/bootstrap.min.js"></script>
<div class="row">
	<div class="col-md-1"></div>
	<div class="col-md-10">
		<div class="container-sombra-exterior container-califications">

			<div class="title-report-details title-califications" >
	          	<span class="title-sanctions"><?php echo __('Califications report') . ' |'; ?></span> 
	          	<span><?php echo $student  ?></span>
	      	</div>

		 	<ul class="nav nav-tabs" role="tablist">
				
				<?php $last_year= $student->getLastStudentCareerSchoolYear()->getCareerSchoolYear()->getSchoolYear()->getYear();?>
		 		<?php foreach ($student->getStudentCareerSchoolYears() as $student_career_school_year): ?>
			  		<?php $year= $student_career_school_year->getCareerSchoolYear()->getSchoolYear()->getYear(); ?>	
			  		<?php $class= ($student_career_school_year->getCareerSchoolYear()->getSchoolYear()->getIsActive() || ($year == $last_year))?'active' : ''; ?>
			  		
			  		
			  		<li role="presentation"class="<?php echo $class?>" ><a href="<?php echo '#' . $year ?>" aria-controls="home" role="tab" data-toggle="tab"><?php echo $year ?></a></li>
			 	<?php endforeach ?>                                                
            </ul>

                <div class="tab-content">
                	<?php foreach ($student->getStudentCareerSchoolYears() as $student_career_school_year): ?>
				    	<?php $year= $student_career_school_year->getCareerSchoolYear()->getSchoolYear()->getYear(); ?>
			  			<?php $class= ($student_career_school_year->getCareerSchoolYear()->getSchoolYear()->getIsActive() || ($year == $last_year))?'tab-pane active' : 'tab-pane'; ?>
			  	
                            <div role="tabpanel" class="<?php echo $class ?>" id="<?php echo $year ?>"> 

                            	<?php $divisions = $student->getCurrentDivisions($student_career_school_year->getCareerSchoolYearId())?>

							    <div class="col-md-12 pull-right">
							      <b><?php echo __('Year %%year%%',array('%%year%' => $student_career_school_year->getYear()))?></b>
							      <b><?php echo __("Status") .': '?></b> <?php echo $student_career_school_year->getStatusString() ?>
							    </div>
						    
						  		<?php $career_school_year = $student_career_school_year->getCareerSchoolYear(); ?>
						  		<?php $course_subject_students = $student_career_school_year->getCourses(); ?>
						  		<?php $career_student = CareerStudentPeer::retrieveByCareerAndStudent($career_school_year->getCareerId(), $student->getId()) ?>
						  		<?php $back_url = isset($back_url) ? $back_url : '' ?>

							  	<?php
							  	isset($course_subject_students['ANUAL']) ? include_partial("califications/current_course_subjects", array("course_subject_students" => $course_subject_students['ANUAL'],
							        'career_student' => $career_student,
							        'back_url' => $back_url,
							        'student' => $student,
							        'course_type' => CourseType::TRIMESTER)) : ''
							  	?>

						  		<?php
						  		isset($course_subject_students['QUATERLY']) ? include_partial("califications/current_course_subjects", array("course_subject_students" => $course_subject_students['QUATERLY'],
						        	'career_student' => $career_student,
						        	'back_url' => $back_url,
						        	'student' => $student,
						        	'course_type' => CourseType::QUATERLY)) : ''
						  		?>

						  		<?php
						  		isset($course_subject_students['BIMESTER']) ? include_partial("califications/current_course_subjects", array("course_subject_students" => $course_subject_students['BIMESTER'],
						        	'career_student' => $career_student,
						        	'back_url' => $back_url,
						        	'student' => $student,
						        	'course_type' => CourseType::BIMESTER)) : ''
						  		?>

						  		<?php
						  		isset($course_subject_students['QUATERLY_OF_A_TERM']) ? include_partial("califications/current_course_subjects", array("course_subject_students" => $course_subject_students['QUATERLY_OF_A_TERM'],
						    	    'career_student' => $career_student,
						    	    'back_url' => $back_url,
						    	    'student' => $student,
						    	    'course_type' => CourseType::QUATERLY_OF_A_TERM)) : ''
						  		?>

						  		<?php if ($anual_average = $student_career_school_year->getAnualAverage()): ?>
						    		<div class="info_div">
						     			<strong><?php echo __("Anual average") ?></strong> <em><?php echo $anual_average ?></em>
						    		</div>
								<?php endif; ?>	

                            </div>
                                        
                	<?php endforeach ?>
                </div>
		</div>	    
	</div>
	<div class="col-md-1"></div>
	<div class="col-md-12 container-buttons">
    	<?php echo button_to(__('Go back'), $link, array('class'=>'btn btn-default')) ?>
  	</div>
</div>
