<?php

/**
 * pmPDFKit provides a way to configure wkhtmltopdf parameter.
 *
 * @package    pmPDFKitPlugin
 * @subpackage lib
 * @author     Patricio Mac Adden <pmacadden@cespi.unlp.edu.ar>
 */
class pmPDFKitOptions
{
  protected static $switches = array(
    'collate',
    'no-collate',
    'grayscale',
    'lowquality',
    'no-pdf-compression',
    'outline',
    'no-outline',
    'background',
    'no-background',
    'custom-header-propagation',
    'no-custom-header-propagation',
    'default-header',
    'disable-external-links',
    'enable-external-links',
    'disable-forms',
    'enable-forms',
    'images',
    'no-images',
    'disable-internal-links',
    'enable-internal-links',
    'disable-javascript',
    'enable-javascript',
    'disable-local-file-access',
    'enable-local-file-access',
    'exclude-from-outline',
    'include-in-outline',
    'disable-plugins',
    'enable-plugins',
    'print-media-type',
    'no-print-media-type',
    'disable-smart-shrinking',
    'enable-smart-shrinking',
    'stop-slow-scripts',
    'no-stop-slow-scripts',
    'disable-toc-back-links',
    'enable-toc-back-links',
    'footer-line',
    'no-footer-line',
    'header-line',
    'no-header-line',
    'disable-dotted-lines',
    'disable-toc-links'
  );
  
  protected static $options = array(
    'cookie-jar',
    'copies',
    'dpi',
    'image-dpi',
    'image-quality',
    'margin-bottom',
    'margin-left',
    'margin-right',
    'margin-top',
    'orientation',
    'output-format',
    'page-height',
    'page-size',
    'page-width',
    'title',
    'outline-depth',
    'allow',
    'checkbox-checked-svg',
    'checkbox-svg',
    'cookie',
    'custom-header',
    'encoding',
    'javascript-delay',
    'load-error-handling',
    'minimum-font-size',
    'page-offset',
    'password',
    'post',
    'post-file',
    'proxy',
    'radiobutton-checked-svg',
    'radiobutton-svg',
    'run-script',
    'user-style-sheet',
    'username',
    'window-status',
    'zoom',
    'footer-center',
    'footer-font-name',
    'footer-font-size',
    'footer-html',
    'footer-left',
    'footer-right',
    'footer-spacing',
    'header-center',
    'header-font-name',
    'header-font-size',
    'header-html',
    'header-left',
    'header-right',
    'header-spacing',
    //'replace', // how to do this?
    'toc-header-text',
    'toc-level-indentation',
    'toc-text-size-shrink',
    'xsl-style-sheet'
  );
  
  public static function getSwitchesFromRequest(sfWebRequest $request)
  {
     include sfContext::getInstance()->getConfigCache()->checkConfig('config/pm_pdf_kit.yml');
    $switches = array();


    foreach (self::$switches as $switch)
    {
      if (sfConfig::get('pkp_switches_'.$switch, false)
       ||($request->hasParameter($switch) && ($request->getParameter($switch) == "true" || $request->getParameter($switch) == 1)))
      {
        $switches[] = $switch;
      }
    }

    return $switches;
  }
  
  public static function getOptionsFromRequest(sfWebRequest $request)
  {
   include sfContext::getInstance()->getConfigCache()->checkConfig('config/pm_pdf_kit.yml');
   $options = array();
    
    foreach (self::$options as $option)
    {
      if (!is_null($value = sfConfig::get('pkp_options_'.$option, null)))
      {
          $options[$option] = $value;
      }
      if ($request->hasParameter($option))
      {
        $options[$option] = $request->getParameter($option);
      }
    }
    return $options;
  }
  
  public static function parse($switches, $options)
  {
    $args = "";
    
    foreach ($switches as $sw)
    {
      $args .= "--$sw ";
    }
    
    foreach ($options as $k => $v)
    {
      $args .= "--$k $v ";
    }
    
    return $args;
  }
}
