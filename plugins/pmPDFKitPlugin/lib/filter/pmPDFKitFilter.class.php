<?php

/**
 * sfFilter provides a way for you to intercept incoming PDF requests and if so, create a PDF with the response.
 *
 * @package    pmPDFKitPlugin
 * @subpackage filter
 * @author     Patricio Mac Adden <pmacadden@cespi.unlp.edu.ar>
 */
class pmPDFKitFilter extends sfFilter
{
  public function execute($filterChain)
  {
    // get the request and response objects
    $request = $this->getContext()->getRequest();
    $response = $this->getContext()->getResponse();
    
    // guess the request format and store it in a variable
    $format = $request->getRequestFormat();
    if ($format == "pdf")
    {
      // set the request format as html
      $request->setRequestFormat("html");
    }
    
    // execute next filter
    $filterChain->execute();

    if ($format == "pdf")
    {
      // checks if the user is authenticated
      if (
            ((sfConfig::get('sf_login_module') == $this->context->getModuleName()) && (sfConfig::get('sf_login_action') == $this->context->getActionName())
          ||
            (sfConfig::get('sf_secure_module') == $this->context->getModuleName()) && (sfConfig::get('sf_secure_action') == $this->context->getActionName()))
          &&
            !$this->context->getUser()->isAuthenticated())
      {
        if (sfConfig::get('sf_logging_enabled'))
        {
          $this->context->getEventDispatcher()->notify(new sfEvent($this, 'application.log', array(sprintf('Action "%s/%s" requires authentication, forwarding to "%s/%s"', $this->context->getModuleName(), $this->context->getActionName(), sfConfig::get('sf_login_module'), sfConfig::get('sf_login_action')))));
        }
        // the user is not authenticated
        $this->context->getController()->forward(sfConfig::get('sf_login_module'), sfConfig::get('sf_login_action'));
        throw new sfStopException();
      }
      // get the content...
      $content = $response->getContent();
      // ... and the stylesheets
      $stylesheets = $response->getStylesheets();
      
      $switches = pmPDFKitOptions::getSwitchesFromRequest($request);
      $options = pmPDFKitOptions::getOptionsFromRequest($request);
      
      // create a pmPDFKit instance
      $pdf_kit = new pmPDFKit($content, $stylesheets, $switches, $options);
      // and render the pdf
      $pdf = $pdf_kit->toPDF();
      
      // return the inline pdf
      $response->setHttpHeader('Pragma', 'public');
      $response->setHttpHeader('Cache-Control', 'public,must-revalidate,max-age=0');
      $response->setHttpHeader('Content-Type', 'application/pdf');
      $response->setHttpHeader('Content-Disposition', 'inline');
      $response->setContent($pdf);
    }
  }
}
