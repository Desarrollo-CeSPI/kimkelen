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
<?php use_helper('Javascript') ?>

<div class='info_div'><strong><?php echo $career_subject->getSubject()?></strong></div>
<div class='info_div'><em><strong>Año de la carrera:</strong> <?php echo $career_subject->getYear()?></em></div>

<?php if ($career_subject->getHasChoices()):?>
  <div id="career_subject_options_text<?php echo $career_subject->getId()?>">
    <span class="attribute"><?php echo __('Es optativa') ?></span>
  </div>
<?php endif?>

<?php if ((! is_null($career_subject_school_year)) && ($career_subject_school_year->getSubjectConfiguration())): ?>
  <div id="career_subject_configuration_text<?php echo $career_subject_school_year->getId()?>">
    <span class="attribute"> <?php echo   __('Has own configuration')  ?>  </span>
  </div>
<?php endif?>
<?php if ((! is_null($career_subject_school_year))): ?>
  <div id="career_subject_sorted<?php echo $career_subject_school_year->getId()?>">
    <span class="attribute"> <?php echo   __('Sort %index_sort%' , array('%index_sort%' => $career_subject_school_year->getIndexSort()))  ?>  </span>
  </div>
<?php endif?>

<?php if ($career_subject->getIsChoice()):?>
  <div>
    <span class="attribute">
      <?php echo __('Es opción de optativa') ?>
    </span>
  </div>
<?php endif?>

<?php if ($career_subject->hasCorrelatives()):?>
  <div id="career_subject_correlative_text<?php echo $career_subject->getId()?>">
    <span class="attribute""></span>
      <?php echo link_to_function(__('&gt; Ver correlativas'), "jQuery('#career_subject_correlatives_".$career_subject->getId()."').toggle()", array('class' => 'show-more-link', 'title' => __('Desplegar todas las correlativas para esta materia'))) ?>

      <ul id="career_subject_correlatives_<?php echo $career_subject->getId()?>" style="display: none;" class="more_info">
        <?php foreach ($career_subject->getCorrelativeCareerSubjects() as $cs): ?>
          <li><?php echo $cs?></li>
        <?php endforeach ?>
      </ul>
  </div>
<?php endif ?>

<?php if (!is_null($career_subject_school_year) && ($career_subject->getHasOptions()) && $career_subject_school_year->hasChoices()):?>
  <div id="career_subject_options_text<?php echo $career_subject_school_year->getId()?>">
    <span class="attribute>"><?php echo __('Tiene opciones') ?></span>
    <?php echo link_to_function(__('&gt; Ver opciones'), "jQuery('#career_subject_options_".$career_subject_school_year->getId()."').toggle()", array('class' => 'show-more-link', 'title' => __('Desplegar todas las opciones para esta materia'))) ?>

    <ul id="career_subject_options_<?php echo $career_subject_school_year->getId()?>" style="display: none;" class="more_info">
      <?php foreach ($career_subject_school_year->getChoices() as $cs): ?>
        <li><?php echo $cs?></li>
      <?php endforeach ?>
    </ul>
  </div>
<?php endif?>
<?php if($career_subject->getOrientation()):?>
<div id="career_subject_orientation<?php echo $career_subject->getId()?>">
  <span class="attribute <?php is_null($career_subject->getOrientation()) and print 'disabled' ?>"><?php echo __('With orientation') ?></span>
      <span class="show-more-link">
        <?php echo $career_subject->getOrientation()?>
      </span>
</div>
<?php endif;?>
<?php if ($career_subject->getSubOrientation()):?>
<div id="career_subject_sub_orientation<?php echo $career_subject->getId()?>">
  <span class="attribute <?php is_null($career_subject->getSubOrientation()) and print 'disabled' ?>"><?php echo __('With sub orientation') ?></span>
      <span class="show-more-link">
        <?php echo $career_subject->getSubOrientation()?>
      </span>
</div>
<?php endif;?>