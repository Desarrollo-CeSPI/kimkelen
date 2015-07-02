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
<?php $has_students = $division->countStudents()?>
<span class="attribute <?php !$has_students and print 'disabled' ?>"><?php echo __('Tiene alumnos inscriptos') ?> <?php echo ($has_students ) ? "(".$has_students .") <em>No será posible eliminar la división</em>" : "" ?></span>

  <?php if ($has_students): ?>

    <?php echo link_to_function(__('&gt; Ver alumnos'),
    "jQuery.ajax({
      url: '". url_for("@division_show_students?id=" . $division->getId()) ."',
      success: function (data)
      {
        var element = jQuery('#division_students_" . $division->getId() . "');
        element.html(data);
        element.show();
        jQuery('#division_students_" . $division->getId() . "_show').show();
        jQuery('#division_students_" . $division->getId() . "_ajax_show').hide();
      }
    });",
    array('class' => 'show-more-link' , 'id' => "division_students_".$division->getId()."_ajax_show", 'title' => __('Desplegar todos los alumnos inscriptos en ésta división'))) ?>

    <?php echo link_to_function(__('&gt; Ver alumnos'),"jQuery('#division_students_".$division->getId()."').toggle();",array('class' => 'show-more-link', 'id' => "division_students_".$division->getId()."_show", 'style' => 'display: none', 'title' => __('Desplegar todos los alumnos inscriptos en esta división'))) ?>

    <div id="division_students_<?php echo $division->getId()?>" style="display: none;" class="more_info">
    </div>
  <?php endif ?>