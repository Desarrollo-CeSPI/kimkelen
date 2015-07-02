<?php

/**
 * pmPDFKit provides a wrapper to wkhtmltopdf. Also, is a wkhtmltopdf clone.
 *
 * @package    pmPDFKitPlugin
 * @subpackage lib
 * @author     Patricio Mac Adden <pmacadden@cespi.unlp.edu.ar>
 */
class pmPDFKit
{
  protected
    $executable = "",
    $content = null,
    $stylesheets = array(),
    $switches = array(),
    $options = array();
  
  public function __construct($content, $stylesheets, $switches = array(), $options = array())
  {
    $this->setContent($content);
    $this->setStylesheets($stylesheets);
    
    $this->switches = $switches;
    $this->options = $options;
    
    $this->setExecutable(sfConfig::get('app_pm_pdf_kit_executable', '/usr/local/bin/wkhtmltopdf'));
  }
  
  public function setContent($content)
  {
    $this->content = $content;
  }
  
  public function getContent()
  {
    return $this->content;
  }
  
  public function setExecutable($executable)
  {
    $this->executable = $executable;
  }
  
  public function getExecutable()
  {
    return $this->executable;
  }
  
  public function setStylesheets($stylesheets)
  {
    $this->stylesheets = $stylesheets;
  }
  
  public function getStylesheets()
  {
    return $this->stylesheets;
  }
  
  /**
   * Returns the command beign executed.
   *
   * @return string The command being executed.
   */
  protected function getCommand()
  {
    $args = pmPDFKitOptions::parse($this->switches, $this->options);
    
    /*
     * read from stdin and write to stdout
     */
    return "{$this->getExecutable()} {$args} - -";
  }
  
  /**
   * Returns the pdf as it's string.
   *
   * @return mixed The PDF or false on error.
   */
  public function toPDF()
  {
    if (!file_exists($this->getExecutable()))
    {
      throw new LogicException("The executable ({$this->getExecutable()}) does not exist. Please install wkhtmltopdf.");
    }
    
    $this->appendStylesheets();
    
    $descriptorspec = array(
      0 => array("pipe", "r"), // stdin
      1 => array("pipe", "w"), // stdout
      2 => array("pipe", "w"), // stderr
    );
    
    $proc = proc_open($this->getCommand(), $descriptorspec, $pipes);
    
    if (is_resource($proc))
    {
      // close stdin
      fwrite($pipes[0], $this->content);
      fclose($pipes[0]);
      
      // close stdout
      $stdout = stream_get_contents($pipes[1]);
      fclose($pipes[1]);
      
      // close stderr
      $stderr = stream_get_contents($pipes[2]);
      fclose($pipes[2]);
      
      $retval = proc_close($proc);
      
      return $stdout;
    }
    
    return false;
  }
  
  /**
   * Saves the pdf in the filesystem.
   *
   * @param string $filename The filename where to put the PDF.
   */
  public function toFile($filename)
  {
    file_put_contents($filename, $this->toPDF());
  }
  
  /**
   * Appends the stylesheets to the content.
   */
  protected function appendStylesheets()
  {
    foreach ($this->getStylesheets() as $stylesheet => $opts)
    {
      $this->setContent(preg_replace("/(<\/head>)/", $this->getStyleTagFor($stylesheet)."</head>", $this->getContent()));
    }
  }
  
  /**
   * Adds the <style> tag for the given $stylesheet.
   *
   * @param string $stylesheet
   * @return string
   */
  protected function getStyleTagFor($stylesheet)
  {
    sfContext::getInstance()->getConfiguration()->loadHelpers(array("Asset"));
    
    return sprintf("<style>%s</style>", file_get_contents(stylesheet_path($stylesheet, true)));
  }
}