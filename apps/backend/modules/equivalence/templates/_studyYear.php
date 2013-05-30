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
<?php if (count($career_subject_school_years)): ?>
    <?php $style = "study_plan_" . ($year & 1 ? "odd" : "even") ?>
    <tr class="<?php echo $style ?>">
        <td width="10%" rowspan="<?php echo count($career_subject_school_years) + 1 ?>">
            <?php echo $year ?>
        </td>
        <?php include_partial('equivalence/careerSubjects',array('career_subject_school_years' => $career_subject_school_years, 'style' => $style,  "student" => $student, "year" => $year, "career" => $career,'career_school_year'=>$career_school_year)) ?>
    </tr>
<?php endif; ?>
