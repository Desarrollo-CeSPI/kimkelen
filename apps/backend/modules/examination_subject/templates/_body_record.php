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

  
<?php $evaluator_instance = SchoolBehaviourFactory::getEvaluatorInstance();?>
<table class="gridtable_bordered">
    <thead>
      <tr class="printColumns">
        <th class="orden" rowspan="2"><?php echo __('N°'); ?> </th>
        <th rowspan="2"><?php echo __('Apellido y Nombre'); ?></th>
        <th class="division_record" rowspan="2"><?php echo __('Division'); ?></th>
        <th colspan="2"><?php echo __('Mark'); ?></th>
        <th class="result_record" rowspan="2"><?php echo __('Resultado'); ?></th>
      </tr>
      <tr>
        <th class="calification_number" colspan="1"><?php echo __('Números'); ?></th>
        <th class="calification_letter" colspan="1"><?php echo __('Letras'); ?></th>
      </tr>
    </thead>
    <tbody>
      <?php $i = 1; ?>
      <?php foreach ($record->getRecordDetailsForSheet($rs->getSheet()) as $rd): ?>
        <tr>
          <td><?php echo $rd->getLine() ?> </td>
          <td class="student"><?php echo $rd->getStudent() ?> <?php if($rd->getOwesCorrelative()): ?> <span class="owes_correlative"><?php echo "(". __('Owes correlative') . ")" ?></span> <?php endif; ?></td>
          <td> <?php echo $rd->getDivision() ?> </td>
          <td>
               <?php if(!$rd->getIsNotAverageable()): ?>
                  <?php echo ($rd->getMark())? $rd->getMark(): ''; ?>
               <?php else: ?>
                  <?php if ($rd->getResult() == NotAverageableCalificationType::APPROVED): ?>
                       <?php echo "Trayectoria completa"; ?>
                  <?php else: ?>
                       <?php echo "Trayectoria en curso"; ?>
                  <?php endif; ?>
               <?php endif; ?>
          </td>
          <td>
            <?php $c = new num2text();?>
            <?php if(!$rd->getIsAbsent()):?>
               <?php echo $c->num2str($rd->getMark()) ?>
              <?php else: ?>
                <?php echo __('Absent'); ?>
              <?php endif; ?>
          </td>
          <td> <?php echo $evaluator_instance->getResultStringFor($rd->getResult()) ?></td>
        </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
