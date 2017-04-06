<?php

/*
 * This file is part of the symfony package.
 * (c) Fabien Potencier <fabien.potencier@symfony-project.com>
 * 
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * Base class for tasks that depends on a sfCommandApplication object.
 *
 * @package    symfony
 * @subpackage task
 * @author     Fabien Potencier <fabien.potencier@symfony-project.com>
 * @version    SVN: $Id: sfCommandApplicationTask.class.php 19112 2009-06-10 06:32:12Z fabien $
 */
abstract class sfCommandApplicationTask extends sfTask
{
  protected
    $commandApplication = null;

  /**
   * Sets the command application instance for this task.
   *
   * @param sfCommandApplication $commandApplication A sfCommandApplication instance
   */
  public function setCommandApplication(sfCommandApplication $commandApplication = null)
  {
    $this->commandApplication = $commandApplication;
  }

  /**
   * @see sfTask
   */
  public function log($messages)
  {
    if (is_null($this->commandApplication) || $this->commandApplication->isVerbose())
    {
      parent::log($messages);
    }
  }

  /**
   * @see sfTask
   */
  public function logSection($section, $message, $size = null, $style = 'INFO')
  {
    if (is_null($this->commandApplication) || $this->commandApplication->isVerbose())
    {
      parent::logSection($section, $message, $size, $style);
    }
  }


  /**
   * Returns a mailer instance.
   *
   * Notice that your task should accept an application option.
   * The mailer configuration is read from the current configuration
   * instance, which is automatically created according to the current
   * --application option.
   *
   * @return sfMailer A sfMailer instance
   */
  protected function getMailer()
  {
    if (!$this->mailer)
    {
      $this->mailer = $this->initializeMailer();
    }

    return $this->mailer;
  }

  protected function initializeMailer()
  {
    require_once sfConfig::get('sf_symfony_lib_dir').'/vendor/swiftmailer/classes/Swift.php';
    Swift::registerAutoload();
    sfMailer::initialize();

    $config = sfFactoryConfigHandler::getConfiguration($this->configuration->getConfigPaths('config/factories.yml'));

    return new $config['mailer']['class']($this->dispatcher, $config['mailer']['param']);
  }
}
