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
 * Description of saveOutputFilter
 *
 * @author lucianoc
 */
class saveOutputFilter extends sfFilter
{

    public function execute($filterChain)
    {
        $filterChain->execute($filterChain);
        $action_name = $this->getContext()->getActionName();
        $action = $this->getContext()->getController()->getActionStack()->getLastEntry()->getActionInstance();
        $post_action_name = 'postExecute'.ucfirst($action_name);
        
        if (method_exists($action, $post_action_name))
        {
            $action->$post_action_name($this->getContext()->getRequest());
        }
        /*
        $format = $this->getContext()->getRequest()->getParameter('sf_format');
        */
    }

}
