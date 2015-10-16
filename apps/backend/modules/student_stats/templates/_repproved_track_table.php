<?php 
/*
 * Kimkëlen - School Management Software
 * Copyright (C) 2013 CeSPI - UNLP <desarrollo@cespi.unlp.edu.ar>
 *
 * This file is part of Kimkëlen.
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
<table class="print_table" cellspacing="0" id="export_to_excel" >
    <thead>
      <tr class="printColumns">
        <th rowspan="2"><?php echo __('Students'); ?></th>
        <th colspan="<?php echo count($career_subjects) ?>"><?php echo "Materias Bajas 1er Trimestre"/*echo __('Students');*/ ?></th>
      </tr>
      <tr>
        <?php foreach ($career_subjects as $i => $career_subject) : ?>
          <th colspan="1"><?php echo $career_subject->getSubject()->getFantasyName(); ?></th>
        <?php endforeach; ?>
      </tr>
    </thead>
    <tbody class="print_body">
      <?php foreach ($students as $student) : ?>
        <tr>
          <th><?php echo $student ?></th>
          <?php foreach ($courses as $i => $course): ?>
            <th><?php echo $course ?></th>
          <?php endforeach; ?>
        <?php endforeach; ?>
      </tr>
    </tbody>
  </table>