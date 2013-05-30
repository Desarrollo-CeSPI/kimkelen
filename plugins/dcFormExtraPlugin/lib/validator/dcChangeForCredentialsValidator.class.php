<?php 
class dcChangeForCredentialsValidator extends sfValidatorBase
{
  protected function configure($options = array(), $messages = array())
  {
    $this->addRequiredOption('credentials');
    $this->addRequiredOption('widget_without_credentials');
    $this->addRequiredOption('widget_with_credentials');
  }

  private function hasCredential()
  {
    return sfContext::getInstance()->getUser()->hasCredential($this->getOption('credentials'));
  }


 /**
   * @see sfValidatorBase
   */
  protected function doClean($value)
  {
    $widget_without= $this->getOption('widget_without_credentials');
    $widget_with= $this->getOption('widget_with_credentials');
    if ($this->hasCredential()){
      $widget_with->clean($value);
    }else{
      $widget_without->clean($value);
    }
    return $value;
  }
}
