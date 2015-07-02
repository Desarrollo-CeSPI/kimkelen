<?php

/**
 * @author Patricio Mac Adden <pmacadden@cespi.unlp.edu.ar>
 */
abstract class pmFormFilterPropel extends sfFormFilterPropel
{
  public function setup()
  {
    pmFormCommon::setup($this);
  }
  
  public function unsetFields()
  {
    unset(
      $this["created_at"],
      $this["updated_at"]
    );
  }
  
  public function configureWidgets() {}
  
  public function configureValidators() {}
}