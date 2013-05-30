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
 * ncFlavorView
 *
 * @author ncuesta
 */
class ncFlavorView extends sfPHPView
{
  protected function getTemplateDir($module_name, $template)
  {
    $directory = ncFlavorFlavors::getModulePath($module_name).'/templates';

    if (is_readable($directory.'/'.$template))
    {
      return $directory;
    }
    return null;
  }

  protected function getGlobalTemplateDir($template)
  {
    $directory = ncFlavorFlavors::getGlobalPath().'/templates';

    if (is_readable($directory.'/'.$template))
    {
      return $directory;
    }

    return null;
  }

  public function configure()
  {
    parent::configure();

    if (!is_readable($this->getDirectory().'/'.$this->getTemplate()) || !$this->directory)
    {
      $this->setDirectory($this->getTemplateDir($this->moduleName, $this->getTemplate()));

      // require our configuration
      $viewConfigFile = ncFlavorFlavors::getModulePath($this->moduleName).'/config/view.yml';
      if (sfContext::getInstance()->getConfigCache()->checkConfig($viewConfigFile, true))
      {
        require($config);
      }
    }

    if (!is_readable($this->getDecoratorDirectory().'/'.$this->getDecoratorTemplate()))
    {
      $this->decoratorDirectory = $this->getGlobalTemplateDir($this->getDecoratorTemplate());
    }
  }

  /**
   * Loop through all template slots and fill them in with the results of
   * presentation data.
   *
   * @override
   * 
   * @param string A chunk of decorator content
   *
   * @return string A decorated template
   */
  protected function decorate($content)
  {
    $template = $this->getDecoratorDirectory().'/'.$this->getDecoratorTemplate();
    if (!is_readable($template))
    {
      $template = $this->getGlobalTemplateDir($template);
    }

    if (sfConfig::get('sf_logging_enabled'))
    {
      //$this->getContext()->getLogger()->info('{sfView} decorate content with "'.$template.'"');
    }

    // set the decorator content as an attribute
    $this->attributeHolder->set('sf_content', $content);

    // for backwards compatibility with old layouts; remove at 0.8.0?
    $this->attributeHolder->set('content', $content);

    // render the decorator template and return the result
    $retval = $this->renderFile($template);

    return $retval;
  }
  
  /**
   * Renders the presentation.
   *
   * @return string A string representing the rendered presentation
   */
  public function render()
  {
    $content = null;
    if (sfConfig::get('sf_cache'))
    {
      $viewCache = $this->context->getViewCacheManager();
      $uri = $this->context->getRouting()->getCurrentInternalUri();

      if (!is_null($uri))
      {
        list($content, $decoratorTemplate) = $viewCache->getActionCache($uri);
        if (!is_null($content))
        {
          $this->setDecoratorTemplate($decoratorTemplate);
        }
      }
    }

    // render template if no cache
    if (is_null($content))
    {
      // execute pre-render check
      $this->preRenderCheck();

      $this->attributeHolder->set('sf_type', 'action');

      // render template file
      $content = $this->renderFile($this->getFlavorDirectory($this->getModuleName(), $this->getTemplate()).'/'.$this->getTemplate());//die(var_dump($this->getDirectory(), $this->getModuleName(), $this->getTemplate()));

      if (sfConfig::get('sf_cache') && !is_null($uri))
      {
        $content = $viewCache->setActionCache($uri, $content, $this->isDecorator() ? $this->getDecoratorDirectory().'/'.$this->getDecoratorTemplate() : false);
      }
    }

    // now render decorator template, if one exists
    if ($this->isDecorator())
    {
      $content = $this->decorate($content);
    }

    return $content;
  }

  public function getFlavorDirectory($module_name, $template)
  {
    $directory = ncFlavorFlavors::getModulePath($module_name).'/templates';

    if ($this->isFlavorTemplate($directory, $template))
    {
      return $directory;
    }
    else
    {
      return $this->getDirectory();
    }
  }

  public function isFlavorTemplate($flavor_directory, $template)
  {
    return is_readable($flavor_directory.'/'.$template);
  }


}