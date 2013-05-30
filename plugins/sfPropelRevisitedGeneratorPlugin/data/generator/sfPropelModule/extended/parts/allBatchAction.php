  public function executeAllBatch(sfWebRequest $request)
  {
    $request->checkCSRFProtection();

    $peer_method = $this->configuration->getPeerMethod();
    $objects = call_user_func(array("<?php echo constant($this->getModelClass().'::PEER') ?>", $peer_method), $this->buildCriteria());

    if (!count($objects))
    {
      $this->getUser()->setFlash('error', 'You must at least select one item.');

      $this->redirect('@<?php echo $this->getUrlForAction('list') ?>');
    }

    if (!$action = $request->getParameter('all_batch_action'))
    {
      $this->getUser()->setFlash('error', 'You must select an action to execute on the selected items.');

      $this->redirect('@<?php echo $this->getUrlForAction('list') ?>');
    }

    if (!method_exists($this, $method = 'execute'.ucfirst($action)))
    {
      throw new InvalidArgumentException(sprintf('You must create a "%s" method for action "%s"', $method, $action));
    }

    if (!$this->getUser()->hasCredential($this->configuration->getCredentials($action)))
    {
      $this->forward(sfConfig::get('sf_secure_module'), sfConfig::get('sf_secure_action'));
    }

    // execute batch
    $this->$method($request, $objects);

    $this->redirect('@<?php echo $this->getUrlForAction('list') ?>');
  }
