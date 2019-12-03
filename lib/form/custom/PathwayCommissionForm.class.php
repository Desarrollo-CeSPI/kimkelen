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
 */

/**
 * The form for a pathway course
 *
 * @author lucianoc
 */
class PathwayCommissionForm extends CommissionForm
{
    public function configure()
    {
        parent::configure();
        $this->widgetSchema["school_year_id"] = new sfWidgetFormPropelChoice(array('model' => 'SchoolYear', 'add_empty' => false));
        $this->setWidget('evaluation_date', new csWidgetFormDateInput());
        $this->setValidator('evaluation_date', new mtValidatorDateString(array('required' => false)));
        
    }
    
    public function doSave($con = null)
    {
        $this->getObject()->setIsPathway(true);
        
        return parent::doSave($con);
    }
}
