<?php

/**
 * ncWidgetFormInputUniquenessChecker
 *
 * @author José Nahuel Cuesta Luengo <nahuelcuestaluengo@gmail.com>
 */
class ncWidgetFormInputUniquenessChecker extends sfWidgetFormInput
{
  /**
   * Configure this widget.
   *
   * Available options are:
   *
   *   * url: REQUIRED. The URL to check for uniqueness. It will receive two parameters:
   *          'query' with the query string and 'id' with the optional 'id' option passed.
   *          It must reply with a JSON TRUE or FALSE value.
   *   * id:  OPTIONAL. The primary key of the object (if any) that's being edited. This
   *          can be then used to avoid false negatives.
   *   * success_message: OPTIONAL. A message to show when the response from 'url' is TRUE.
   *                      This will be automatically translated.
   *   * error_message: OPTIONAL. A message to show when the response from 'url' is FALSE.
   *                    This will be automatically translated.
   *   * event: OPTIONAL. DOM event that will trigger the check. Defaults to 'keyup'.
   *
   * @param array $options    Options for this widget.
   * @param array $attributes Attributes for this widget.
   */
  public function configure($options = array(), $attributes = array())
  {
    parent::configure($options, $attributes);

    $this->addRequiredOption('url');

    $this->addOption('id');
    $this->addOption('event', 'keyup');
    $this->addOption('success_message', 'This value is available.');
    $this->addOption('error_message', 'This value is already in use.');
  }

  /**
   * Renders the widget.
   *
   * @param  string $name        The element name
   * @param  string $value       The value displayed in this widget
   * @param  array  $attributes  An array of HTML attributes to be merged with the default HTML attributes
   * @param  array  $errors      An array of errors for the field
   *
   * @return string An HTML tag string
   *
   * @see sfWidgetForm
   */
  public function render($name, $value = null, $attributes = array(), $errors = array())
  {
    $this
      ->setAttribute('data-successmessage', $this->translate($this->getOption('success_message')))
      ->setAttribute('data-errormessage', $this->translate($this->getOption('error_message')))
      ->setAttribute('data-id', $this->getOption('id'))
      ->setAttribute('data-event', $this->getOption('event'))
      ->setAttribute('data-url', $this->getOption('url'))
      ->setAttribute('data-plugin', 'uniqueness-checker')
      ->setAttribute('autocomplete', 'off');

    return parent::render($name, $value, $attributes, $errors);
  }

  /**
   * Get the required Javascripts for this widget.
   *
   * @return array
   */
  public function getJavaScripts()
  {
    return array(
      '/dcReloadedFormExtraPlugin/js/uniqueness-checker.js',
    );
  }

  /**
   * Get the required stylesheets for this widget.
   *
   * This widget looks better if used with Twitter's bootstrap CSS framework,
   * although that's not a requirement.
   *
   * @return array
   *
   * @see    http://twitter.github.com/bootstrap/#alerts
   */
  public function getStylesheets()
  {
    return array(
      '/dcReloadedFormExtraPlugin/css/uniqueness-checker.css' => 'all',
    );
  }
}