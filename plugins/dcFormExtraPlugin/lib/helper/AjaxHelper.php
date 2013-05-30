<?php

  /******************************************
   * This helper replaces the 'remote_function' 
   * by adding some useful options. Take a look
   * yourself! Damn you!
   *
   * This helpers are required.
   *  - Assets
   *  - Javascript
   *
   ******************************************/


  /**
   * Load required helpers... if a context exists
   */
  function _load_requirements()
  {
    if (sfContext::hasInstance())
    {
      sfContext::getInstance()->getConfiguration()->loadHelpers(array('Asset', 'Javascript', 'Url', 'Tag'));
    }
  }

 /**
  * Returns an img tag that will be shown in the update tag when the ajax request is in 'loading' stage
  *
  * Options involved:
  *   - loadingImage
  */
  function _custom_get_ajax_loader_image($options)
  {
    return image_tag($options['loadingImage'], array('alt' => 'Ajax loader', 'style' => $options['loadingImage']));
  }

 /**
  * Returns the text that will be shown in the update tag when the ajax request is in 'loading' stage
  *
  * Options involved:
  *   - loadingText
  */
  function _custom_get_ajax_loader_text($options)
  {
    return $options['loadingText'];
  }

 /**
  * Returns the ajax loading HTML.
  *
  * Options involved:
  *   - loadingImagePath
  *   - loadingText
  *   - loadingHtml - replaced values in loadingHtml are %%image%% and %%text%%
  */
  function _custom_get_ajax_loader($options)
  {
    return str_replace(
      array('%%image%%', '%%text%%'),
      array(_custom_get_ajax_loader_image($options), _custom_get_ajax_loader_text($options)),
      $options['loadingHtml']
    );
  }

  /**
   * Return the javascript that replaces the update tag content.
   */
  function _custom_get_ajax_loader_javascript($options)
  {
    $id = $options['update'];
    return "var element = $('$id'); if (element) { element.update('".addcslashes(_custom_get_ajax_loader($options), "'")."'); }";
  }

  /**
   * Returns the JS that disables the tag with the specified id.
   */
  function _custom_get_disable_javascript_for($id)
  {
    return "var element = $('$id'); if (element) { element.disable(); }";
  }

  /**
   * Returns the JS that enables the tag with the specified id.
   */
  function _custom_get_enable_javascript_for($id)
  {
    return "var element = $('$id'); if (element) { element.enable(); }";
  }

  /**
   * Returns the JS that hides the tag with the specified id.
   */
  function _custom_get_hide_javascript_for($id)
  {
    return "var element = $('$id'); if (element) { element.hide(); }";
  }

  /**
   * Returns the JS that shows the tag with the specified id.
   */
  function _custom_get_show_javascript_for($id)
  {
    return "var element = $('$id'); if (element) { element.show(); }";
  }

  /**
   * Returns the javascript that will be appended to the 'loading' option.
   */
  function _custom_get_loading_javascript($options)
  {
    $js = "";
    foreach ($options['disableElements'] as $id)
    {
      $js .= _custom_get_disable_javascript_for($id);
    }

    foreach ($options['hideElements'] as $id)
    {
      $js .= _custom_get_hide_javascript_for($id);
    }

    $js .= _custom_get_ajax_loader_javascript($options);

    return $js;
  }

  /**
   * Returns the javascript that will be appended to the 'complete' option.
   */
  function _custom_get_complete_javascript($options)
  {
    $js = "";
    foreach ($options['disableElements'] as $id)
    {
      $js .= _custom_get_enable_javascript_for($id);
    }

    foreach ($options['hideElements'] as $id)
    {
      $js .= _custom_get_show_javascript_for($id);
    }
    return $js;
  }


  /**
   * Sets default options
   */
  function _custom_set_custom_options(&$options)
  {
    $options['wrapperClass']    = isset($options['wrapperClass'])? $options['wrapperClass'] : 'ajaxHelper_wrapper_class';
    $options['loadingImage']    = isset($options['loadingImage'])? $options['loadingImage'] : '/dcFormExtraPlugin/images/ajax-loader.gif';
    $options['loadingText']     = isset($options['loadingText'])? $options['loadingText'] : 'Espere por favor...';
    $options['loadingHtml']     = isset($options['loadingHtml'])? $options['loadingHtml'] : '<div class="'.$options['wrapperClass'].'">%%image%% %%text%%</div>';
    $options['disableElements'] = isset($options['disableElements'])? $options['disableElements'] : array();
    $options['hideElements']    = isset($options['hideElements'])? $options['hideElements'] : array();
  }

  /**
   * Unsets the dwfault options
   */
  function _custom_unset_custom_options(&$options)
  {
    unset(
      $options['wrapperClass'],
      $options['loadingHtml'],
      $options['loadingImage'],
      $options['loadingText'],
      $options['disableElements'],
      $options['hideElements']
    );
  }

  /**
   * Concatenates javascript
   */
  function _custom_concatenate_javascript(&$options, $key, $javascript)
  {
    if (isset($options[$key]))
    {
      $trimmed = trim($options[$key]);
      if ($trimmed[count($trimmed)-1] != ';')
      {
        $trimmed .= ';';
      }
      $javascript = $trimmed.$javascript;
    }
    $options[$key] = $javascript;
  }

  /**
   * Replaces the remote_function function
   *
   * Adds the following options:
   *  'wrapperClass'    : the class of the id that wraps the ajax loader html.
   *  'loadingHtml'     : the html of the ajax_loader. The text '%%image%%' and '%%text%%' will be replaced.
   *  'loadingImage'    : the image url of the ajax loader that will replace the %%image%% text inthe loadingHtml.
   *  'loadingText'     : the text of the ajax loader that will replace the %%text%% text in the loadingHtml.
   *  'disableElements' : an array of ids to disable while loading and enable in complete.
   *  'hideElements'    : an array of ids to hide while loading and show in complete.
   *
   * None of this options are required. See _custom_set_custom_options to see the defaults.
   */
  function custom_remote_function($options)
  {
    _load_requirements();
    _custom_set_custom_options($options);

    $loadingJavascript  = _custom_get_loading_javascript($options);
    $completeJavascript = _custom_get_complete_javascript($options);

    _custom_concatenate_javascript($options, 'loading', $loadingJavascript);
    _custom_concatenate_javascript($options, 'complete', $completeJavascript);

    _custom_unset_custom_options($options);
    return remote_function($options);
  }

?>
