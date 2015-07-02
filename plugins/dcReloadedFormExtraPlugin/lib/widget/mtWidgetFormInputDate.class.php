<?php

/**
 * Input date widget based on JQuery UI Calendar component.
 * Must be validated with mtValidatorDateString.
 * jquery and jquery-ui are REQUIRED.
 *
 * @author Matías A. Torres <torresmat@gmail.com>
 */
class mtWidgetFormInputDate extends sfWidgetFormInput
{
  public function configure($options = array(), $attributes = array())
  {
    sfContext::getInstance()->getConfiguration()->loadHelpers(array('Asset','I18N'));
    
    parent::configure($options,$attributes);
    
    $this->addOption('locale', 'es');
    $this->addOption('config', '{}');
    $this->addOption('use_own_help', true);
    $this->addOption('own_help', __('Date format is "dd/mm/yyyy"'));
  }

  protected function getJavascriptCode($id)
  {
    $locale = $this->getOption('locale');
    $config = $this->getOption('config');
    return "
      jQuery(function() {
        var params = {
            closeText: 'Cerrar',
            prevText: '&#x3c;Ant',
            nextText: 'Sig&#x3e;',
            currentText: 'Hoy',
            monthNames: [ 'Enero','Febrero','Marzo','Abril',
                          'Mayo','Junio', 'Julio','Agosto',
                          'Septiembre','Octubre','Noviembre','Diciembre'],
            monthNamesShort: ['Ene','Feb','Mar','Abr','May','Jun',
                              'Jul','Ago','Sep','Oct','Nov','Dic'],
            dayNames: ['Domingo','Lunes','Martes','Mi&eacute;rcoles','Jueves','Viernes','S&aacute;bado'],
            dayNamesShort: ['Dom','Lun','Mar','Mi&eacute;','Juv','Vie','S&aacute;b'],
            dayNamesMin: ['Do','Lu','Ma','Mi','Ju','Vi','S&aacute;'],
            dateFormat: 'dd/mm/yy', 
            firstDay: 0,
            isRTL: false
        };
        jQuery.datepicker.setDefaults(params);
        jQuery('#$id').datepicker($config);
      }); 
    ";
  }

  protected function renderOwnHelp()
  {
    return $this->getOption('use_own_help')? '<div class="help">'.$this->getOption('own_help').'</div>' : '';
  }

  public function render($name, $value = null, $attributes = array(), $errors = array())
  {
    sfContext::getInstance()->getConfiguration()->loadHelpers(array("JavascriptBase"));

    if (preg_match('/\d+-\d+-\d+/',$value))
    {
      $value = strtotime($value);
      if ($value !== false)
      {
        $value=date('d/m/Y',$value);
      }
    }
    return
        javascript_tag($this->getJavascriptCode($this->generateId($name))).
        $this->renderTag('input', array_merge(array(
            'type' => $this->getOption('type'),
            'name' => $name,
            'style'=> "width: 10em; text-align: right",
            'value' => $value), $attributes)).
        $this->renderOwnHelp();
  }
}
