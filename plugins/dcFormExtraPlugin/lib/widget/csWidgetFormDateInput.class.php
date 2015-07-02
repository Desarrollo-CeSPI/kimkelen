<?php
/**
 * Date Input widget based on JQuery UI Calendar component
 * It must be validated with mtValidatorDateString
 */
class csWidgetFormDateInput extends sfWidgetFormInput
{
  public function configure($options = array(), $attributes = array())
  {
    sfContext::getInstance()->getConfiguration()->loadHelpers(array('Asset','I18N'));
    parent::configure($options,$attributes);
    $this->addOption('locale','es');
    $this->addOption('use_own_help', true);
    $this->addOption('own_help', __('Date format is "dd/mm/yyyy"'));
    $this->addOption('change_year', false);
    $this->addOption('change_month', false);
    $this->addOption('year_range', (date('Y')-50).':'.(date('Y')+1));
  }

  public function getStylesheets()
  {
    return array('/dcFormExtraPlugin/css/jquery-ui-1.7.2.custom.css'=>'all');
  }

  public function getJavaScripts()
  {
    return array('/dcFormExtraPlugin/js/jquery-ui-1.7.2.custom.min.js');
  }

  protected function getJavascriptCode($id)
  {
    $locale=$this->getOption('locale');
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
            isRTL: false".($this->getOption('change_year')?', changeYear: true, yearRange:"'.$this->getOption('year_range').'"':'')."
            ".($this->getOption('change_month')?', changeMonth: true':'')."
        };
        jQuery.datepicker.setDefaults(params);
        jQuery('#$id').datepicker();
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
