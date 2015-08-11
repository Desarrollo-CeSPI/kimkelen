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
<style>
    #header_analytical_data_left
    {
        width: 20%;
    }
    #header_analytical_data_right
    {
        width: 20%;
        float: right
    }
    #header_analytical_data_center
    {
        width: 55%;
        text-align: center;
    }
    #header_analytical_data_center h1
    {
        display: inline;
    }
    .title dd.detail{
        font-weight: normal;
        padding-left: 1em;
    }
    #header_analytical_data_center h1 small
    {
        display: block;
        font-weight: normal;
    }
</style>

<div class="report-header">
    <div class="header_row">
        <div class="title" id="header_analytical_data_left">
            <dl class="dl-horizontal">
                <dt><?php echo __("Legajo N°") ?>:</dt>
                <dd class="detail"><?php echo $career_student->getStudent()->getFileNumber($career_student->getCareer()); ?></dd>
                <dt><?php echo __("Course") ?>:</dt>
                <dd class="detail"><?php echo $career_student->getStudent()->getCurrentDivisionsString() ?></dd>
            </dl>
        </div>
        <div class="title" id="header_analytical_data_center">
            <?php echo image_tag("kimkelen_logo_small.png", array('absolute' => true)) ?>
            <h1><?php echo __($career_student->getCareer()->getCareerName()) ?> <small><?php echo __("Universidad Nacional de La Plata") ?></small></h1>
        </div>
        <div id="header_analytical_data_right" class="title">
            <?php echo __('Certificado N°'); ?>
        </div>
    </div>

    <div class="header_row">
        <?php include_partial('analytical_header_text', array('student' => $career_student->getStudent(), 'career_student' => $career_student)) ?>
    </div>
</div>