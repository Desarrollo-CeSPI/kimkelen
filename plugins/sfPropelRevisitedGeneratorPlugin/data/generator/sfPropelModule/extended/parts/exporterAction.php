  public function executeDoExportation(sfWebRequest $request)
  {
    $this->pageNumber       = $request->getParameter('page');

    if (empty($this->pageNumber))
    {
      $this->getUser()->setFlash('error', 'There was an error while trying to export the desired page.');
      $this->redirect('@<?php echo $this->getModuleName()?>');
    }
    else
    {
      $this->exportationPager = $this->getExportationPager($this->configuration->getExportationType(), $this->pageNumber);

      $helperKlass    = $this->configuration->getExportationHelperClass();
      $exporterType   = $this->configuration->getExportationType();
      $this->exportationHelper = new $helperKlass($this->configuration, $this->getExportationResults($this->exportationPager), array('type' => $exporterType, 'context' => $this->getContext(), 'title' => $this->configuration->getExportationTitle(), 'headers' => $this->configuration->getExportationHeaders()));

      $this->exportationHelper->build();
      $this->exportationHelper->saveFile($this->configuration->getExportationSavePath().time().rand(1,1000).'.'.$this->configuration->getExportationFileExtension());
      $this->exportationHelper->freeMem();
      $this->content = $this->exportationHelper->getFileContents();
      $this->exportationHelper->deleteFile();
      $this->prepareResponseForExportation($this->configuration->getExportationFileExtension());

      return $this->renderText($this->content);
    }
  }

  public function executeDoExportationPages(sfWebRequest $request)
  {
    $this->exportationPager = $this->getExportationPager($this->configuration->getExportationType());
    return $this->renderPartial('<?php echo $this->getModuleName() ?>/exportation_pages', array('pager' => $this->exportationPager, 'exportUrl' => 'doExportation'));
  }

  public function executeNewUserExportation(sfWebRequest $request)
  {
    $this->form  = $this->configuration->getExportationForm(array(), array('pager' => $this->getExportationPager(), 'configuration' => $this->configuration));
  }

  public function executeCreateUserExportation(sfWebRequest $request)
  {
    $this->pager = $this->getExportationPager();
    $this->form  = $this->configuration->getExportationForm(array(), array('pager' => $this->pager, 'configuration' => $this->configuration));

    $this->form->bind($request->getParameter($this->form->getName()), $request->getFiles($this->form->getName()));
    if ($this->form->isValid())
    {
      $values = $this->form->getValues();
      if ( $this->form->isCSRFProtected() )
      {
        $values = array_merge($values,array($this->form->getCSRFFieldName() => $this->form->getCSRFToken() ));
      }
      $this->getUser()->setAttribute('<?php echo $this->getModuleName() ?>.exportation_form_values', $values);
      $this->setTemplate('createUserExportation');
    }
    else
    {
      $this->setTemplate('newUserExportation');
    }
  }

  public function getExportationFileExtension($form)
  {
    return $this->configuration->getExportationFileExtension($form->getExportationType());
  }

  public function executeProcessUserExportation(sfWebRequest $request)
  {
    $this->pageNumber            = $request->getParameter('page');
    $this->exportationFormValues = $this->getUser()->getAttribute('<?php echo $this->getModuleName() ?>.exportation_form_values', array());

    if (!empty($this->pageNumber) && !empty($this->exportationFormValues))
    {
      $this->form = $this->configuration->getExportationForm(array(), array('pager' => $this->getExportationPager(null, $this->pageNumber), 'configuration' => $this->configuration));
      $this->form->bind($this->exportationFormValues);
      if ($this->form->isValid())
      {
        $helperKlass    = $this->configuration->getExportationHelperUserClass();
        $exporterType   = $this->form->getExportationType();

        $this->exportationPager = $this->form->getExportationPager();

        $this->exportationHelper = new $helperKlass($this->configuration, $this->form->getExportationResults(), array('type' => $this->form->getExportationType(), 'context' => $this->getContext(), 'title' => $this->configuration->getExportationTitle(), 'headers' => $this->configuration->getExportationHeaders()), $this->form);

        $this->exportationHelper->build();
        $this->exportationHelper->saveFile($this->configuration->getExportationSavePath().time().rand(1,1000).'.'.$this->getExportationFileExtension($this->form));
        $this->exportationHelper->freeMem();
        $this->content = $this->exportationHelper->getFileContents();
        $this->exportationHelper->deleteFile();
        $this->prepareResponseForExportation($this->getExportationFileExtension($this->form));

        return $this->renderText($this->content);
      }
      else
      {
        $this->getUser()->setFlash('error', 'There was an error while trying to export the desired page.');
        $this->redirect('@<?php echo $this->getModuleName()?>');
      }
    }
    else
    {
      $this->getUser()->setFlash('error', 'There was an error while trying to export the desired page.');
      $this->redirect('@<?php echo $this->getModuleName()?>');
    }
  }

  public function getExportationResults($pager)
  {
    return $pager->getResults();
  }

  public function prepareResponseForExportation($extension)
  {
    sfConfig::set('sf_web_debug', false);
    $this->setLayout(false);
    
    $mimeType = $this->configuration->getExportationMimeType($extension);
    $this->getResponse()->setHttpHeader('Content-type', "$mimeType; charset=UTF-8");
    $this->getResponse()->setHttpHeader('Content-Disposition', ' attachment; filename="'.$this->configuration->getExportationFileName($extension).'"');
    $this->getResponse()->setHttpHeader('Cache-Control', ' maxage=3600');
    $this->getResponse()->setHttpHeader('Pragma', 'public');
  }
