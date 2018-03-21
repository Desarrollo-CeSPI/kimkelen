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
<p>
    Las Autoridades del <?php echo SchoolBehaviourFactory::getInstance()->getSchoolName() ?> de la Universidad Nacional de La Plata, hace constar que
    <b><?php echo $student .' '. $student->getPerson()->getFullIdentification() ?> </b>
    cursó <b><?php echo ($student->getLastStudentCareerSchoolYearCursed()) ? $student->getLastStudentCareerSchoolYearCursed()->getYear() .'° año' : $student->getLastStudentCareerSchoolYear()->getYear() .'° año'?> </b> en el ciclo lectivo 
    <b> <?php echo ($student->getLastStudentCareerSchoolYearCursed()) ? $student->getLastStudentCareerSchoolYearCursed()->getCareerSchoolYear()->getSchoolYear()->getYear() : $student->getLastStudentCareerSchoolYear()->getCareerSchoolYear()->getSchoolYear()->getYear()?></b> 
			    
    <?php if(count ($p) == 0): ?>
	<?php echo "sin adeudar materias"?>
    <?php else:?>
		
	<?php echo 'adeudando ' ?>
	<b><?php echo $p[0]->getCourseSubject() .' de '.  $p[0]->getCourseSubject()->getCareerSubjectSchoolYear()->getCareerSubject()->getYear() .'° año' ?></b>
			
	<?php for($i= 1 ; $i < count($p)  ; $i++): ?>
	<b>
            <?php echo ($i == (count($p) -1)) ? 'y' : ',' ;?>
            <?php echo $p[$i]->getCourseSubject() .' de '.  $p[$i]->getCourseSubject()->getCareerSubjectSchoolYear()->getCareerSubject()->getYear() .'° año';?> 
	</b>
	<?php endfor?>
    <?php endif?>
    <?php echo '.'?>
</p>
