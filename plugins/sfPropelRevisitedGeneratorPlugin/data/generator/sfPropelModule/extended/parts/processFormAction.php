  protected function processForm(sfWebRequest $request, sfForm $form)
  {
    $form->bind($request->getParameter($form->getName()), $request->getFiles($form->getName()));
    if ($form->isValid())
    {
      $notice = $this->getProcessFormNotice($form->getObject()->isNew());

      $<?php echo $this->getSingularName() ?> = $form->save();

      $this->dispatcher->notify(new sfEvent($this, 'admin.save_object', array('object' => $<?php echo $this->getSingularName() ?>)));

      if ($request->hasParameter('_save_and_add'))
      {
        $this->setProcessFormSaveAndAddFlash($notice);

        $this->redirect('@<?php echo $this->getUrlForAction('new') ?>');
      }
      else
      {
        $this->getUser()->setFlash('notice', $notice);

        if($request->hasParameter('_save_and_list')){
          $this->redirect('@<?php echo $this->getUrlForAction('list') ?>');
        }
        else{
          $this->redirect('@<?php echo $this->getUrlForAction('edit') ?>?<?php echo $this->getPrimaryKeyUrlParams() ?>);
        }
      }
    }
    else
    {
      $this->setProcessFormErrorFlash();
    }
  }

  public function getProcessFormNotice($new)
  {
    return $new ? 'The item was created successfully.' : 'The item was updated successfully.';
  }

  public function setProcessFormErrorFlash()
  {
    $this->getUser()->setFlash('error', 'The item has not been saved due to some errors.', false);
  }

  public function setProcessFormSaveAndAddFlash($notice)
  {
    $this->getUser()->setFlash('notice', $notice.' You can add another one below.');
  }
