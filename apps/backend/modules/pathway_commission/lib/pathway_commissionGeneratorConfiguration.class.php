<?php

/**
 * pathway_commission module configuration.
 *
 * @package    symfony
 * @subpackage pathway_commission
 * @author     Your name here
 * @version    SVN: $Id: configuration.php 12474 2008-10-31 10:41:27Z fabien $
 */
class pathway_commissionGeneratorConfiguration extends BasePathway_commissionGeneratorConfiguration
{

    /**
     * Gets the form class name.
     *
     * @return string The form class name
     */
    public function getFormClass()
    {
        return SchoolBehaviourFactory::getInstance()->getFormFactory()->getPathwayCommissionForm();
    }

}
