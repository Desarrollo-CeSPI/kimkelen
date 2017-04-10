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

<div class="row">
    <div class="col-md-12 student-actions text-right">

        <?php echo link_to('<span class="glyphicon glyphicon-star" aria-hidden="true"></span>',
                           'califications/showHistory?student_id=' . $student->getId() . '',
                           array('class' => 'btn btn btn-info',
                                 'data-toggle' => 'tooltip',
                                 'data-placement' => 'bottom',
                                 'title' => __('Califications')
                           ))
        ?>

        <?php echo link_to('<span class="glyphicon glyphicon-file" aria-hidden="true"></span>',
                           'student_attendance/index?student_id=' . $student->getId() . '',
                           array('class' => 'btn btn btn-warning',
                                 'data-toggle' => 'tooltip',
                                 'data-placement' => 'bottom',
                                 'title' => __('Attendance')
                           ))
        ?>

        <?php echo link_to('<span class="glyphicon glyphicon-exclamation-sign" aria-hidden="true"></span>',
                           'student_disciplinary_sanction/showHistory?student_id=' . $student->getId() . '',
                           array('class' => 'btn btn btn-danger',
                                 'data-toggle' => 'tooltip',
                                 'data-placement' => 'bottom',
                                 'title' => __('Disciplinary sanctions')
                           ))
        ?>

    </div>
</div>