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
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of ncFlavorPartialView
 *
 * @author mbrown
 */
class ncFlavorPartialView extends sfPartialView {
   
  /**
   * Renders the presentation.
   *
   * @return string Current template content
   */
  public function render()
  {
    if (sfConfig::get('sf_debug') && sfConfig::get('sf_logging_enabled'))
    {
      $timer = sfTimerManager::getTimer(sprintf('Partial "%s/%s"', $this->moduleName, $this->actionName));
    }

    if ($retval = $this->getCache())
    {
      if($this->isFlavorPartial(ncFlavorFlavors::getModulePath($module_name).'/templates', $this->getTemplate()))
      {
        $retval = $this->renderFile($this->getFlavorDirectory($this->getModuleName(), $this->getTemplate()).'/'.$this->getTemplate());
      }
      return $retval;

    }
    else if ($this->checkCache)
    {
      $mainResponse = $this->context->getResponse();
      $responseClass = get_class($mainResponse);
      $this->context->setResponse($response = new $responseClass($this->context->getEventDispatcher(), array_merge($mainResponse->getOptions(), array('content_type' => $mainResponse->getContentType()))));
    }

    try
    {
      // execute pre-render check
      $this->preRenderCheck();

      $this->getAttributeHolder()->set('sf_type', 'partial');

      // render template
      $retval = $this->renderFile($this->getFlavorDirectory($this->getModuleName(), $this->getTemplate()).'/'.$this->getTemplate());
    }
    catch (Exception $e)
    {
      if ($this->checkCache)
      {
        $this->context->setResponse($mainResponse);
        $mainResponse->merge($response);
      }

      throw $e;
    }

    if ($this->checkCache)
    {
      if($this->isFlavorPartial(ncFlavorFlavors::getModulePath($module_name).'/templates', $this->getTemplate()))
      {
        $retval = $this->renderFile($this->getFlavorDirectory($this->getModuleName(), $this->getTemplate()).'/'.$this->getTemplate());
      }
      else
      {
        $retval = $this->viewCache->setPartialCache($this->moduleName, $this->actionName, $this->cacheKey, $retval);
      }
      $this->context->setResponse($mainResponse);
      $mainResponse->merge($response);
    }

    if (sfConfig::get('sf_debug') && sfConfig::get('sf_logging_enabled'))
    {
      $timer->addTime();
    }

    return $retval;
  }

  public function getFlavorDirectory($module_name, $partial)
  {
    $directory = ncFlavorFlavors::getModulePath($module_name).'/templates';

    if ($this->isFlavorPartial($directory, $partial))
    {
      return $directory;
    }
    else
    {
      return $this->getDirectory();
    }
  }
  
  public function isFlavorPartial($flavor_directory, $partial)
  {
    return is_readable($flavor_directory.'/'.$partial);
  }
}
?>