<?php

/**
 * ncWidgetFormSelect2Ajax
 *
 * @author José Nahuel Cuesta Luengo <nahuelcuestaluengo@gmail.com>
 */
class ncWidgetFormSelect2Ajax extends sfWidgetFormInput
{
	protected function configure($options = array(), $attributes = array())
	{
		parent::configure($options, $attributes);

		$this->addRequiredOption('url');
		$this->addOption('select2_options', array('minimumInputLength' => 3, 'placeholder' => 'Buscar...'));
		$this->addOption('cache_responses', false);
		$this->addOption('html_results', true);
	}

	public function render($name, $value = null, $attributes = array(), $errors = array())
	{
		$attributes['style'] = 'display: none;';

		$id            = $this->generateId($name, $value);
		$input         = parent::render($name, $value, $attributes, $errors);
		$user_options  = json_encode($this->getOption('select2_options'));
		$url           = $this->getUrl();
		$cache         = $this->getOption('cache_responses') ? 'true' : 'false';
		$escape_markup = $this->getOption('html_results') ? 'escapeMarkup: function(m) { return m; },' : '';

		return <<<HTML
{$input}
<script type="text/javascript">
jQuery(function($) {
  var configuration = $.extend(
    {
      {$escape_markup}
      ajax: {
        url: '{$url}',
        results: function(data, page) { return data;},
        data: function(term, page) { return {q: term}; },
        cache: {$cache}
      },
      initSelection: function(element, callback) {
        var v = $(element).val();
        if (v !== '') {
          $.getJSON('{$url}', {id: v}, callback);
        }
        callback({text: ''});
      }
    },
    {$user_options}
  );
  $('#{$id}').select2(configuration);
});
</script>
HTML;
	}

	protected function getUrl()
	{
		$this->loadHelpers(array('Url'));

		return url_for($this->getOption('url'));
	}

	protected function loadHelpers($helpers)
	{
		return sfContext::getInstance()->getConfiguration()->loadHelpers($helpers);
	}

	/**
	 * Gets the JavaScript paths associated with the widget.
	 *
	 * @return array An array of JavaScript paths
	 */
	public function getJavaScripts()
	{
		return array('select2.min.js', 'select2_locale_es.js');
	}

	/**
	 * Gets the stylesheet paths associated with the widget.
	 *
	 * The array keys are files and values are the media names (separated by a ,):
	 *
	 *   array('/path/to/file.css' => 'all', '/another/file.css' => 'screen,print')
	 *
	 * @return array An array of stylesheet paths
	 */
	public function getStylesheets()
	{
		return array('select2.css' => 'screen');
	}
}