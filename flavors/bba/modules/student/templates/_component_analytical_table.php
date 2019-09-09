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
                    <th colspan="9" class="text-center"><?php echo $year.'°'.__('Year') .' - ' .$object->get_career_name($year) ?></th>
                </tr>
                <tr>
                    <th rowspan="2"><?php echo __("Date") ?></th>
                    <th class="text-left" rowspan="2" colspan="4"><?php echo __("General Subjects") ?></th>
                    <th colspan="2" ><?php echo __("Calification") ?></th>
                    <th rowspan="2" ><?php echo __("Tomo") ?></th>
                    <th rowspan="2" ><?php echo __("Folio") ?></th>
                </tr>
                <tr>
                    <th>Nro.</th>
                    <th>Letras</th>
                </tr>
            </thead>
            <tbody class="analytical_body_table">
            <?php foreach ($object->get_general_subjects_in_year($year) as $css):?>
                <tr>
                    <td width="10%"><?php echo ($css->getApprovedDate() ? $css->getApprovedDate()->format('d/m/Y') : '<hr/>')?></td>
                    <td align="left" colspan="4" width="60%"><?php echo $css->getSubjectName() ?></td>
                    <td colspan="1"><?php echo ($css->getMark()?$css->getMark():'<strong>'.__('Adeuda').'</strong>') ?></td>
                    <td colspan="1"><?php echo ($css->getMarkAsSymbol()?$css->getMarkAsSymbol():'<strong>'.__('Adeuda').'</strong>') ?></td>
                    <td width="10%"><?php echo (!is_null($css->getBookSheet())) ? $css->getBookSheet()->getBook(): '' ?></td>
                    <td width="5%"><?php echo (!is_null($css->getBookSheet())) ? $css->getBookSheet()->getPhysicalSheet(): '' ?></td>
                </tr>
            <?php endforeach ?>
            </tbody>
         <?php if(!is_null($object->get_specific_subjects_in_year($year))):?>
            <thead>
                <tr>
                    <th class="text-left" colspan="9"><?php echo __("Specific Subjects") ?></th>
                </tr>
            </thead>
            <tbody class="analytical_body_table">
            <?php foreach ($object->get_specific_subjects_in_year($year) as $css):?>
                <tr>
                    <td width="10%"><?php echo ($css->getApprovedDate() ? $css->getApprovedDate()->format('d/m/Y') : '<hr/>')?></td>
                    <td align="left" colspan="4" width="60%"><?php echo $css->getSubjectName() ?></td>
                    <td class="" colspan="1"><?php echo ($css->getMark()?$css->getMark():'<strong>'.__('Adeuda').'</strong>') ?></td>
                    <td class="" colspan="1"><?php echo ($css->getMarkAsSymbol()?$css->getMarkAsSymbol():'<strong>'.__('Adeuda').'</strong>') ?></td>
                    <td width="10%"><?php echo (!is_null($css->getBookSheet())) ? $css->getBookSheet()->getBook(): '' ?></td>
                    <td width="5%"><?php echo (!is_null($css->getBookSheet())) ? $css->getBookSheet()->getPhysicalSheet(): '' ?></td>
                </tr>
            <?php endforeach ?>
            </tbody>
        <?php endif;?>
        <?php if(!is_null($object->get_suborientation_subjects_in_year($year))): ?>
            <thead>
                <tr>
                    <th class="text-left" colspan="9"><?php  echo ($student->getStudentSpecialityString()? __("%speciality% subjects", array("%speciality%" => $student->getStudentSpecialityString())): __("Suborientation Subjects"))?> </th>
                </tr>
            </thead>
            <tbody class="analytical_body_table">
            <?php foreach ($object->get_suborientation_subjects_in_year($year) as $css):?>
                <tr>
                    <td width="10%"><?php echo ($css->getApprovedDate() ? $css->getApprovedDate()->format('d/m/Y') : '<hr/>')?></td>
                    <td align="left" colspan="4" width="60%"><?php echo $css->getSubjectName() ?></td>
                    <td class="" colspan="1"><?php echo ($css->getMark()?$css->getMark():'<strong>'.__('Adeuda').'</strong>') ?></td>
                    <td class="" colspan="1"><?php echo ($css->getMarkAsSymbol()?$css->getMarkAsSymbol():'<strong>'.__('Adeuda').'</strong>') ?></td>
                    <td width="10%"><?php echo (!is_null($css->getBookSheet())) ? $css->getBookSheet()->getBook(): '' ?></td>
                    <td width="5%"><?php echo (!is_null($css->getBookSheet())) ? $css->getBookSheet()->getPhysicalSheet(): '' ?></td>
                </tr>
            <?php endforeach ?>
            </tbody>
        <?php endif; ?>
        <?php if(!is_null($object->get_optional_subjects_in_year($year))): ?>
            <thead>
                <tr>
                    <th class="text-left" colspan="9"><?php echo __("Optional Subjects") ?> </th>
                </tr>
            </thead>
            <tbody class="analytical_body_table">
            <?php foreach ($object->get_optional_subjects_in_year($year) as $css):?>
                <tr>
                    <td width="10%"><?php echo ($css->getApprovedDate() ? $css->getApprovedDate()->format('d/m/Y') : '<hr/>')?></td>
                    <td align="left" colspan="4" width="60%"><?php echo $css->getSubjectName() ?></td>
                    <td class="" colspan="1"><?php echo ($css->getMark()?$css->getMark():'<strong>'.__('Adeuda').'</strong>') ?></td>
                    <td class="" colspan="1"><?php echo ($css->getMarkAsSymbol()?$css->getMarkAsSymbol():'<strong>'.__('Adeuda').'</strong>') ?></td>
                    <td width="10%"><?php echo (!is_null($css->getBookSheet())) ? $css->getBookSheet()->getBook(): '' ?></td>
                    <td width="5%"><?php echo (!is_null($css->getBookSheet())) ? $css->getBookSheet()->getPhysicalSheet(): '' ?></td>
                </tr>
            <?php endforeach ?>
        <?php endif; ?>
        <?php if(!is_null($object->get_subjectsEOP_in_year($year))): ?>
            <thead>
                <tr>
                    <th class="text-left" colspan="9"><?php echo __("Asignaturas del Espacio Optativo de Profundización") ?> </th>
                </tr>
            </thead>
            <tbody class="analytical_body_table">
            <?php foreach ($object->get_subjectsEOP_in_year($year) as $css):?>
                <tr>
                    <td width="10%"><?php echo ($css->getApprovedDate() ? $css->getApprovedDate()->format('d/m/Y') : '<hr/>')?></td>
                    <td align="left" colspan="4" width="60%"><?php echo $css->getSubjectName() ?></td>
                    <td class="" colspan="1"><?php echo ($css->getMark()?$css->getMark():'<strong>'.__('Adeuda').'</strong>') ?></td>
                    <td class="" colspan="1"><?php echo ($css->getMarkAsSymbol()?$css->getMarkAsSymbol():'<strong>'.__('Adeuda').'</strong>') ?></td>
                    <td width="10%"><?php echo (!is_null($css->getBookSheet())) ? $css->getBookSheet()->getBook(): '' ?></td>
                    <td width="5%"><?php echo (!is_null($css->getBookSheet())) ? $css->getBookSheet()->getPhysicalSheet(): '' ?></td>
                </tr>
            <?php endforeach ?>
        <?php endif; ?>
            <tbody>
                <tr>
                    <th colspan="7" style="text-align:left !important;">Año: <?php echo $object->get_school_year($year) .' - '. __($object->get_str_year_status($year)) ?></th>
                    <th colspan="2"><?php echo __('Average') ?>: <?php echo ( $object->get_year_average($year) ? round($object->get_year_average($year), 2) : '-'); ?>    </th>
                </tr>
            </tbody>
        </table>
    <?php endforeach ?>

    <?php if ($object->has_completed_career()): ?>
        <div id="promedio_gral"><?php echo __('Promedio general'); ?>: <span id="promedio_gral_valor"><?php echo ($object->get_total_average()?round($object->get_total_average(),2):'-'); ?></span></div>
    <?php endif; ?>
<?php endif; ?>