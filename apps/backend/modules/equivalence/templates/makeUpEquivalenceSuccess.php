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
<?php use_helper('I18N') ?>
<?php use_helper('Javascript') ?>
<?php use_javascript('study_plan.js') ?>
<?php include_partial("$module/assets") ?>
<div id="sf_admin_container">
    <div>
        <h1><?php echo __("Plan de estudio de ") . $career->getCareerName() . ' (' . $career->getPlanName() . ')' ?></h1>
    </div>
    <ul class="sf_admin_actions">
        <li class="sf_admin_action_list">
            <?php echo link_to(__('Volver al listado de carreras', array(), 'messages'), '@equivalence', array()) ?>
        </li>
    </ul>
    <div id='content' style='font-size: 12px;'>
        <div id='sf_admin_container'>  
            <form  action="<?php echo url_for('equivalence/updateEquivalence') ?>" method="post">
                <input type="hidden" value = "<?php echo $career_school_year->getId() ?>" name ="career_school_year_id">
                <table class="study_plan">
                    <thead>
                        <tr>
                            <th style="width:10%;"><?php echo __('Year') ?></th>
                            <th style="width:30%"><?php echo __('Subject') ?></th>
                            <th style="width:30%"><?php echo __('School year') ?></th>
                            <th style="width:30%"><?php echo __('Options') ?></th>
                            <th style="width:30%"><?php echo __('Mark') ?></th>
                        </tr>
                    </thead> 
                    <tbody>
                        <?php foreach ($years as $year): ?>
                            <?php if (count($career_subject_school_years)): ?>
                                <?php $style = "study_plan_" . ($year & 1 ? "odd" : "even") ?>
                                <tr class="<?php echo $style ?>">
                                    <td width="10%" rowspan="<?php echo count($career_subject_school_years[$year]) + 1 ?>">
                                        <?php echo $year ?>
                                    </td>
                                    <?php foreach ($career_subject_school_years[$year] as $career_subject): ?>
                                        <?php include_javascripts_for_form($forms[$career_subject->getId()]) ?>
                                        <?php use_helper('Form') ?>
                                        <?php echo $forms[$career_subject->getId()]->renderHiddenFields() ?>
                                    <tr>
                                        <td><?php echo $forms[$career_subject->getId()]['mark']->renderLabel(); ?></td>
                                        <td><?php echo $forms[$career_subject->getId()]['school_year']->renderError(); ?>
                                            <?php echo $forms[$career_subject->getId()]['school_year']; ?></td>
                                        <?php if ($career_subject->getHasOptions()): ?>
                                            <td><?php echo $forms[$career_subject->getId()]['optional']->renderError(); ?>
                                                <?php echo $forms[$career_subject->getId()]['optional']; ?></td>
                                        <?php else: ?>
                                            <td>-</td>
                                        <?php endif; ?>
                                        <td><?php echo $forms[$career_subject->getId()]['mark']->renderError(); ?>
                                            <?php echo $forms[$career_subject->getId()]['mark']; ?></td>
                                    </tr>
                                <?php endforeach; ?>                          
                                </tr>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    </tbody>
                </table>
                <input type="submit" name="_save" value="Guardar">
            </form>  
            <ul class="sf_admin_actions">
                <li class="sf_admin_action_list">
                    <?php echo link_to(__('Volver al listado de carreras', array(), 'messages'), "@$module", array()) ?>
                </li>
            </ul>
        </div>
    </div>
</div>