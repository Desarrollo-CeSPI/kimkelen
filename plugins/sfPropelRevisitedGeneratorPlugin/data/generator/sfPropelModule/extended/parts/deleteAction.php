  public function executeDelete(sfWebRequest $request)
  {
    $request->checkCSRFProtection();

    $this->dispatcher->notify(new sfEvent($this, 'admin.delete_object', array('object' => $this->getRoute()->getObject())));

    $this->getRoute()->getObject()->delete();

    $this->setDeleteFlash();

    $this->redirect('@<?php echo $this->getUrlForAction('list') ?>');
  }

  public function setDeleteFlash()
  {
    $this->getUser()->setFlash('notice', 'The item was deleted successfully.');
  }
