<?php

/*
 * This file is part of the symfony package.
 * (c) Fabien Potencier <fabien.potencier@symfony-project.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * Propel generator.
 *
 * @package    symfony
 * @subpackage propel
 * @author     José Nahuel Cuesta Luengo <ncuesta@cespi.unlp.edu.ar>
 * @version    SVN: $Id: sfPropelGenerator.class.php 13495 2008-11-29 16:26:10Z fabien $
 */
class sfPropelRevisitedGenerator extends sfPropelGenerator
{
  public function getSlotActionsConfiguration($context)
  {
    $slot_name = isset($this->config[$context]['slot_name']) ? $this->config[$context]['slot_name'] : 'actions';
    $slot_actions = array();

    foreach ($this->config[$context]['slot_actions'] as $name => $options) {
      $slot_actions[$name] = $options;
    }

    unset($this->config[$context]['slot_actions']);
    unset($this->config[$context]['slot_name']);

    return $slot_actions;
  }

  /**
   * Returns HTML code for slot action.
   *
   * @param string  $actionName   The action name
   * @param array   $params       The parameters
   * @param boolean $pk_link      Whether to add a primary key link or not
   *
   * @return string HTML code
   */
  public function getSlotAction($actionName, $params, $pk_link = false)
  {
    $action = isset($params['action']) ? $params['action'] : 'List'.sfInflector::camelize($actionName);

    //Default parameters definition
    $params['params'] = (isset($params['params'])) ? $params['params'] : array();
    $params['label'] = (isset($params['label'])) ? $params['label'] : sfInflector::humanize($params['action']) ;
    $params['icon'] = (isset($params['icon'])) ? $params['icon'] : $this->getDefaultValue('slot.icon');
    $params['params']['class'] = (isset($params['params']['class'])) ? $params['params']['class'] : $this->getDefaultValue('slot.class');
    $params['params']['image_class'] = (isset($params['params']['image_class'])) ? $params['params']['image_class'] : $this->getDefaultValue('slot.image_class');
    $params['params']['text_class'] = (isset($params['params']['text_class'])) ? $params['params']['text_class'] : $this->getDefaultValue('slot.text_class');

    $url_params = $pk_link ? '?'.$this->getPrimaryKeyUrlParams() : '\'';

    $content = '$helper->getSlotActionImage('.$this->asPhp($params).') . $helper->getSlotActionText('.$this->asPhp($params).')';

    return '[?php echo link_to('.$content.', \''.$this->getModuleName().'/'.$action.$url_params.', '.$this->asPhp($params['params']).', \''.$this->getI18nCatalogue().'\') ?]';
  }

  public function getDefaultValue($key, $if_absent = null)
  {
    $defaults = array('slot.text_class'  => 'text',
                      'slot.image_class' => 'image',
                      'slot.class'       => 'button',
                      'slot.icon'        => 'icons/16x16/generic.png');

    return (array_key_exists($key, $defaults) ? $defaults[$key] : $if_absent);
  }

  public function getLinkToAction($actionName, $params, $pk_link = false)
  {
    $options = isset($params['params']) && !is_array($params['params']) ? sfToolkit::stringToArray($params['params']) : array();

    // default values
    if ($actionName[0] == '_')
    {
      $actionName = substr($actionName, 1);
      $name       = $actionName;
      //$icon       = sfConfig::get('sf_admin_web_dir').'/images/'.$actionName.'_icon.png';
      $action     = $actionName;

      if ($actionName == 'delete')
      {
        $options['post'] = true;
        if (!isset($options['confirm']))
        {
          $options['confirm'] = 'Are you sure?';
        }
      }
    }
    else
    {
      $name   = isset($params['name']) ? $params['name'] : $actionName;
      //$icon   = isset($params['icon']) ? sfToolkit::replaceConstants($params['icon']) : sfConfig::get('sf_admin_web_dir').'/images/default_icon.png';
      $action = isset($params['action']) ? $params['action'] : 'List'.sfInflector::camelize($actionName);
    }

    $url_params = $pk_link ? '?'.$this->getPrimaryKeyUrlParams() : '\'';

    $phpOptions = var_export($options, true);

    // little hack
    $phpOptions = preg_replace("/'confirm' => '(.+?)(?<!\\\)'/", '\'confirm\' => __(\'$1\')', $phpOptions);

    return '<li class="sf_admin_action_'.sfInflector::underscore($name).'">[?php echo link_to(__(\''.$params['label'].'\'), \''.$this->getModuleName().'/'.$action.$url_params.($options ? ', '.$phpOptions : '').') ?]</li>'."\n";
  }
}
