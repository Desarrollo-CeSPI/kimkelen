<?php

/**
 * student actions.
 *
 * @package    symfony
 * @subpackage student
 * @author     Your name here
 * @version    SVN: $Id: actions.class.php 12479 2008-10-31 10:54:40Z fabien $
 */
class studentActions extends sfActions
{
 /**
  * Executes index action
  *
  * @param sfRequest $request A request object
  */
  public function executeIndex(sfWebRequest $request)
  {
    die("Acá habría que buscar en StudentTutorPeer solo a los id de student de aquellos que coincidan con el tutor_id del usuario y mostrar la info de ese/esos estudiantes con sus respectivas opciones");
    $this->forward('default', 'module');
  }
}
