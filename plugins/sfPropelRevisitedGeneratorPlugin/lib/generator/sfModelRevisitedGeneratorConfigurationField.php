<?php
/**
 * This class adds a different behaviour when column_name attribute
 * of a column is setted
 *
 * @author Christian A. Rodriguez <car at cespi.unlp.edu.ar>
 */
class sfModelRevisitedGeneratorConfigurationField extends sfModelGeneratorConfigurationField{
  public function isReal() {
    return parent::isReal() or isset($this->config['column_name']);
  }
}
?>
