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
<?php use_helper('Date') ?>

<?php if ($object->is_empty()): ?>

  <div class="notice" style="padding: 20px; background-image: none; margin-bottom: 15px;">
    <?php echo __('The student has no approved subjects') ?>
  </div>

<?php else:?>

  <?php foreach ($object->get_years_in_career() as $year): ?>

    <table class="table gridtable_bordered">
      <thead>
        <tr>
          <th colspan="8" class="analytical_year"><?php echo __('Year ' . $year) ?></th>
        </tr>
        <tr>
          <th rowspan="2"><?php echo __("Condition") ?></th>
          <th rowspan="2"><?php echo __("Approval date") ?></th>
          <th rowspan="2"><?php echo __("Subject") ?></th>
          <th class="text-left" rowspan="2"><?php echo __("Asignatura") ?></th>
          <th colspan="2"><?php echo __("Calification") ?></th>
          <th rowspan="2" ><?php echo __("Tomo") ?></th>
          <th rowspan="2" ><?php echo __("Folio") ?></th>
        </tr>
        <tr>
          <th>Nro.</th>
          <th>Letras</th>
        </tr>
      </thead>
      <tbody class="analytical_body_table">
      <?php foreach ($object->get_subjects_in_year($year) as $css):?>
        <tr>
          <td class="text-center" width="5%"><?php echo ($css->getCondition()?$css->getCondition():'<hr/>') ?></td>
          <td class="text-center" width="10%"><?php echo ($css->getApprovedDate() ? ucwords(format_datetime($css->getApprovedDate()->format('U'),'dd/MM/yyyy')):'<hr/>') ?> </td>
          <td align="left" width="10%"><?php echo ($css->getOption() ? 'Optativa':'' ) ?></td>
          <td align="left" width="45%"><?php echo $css->getSubjectName() ?></td>
          <?php if( $css->getSubjectName() == 'Orientación Escolar'): ?>
                <td class="text-center" width="10%">----</td>
                <td class="text-center"><?php echo __('Sin calificaciones') ?></td>
          <?php else:?>
               <?php if(($css->getCourseSubjectStudent()->getNotAverageableCalification() == NotAverageableCalificationType::APPROVED && $css->getCourseSubjectStudent()->getIsNotAverageable())): ?>
                <td class="text-center" width="10%">Aprobado</td>
                <td class="text-center">Aprobado</td>
          <?php elseif( $css->getCourseSubjectStudent()->getIsNotAverageable()  && is_null($css->getCourseSubjectStudent()->getNotAverageableCalification())):?>
                <td class="text-center" width="10%">----</td>
                <td class="text-center"><?php echo __('Eximido') ?></td>
          <?php else:?>
                <td class="text-center" width="10%"><?php echo ($css->getMark()?$css->getMark():'<strong>'.__('Adeuda').'</strong>') ?></td>
                <td class="text-center"><?php echo ($css->getMarkAsSymbol()?$css->getMarkAsSymbol():'<strong>'.__('Adeuda').'</strong>') ?></td>
          <?php endif;?>
          <?php endif;?>
          <td width="10%"><?php echo (!is_null($css->getBookSheet())) ? $css->getBookSheet()->getBook(): '' ?></td>
          <td width="5%"><?php echo (!is_null($css->getBookSheet())) ? $css->getBookSheet()->getPhysicalSheet(): '' ?></td>
        </tr>
      <?php endforeach ?>
        <tr>
          <th colspan="6" style="text-align:left !important;"><?php echo __($object->get_str_year_status($year)) ?></th>
          <th colspan="2"><?php echo __('Average') ?>: <?php echo ( $object->get_year_average($year) ? number_format(round($object->get_year_average($year), 2), 2, '.', '') : '-') ;?>    </th>
        </tr>
      </tbody>
    </table>

  <?php endforeach ?>

  <?php if ($object->has_completed_career() || $career_student->getStudent()->canPrintGraduateCertificate()): ?>
    <div id="promedio_gral"><?php echo __('Promedio general'); ?>: <span id="promedio_gral_valor"><?php echo ($object->get_total_average()? number_format(round($object->get_total_average(), 2), 2, '.', '') :'-'); ?></span></div>
  <?php endif; ?>
<?php endif; ?>

