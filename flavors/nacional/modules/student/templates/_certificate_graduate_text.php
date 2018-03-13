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
    Las autoridades del <?php echo SchoolBehaviourFactory::getInstance()->getSchoolName() ?> de la Universidad Nacional de La Plata, hace constar que
    <b><?php echo $student .' '. $student->getPerson()->getFullIdentification() ?> </b>
    completó sus estudios secundarios. <?php echo $student->getCareerStudent()->getCareer()->getCareerName()?>. Certificado analítico en trámite.
</p>