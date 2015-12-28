<?php 
/*
 * Kimkëlen - School Management Software
 * Copyright (C) 2013 CeSPI - UNLP <desarrollo@cespi.unlp.edu.ar>
 *
 * This file is part of Kimkëlen.
 *
 * Kimkëlen is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License v2.0 as published by
 * the Free Software Foundation.
 *
 * Kimkëlen is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with Kimkëlen.  If not, see <http://www.gnu.org/licenses/gpl-2.0.html>.
 */ ?>
<?php

/**
 * ncChangelogRenderer
 *
 * Renderer for changelog entries.
 *
 * @author José Nahuel Cuesta Luengo <ncuesta@cespi.unlp.edu.ar>
 */
class ncChangelogRenderer
{
  /**
   * Public method for rendering the changelog of an $object using a specific
   * $style ('list', 'tooltip').
   * By passing a value in $options['credentials'], this method will check
   * if the session user has the required credentials to see the changelog,
   * and if not will return an empty string.
   *
   * @param  mixed  $object  An object using ncPropelChangeLogBehavior.
   * @param  string $style   The name of the style for rendering. It can be 'list'
   *                         or 'tooltip'.
   * @param  array  $options An array of options.
   *
   * @return string The rendered changelog when possible, or an empty string.
   */
  static public function render($object, $style = 'list', $options = array())
  {
    try
    {
      if ($object->hasChangeLog() && self::checkCredentials($options))
      {
        $method   = 'renderAs'.sfInflector::camelize($style);
        $callable = array(__CLASS__, $method);

        if (is_callable($callable))
        {
          return call_user_func_array($callable, array($object, $options));
        }
      }
    }
    catch (Exception $exception)
    {
      // do nothing - return an empty string
    }
  }

  /**
   * Render the changelog for $object (if any) as a tooltip.
   * This method can't be called directly, instead use self::render().
   *
   * @see render()
   *
   * @param  mixed $object Any object using ncPropelChangeLogBehavior.
   *
   * @return string
   */
  static protected function renderAsTooltip($object)
  {
    sfContext::getInstance()->getResponse()->addJavascript('changelog');
    self::loadHelpers('Asset');

    $klass = get_class($object);
    $id = $object->getId();
    $html_id = "changelog_for_${klass}_${id}";
    $html = <<<HTML
<a href="#" style="display: inline-block; margin: 1px;" onclick="changelog_render_tooltip('%url%','%klass%','%id%','#%html_id%'); return false;">
  <img style="vertical-align: middle;" src="%img_src%" alt="%img_alt%" title="%img_title%" />
</a>
<div id="%html_id%" class="nc_changelog_tooltip" style="display: none;"></div>
HTML;

    return strtr($html, array(
      '%klass%'     => $klass,
      '%url%'       => url_for('@changelog_helper'),
      '%id%'        => $id,
      '%html_id%'   => $html_id,
      '%img_src%'   => image_path('clock.png'),
      '%img_alt%'   => __('Change log', array(), 'nc_change_log_behavior'),
      '%img_title%' => __('See change log', array(), 'nc_change_log_behavior')
    ));
  }

  /**
   * Render the changelog for $object (if any) as an HTML list (ul, ol).
   * This method can't be called directly, instead use self::render().
   *
   * @see render()
   *
   * @param  mixed  $object    Any object using ncPropelChangeLogBehavior.
   * @param  string $list_type The name of the list tag (ul, ol).
   *
   * @return string
   */
  static protected function renderAsList($object, $list_type = 'ul')
  {
    self::loadHelpers('Tag');

    $changelog = implode("\n", array_map(create_function('$c', 'return content_tag("li", $c->render());'), $object->getChangeLog()));

    return content_tag($list_type, $changelog);
  }


  /**
   * Load the helpers passed as arguments of this function.
   *
   * This function accepts any number of parameters and
   * does its best to load them.
   */
  static protected function loadHelpers()
  {
    $helpers = array();

    foreach (func_get_args() as $arg)
    {
      if (is_array($arg))
      {
        $helpers = array_merge($helpers, $arg);
      }
      else
      {
        $helpers[] = $arg;
      }
    }

    sfContext::getInstance()->getConfiguration()->loadHelpers($helpers);
  }

  /**
   * Get the user.
   *
   * @return myUser
   */
  static protected function getUser()
  {
    return sfContext::getInstance()->getUser();
  }

  /**
   * Check if the session user has the required credentials specified by
   * $options['credentials']. If such an index does not exist in $options,
   * asume access is permitted (return true).
   *
   * @param  array $options Options array with optional 'credentials' key.
   *
   * @return boolean True if the user has the required credentials or if the
   *                 options doesn't specify any.
   */
  static protected function checkCredentials($options)
  {
    if (is_array($options))
    {
      $options = array_merge(array('credentials' => false), $options);

      if (false !== $options['credentials'])
      {
        return self::getUser()->hasCredential($options['credentials']);
      }
    }

    return true;
  }

}
