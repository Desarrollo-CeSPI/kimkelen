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
<?php include_javascripts_for_form($form) ?>
<?php use_helper('Form') ?>


<?php echo $form->renderHiddenFields()?>
<tr>
    <td><?php echo $form['mark']->renderLabel(); ?></td>

    <?php if ($career_subject->getHasOptions()): ?>
        <td><?php echo $form['school_year']; ?></td>
        <td><?php echo $form['optional']; ?></td>
    <?php else: ?>
        <td>-</td><td>-</td>
    <?php endif; ?>
    <td><?php echo $form['mark']; ?></td>
</tr>