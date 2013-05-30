<?php if (!sfConfig::get('sfTCPDFPlugin_dir'))
{
  sfConfig::set('sfTCPDFPlugin_dir', sfConfig::get('sf_root_dir'). DIRECTORY_SEPARATOR. 'plugins'. DIRECTORY_SEPARATOR. 'sfTCPDFPlugin'. DIRECTORY_SEPARATOR. 'lib'. DIRECTORY_SEPARATOR. 'tcpdf'. DIRECTORY_SEPARATOR);
}
sfConfig::set('sfTCPDFPlugin_font_dir', sfConfig::get('sfTCPDFPlugin_dir'). 'fonts'. DIRECTORY_SEPARATOR);