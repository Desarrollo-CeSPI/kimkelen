[?php

/**
 * <?php echo $this->getModuleName() ?> module configuration.
 *
 * @package    ##PROJECT_NAME##
 * @subpackage <?php echo $this->getModuleName()."\n" ?>
 * @author     ##AUTHOR_NAME##
 * @version    SVN: $Id: helper.php 14891 2009-01-20 06:47:03Z dwhittle $
 */
class Base<?php echo ucfirst($this->getModuleName()) ?>GeneratorHelper extends sfModelGeneratorHelper
{
  public function fillSlotActionDefaults(&$params)
  {
    $params['label'] = (isset($params['label'])) ? $params['label'] : sfInflector::humanize($params['action']);
    $params['icon'] = (isset($params['icon'])) ? $params['icon'] : '<?php echo $this->getDefaultValue('slot.icon') ?>';
    $params['params']['class'] = (isset($params['params']['class'])) ? $params['params']['class'] : '<?php echo $this->getDefaultValue('slot.class') ?>';
    $params['params']['image_class'] = (isset($params['params']['image_class'])) ? $params['params']['image_class'] : '<?php echo $this->getDefaultValue('slot.image_class') ?>';
    $params['params']['text_class'] = (isset($params['params']['text_class'])) ? $params['params']['text_class'] : '<?php echo $this->getDefaultValue('slot.text_class') ?>';
    $params['query_string'] = (isset($params['query_string']) ? $params['query_string'] : array());
    $params['confirm'] = (isset($params['confirm']) ? $params['confirm'] : '');
  }

  public function getCurtainJavascript($params=array())
  {
    $js = '';
    if ((!isset($params['confirm'])) || (empty($params['confirm'])))
      $js = "document.body.appendChild(new Element('div', { 'style' :  'background: #fff none; height: 100%; width: 100%; position: absolute; top: 0; left: 0; filter: alpha(opacity=20); opacity: 0.2;' }));";
    return $js;
  }

  public function getSlotActionImage($params, $default = '<?php echo $this->getDefaultValue('slot.icon') ?>')
  {
    if (!isset($params['icon']) && is_null($default))
      return '';

    return image_tag((isset($params['icon']) ? $params['icon'] : $default), array('alt_title' => $params['label'], 'class' => $params['params']['image_class'])) . ' ';
  }

  public function getSlotActionText($params)
  {
    return '<span class="' . $params['params']['text_class'] . '">' . __($params['label'], array(), 'sf_admin') . '</span>';
  }

  public function slotActionToNew($params)
  {
    $params['action'] = 'new';
    $params['icon'] = (isset($params['icon']) ? $params['icon'] : '/sfPropelRevisitedGeneratorPlugin/images/icons/24x24/create.png');
    $this->fillSlotActionDefaults($params);


    return link_to($this->getSlotActionImage($params) . $this->getSlotActionText($params), $this->getUrlForAction('new'), $params['query_string'], $params['params']);
  }

  public function slotActionToEdit($object, $params)
  {
    $params['action'] = 'edit';
    $params['icon'] = (isset($params['icon']) ? $params['icon'] : '/sfPropelRevisitedGeneratorPlugin/images/icons/24x24/edit.png');
    $this->fillSlotActionDefaults($params);

    return link_to($this->getSlotActionImage($params) . $this->getSlotActionText($params), $this->getUrlForAction('edit'), $object, $params['params']);
  }

  public function slotActionToDelete($object, $params)
  {
    if ($object->isNew())
    {
      return '';
    }

    $params['action'] = 'delete';
    $params['icon'] = (isset($params['icon']) ? $params['icon'] : '/sfPropelRevisitedGeneratorPlugin/images/icons/24x24/delete.png');
    $this->fillSlotActionDefaults($params);

    return link_to($this->getSlotActionImage($params) . $this->getSlotActionText($params), $this->getUrlForAction('delete'), $object, array_merge($params['params'], array('method' => 'delete', 'confirm' => !empty($params['confirm']) ? __($params['confirm'], array(), 'sf_admin') : __('Are you sure you want to delete the object?', array(), 'sf_admin'))));
  }

  public function slotActionToList($params)
  {
    $params['action'] = 'list';
    $params['icon'] = (isset($params['icon']) ? $params['icon'] : '/sfPropelRevisitedGeneratorPlugin/images/icons/24x24/list.png');
    $this->fillSlotActionDefaults($params);

    return link_to($this->getSlotActionImage($params) . $this->getSlotActionText($params), $this->getUrlForAction('list'), $params['query_string'], $params['params']);
  }

  public function slotActionToSave($object, $params)
  {
    $params['action'] = 'save';
    $params['icon'] = (isset($params['icon']) ? $params['icon'] : '/sfPropelRevisitedGeneratorPlugin/images/icons/24x24/save.png');
    $this->fillSlotActionDefaults($params);

    return link_to_function($this->getSlotActionImage($params) . $this->getSlotActionText($params), $this->getCurtainJavascript($params) . $this->getJSSaveFunction('_save'), $params['params']);
  }

  public function slotActionToSaveAndAdd($object, $params)
  {
    $params['action'] = 'save_and_add';
    $params['icon'] = (isset($params['icon']) ? $params['icon'] : '/sfPropelRevisitedGeneratorPlugin/images/icons/24x24/save_and_add.png');
    $this->fillSlotActionDefaults($params);

    return link_to_function($this->getSlotActionImage($params) . $this->getSlotActionText($params), $this->getCurtainJavascript($params) . $this->getJSSaveFunction('_save_and_add'), $params['params']);
  }
  
  public function slotActionToSaveAndList($object, $params)
  {
    $params['action'] = 'save_and_list';
    $params['icon'] = (isset($params['icon']) ? $params['icon'] : '/sfPropelRevisitedGeneratorPlugin/images/icons/24x24/save_and_add.png');
    $this->fillSlotActionDefaults($params);

    return link_to_function($this->getSlotActionImage($params) . $this->getSlotActionText($params), $this->getCurtainJavascript($params) . $this->getJSSaveFunction('_save_and_list'), $params['params']);
  }

  public function getJSSaveFunction($action)
  {
    return "var form = document.getElementById('sf_admin_edit_form'); var submit_value = document.createElement('input'); submit_value.style.display = 'none'; submit_value.type='submit'; submit_value.name='$action'; form.appendChild(submit_value); submit_value.click();";
  }

  public function linkToNew($params)
  {
    return '<li class="sf_admin_action_new">'.link_to(__($params['label'], array(), 'sf_admin'), $this->getUrlForAction('new')).'</li>';
  }

  public function linkToEdit($object, $params)
  {
    return '<li class="sf_admin_action_edit">'.link_to(__($params['label'], array(), 'sf_admin'), $this->getUrlForAction('edit'), $object).'</li>';
  }

  public function linkToShow($object, $params)
  {
    return '<li class="sf_admin_action_show">'.link_to(__($params['label'], array(), 'sf_admin'), $this->getUrlForAction('show'), $object).'</li>';
  }

  public function linkToDelete($object, $params)
  {
    if ($object->isNew())
    {
      return '';
    }
    $options = array('method' => 'delete');
    if (!isset($params['confirm']))
    {
      $options['onclick'] = $this->getCurtainJavascript();
    }
    else
    {
      $options['confirm'] = __($params['confirm'], array(), 'sf_admin');
    }

    return '<li class="sf_admin_action_delete">'.link_to(__($params['label'], array(), 'sf_admin'), $this->getUrlForAction('delete'), $object, $options).'</li>';
  }

  public function linkToList($params)
  {
    if(!isset($params['label']) || empty($params['label'])){
      $params['label'] = 'Go back';
    }
    return '<li class="sf_admin_action_list">'.link_to(__($params['label'], array(), 'sf_admin'), $this->getUrlForAction('list')).'</li>';
  }

  public function linkToSave($object, $params)
  {
    return '<li class="sf_admin_action_save"><input type="submit" value="'.__($params['label'], array(), 'sf_admin').'" onclick="'.$this->getCurtainJavascript($params).'" /></li>';
  }

  public function linkToSaveAndAdd($object, $params)
  {
    return '<li class="sf_admin_action_save_and_add"><input type="submit" value="'.__($params['label'], array(), 'sf_admin').'" onclick="'.$this->getCurtainJavascript($params).'" name="_save_and_add" /></li>';
  }
  
  public function linkToSaveAndList($object, $params)
  {
    return '<li class="sf_admin_action_save_and_list"><input type="submit" value="'.__($params['label'], array(), 'sf_admin').'" onclick="'.$this->getCurtainJavascript($params).'" name="_save_and_list" /></li>';
  }

  public function getUrlForAction($action)
  {
    return 'list' == $action ? '<?php echo $this->params['route_prefix'] ?>' : '<?php echo $this->params['route_prefix'] ?>_'.$action;
  }

  public function getDisabledActionHelp($message)
  {
    return '<a href="#" onclick="alert('."'$message'".'); return false;" >'.image_tag('/sfPropelRevisitedGeneratorPlugin/images/icons/16x16/helpdisabled.png').'</a>';
  }

  public function getDisabledAction($object, $params)
  {
    $condition_method = $params['condition'];
    $condition_message = preg_replace('/^can(.*)/','getMessageCant${1}',$condition_method);
    $help_icon = method_exists($object,$condition_message)?$this->getDisabledActionHelp(__($object->$condition_message())):'';
    return '<li class="sf_admin_action_disabled">'.__($params['label']).' '.$help_icon.'</li>'."\n";
  }

  public function showDisabledActions()
  {
    return sfConfig::get('app_revisited_generator_show_disabled_actions',false);
  }

  public function getDisabledActions($action_list)
  { $ret='';
    if ($this->showDisabledActions() && count($action_list)>0 )
    {
      $ret='<div class="sf_admin_td_actions_disabled_title">'.__('Disabled actions').'</div><ul class="sf_admin_td_actions_disabled">';
      foreach($action_list as $li) $ret.=$li;
      $ret.='</ul>';
    }
    return $ret;
  }

  public function linkToExport($params)
  {
    $params['action'] = isset($params['action'])? $params['action'] : 'doExportationPages';
    $params['label'] = 'Export';

    return '<li class="sf_admin_action_export">'.link_to_function(__('Export'),
"
jQuery('#sf_admin_exportation').show();
jQuery('#sf_admin_exportation_ajax_indicator').show();
jQuery('#sf_admin_exportation_form').hide();
jQuery('#sf_admin_exportation').centerHorizontally();

jQuery('#sf_admin_exportation_form').load('".sfContext::getInstance()->getModuleName().'/'.$params['action']."',

  function (response, status, xhr) {
    if (status != 'error')
    {
      jQuery('#sf_admin_exportation').show();
      jQuery('#sf_admin_exportation_ajax_indicator').hide();
      jQuery('#sf_admin_exportation_form').show();
      jQuery('#sf_admin_exportation').centerHorizontally();
      jQuery('#sf_admin_exportation_resizable_area').ensureVisibleHeight();
      jQuery(document).scrollTop(jQuery('#sf_admin_exportation').offset().top);
    }
  }
)").'</li>';
  }

  public function linkToUserExport($params)
  {
    $params['action'] = isset($params['action'])? $params['action'] : 'newUserExportation';
    $params['label'] = 'Custom export';

    return '<li class="sf_admin_action_user_export">'.link_to_function(__('Custom export'),
"
jQuery('#sf_admin_exportation').show();
jQuery('#sf_admin_exportation_ajax_indicator').show();
jQuery('#sf_admin_exportation_form').hide();
jQuery('#sf_admin_exportation').centerHorizontally();

jQuery('#sf_admin_exportation_form').load('".sfContext::getInstance()->getModuleName().'/'.$params['action']."',

  function (response, status, xhr) {
    if (status != 'error')
    {
      jQuery('#sf_admin_exportation').show();
      jQuery('#sf_admin_exportation_ajax_indicator').hide();
      jQuery('#sf_admin_exportation_form').show();
      jQuery('#sf_admin_exportation').centerHorizontally();
      jQuery('#sf_admin_exportation_resizable_area').ensureVisibleHeight();
      jQuery(document).scrollTop(jQuery('#sf_admin_exportation').offset().top);
    }
  }
)").'</li>';
  }
  
}
