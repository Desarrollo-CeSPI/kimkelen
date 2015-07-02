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

  <span class="attribute <?php !$course->getStudents() and print 'disabled' ?>"><?php echo __('Tiene alumnos inscriptos') ?> <?php echo ($course->countStudents()) ? "(".$course->countStudents().")" : "" ?></span>


  <?php if ($course->getStudents()): ?>
  <?php echo link_to_function(__('&gt; Ver alumnos'),
    "jQuery.ajax({
      url: '". url_for("@course_show_students?id=" . $course->getId()) ."',
      success: function (data)
      {
        var element = jQuery('#course_students_" . $course->getId() . "');
        element.html(data);
        element.show();
        jQuery('#course_students_" . $course->getId() . "_show').show();
        jQuery('#course_students_" . $course->getId() . "_ajax_show').hide();
      }
    });",
    array('class' => 'show-more-link' , 'id' => "course_students_".$course->getId()."_ajax_show", 'title' => __('Desplegar todos los alumnos inscriptos en ésta comisión'))) ?>


  <?php echo link_to_function(__('&gt; Ver alumnos'), "jQuery('#course_students_".$course->getId()."').toggle()", array('class' => 'show-more-link','id' => "course_students_".$course->getId()."_show",'style' => 'display: none', 'title' => __('Desplegar todos los alumnos inscriptos en ésta comisión'))) ?>

    <div id="course_students_<?php echo $course->getId()?>" style="display: none;" class="more_info">

    </div>
  <?php endif ?>