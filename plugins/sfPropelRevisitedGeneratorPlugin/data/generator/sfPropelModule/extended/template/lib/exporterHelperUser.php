[?php

/**
 * <?php echo $this->getModuleName() ?> module exporter helper with user selection.
 *
 * @package    ##PROJECT_NAME##
 * @subpackage <?php echo $this->getModuleName()."\n" ?>
 * @author     ##AUTHOR_NAME##
 * @version    SVN: $Id: exporter.php 14891 2009-01-20 06:47:03Z dwhittle $
 */
class Base<?php echo ucfirst($this->getModuleName()) ?>ExporterHelperUser extends gmExporterHelperUser
{
  protected function getExporterSubclassPrefix()
  {
    return '<?php echo $this->getModuleName() ?>';
  }
}
