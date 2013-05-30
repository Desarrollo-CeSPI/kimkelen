<?php

/**
 * BI Helper.
 *
 * @author José Nahuel Cuesta Luengo <ncuesta@cespi.unlp.edu.ar>
 */

/**
 * Include a BI Object as an <iframe> in the generated HTML.
 *
 * @param  mixed $object_or_id The BI Object or its identifier.
 * @param  array $params       Optional parameters for the object.
 * @param  array $html_options Optional HTML attributes for the iframe tag.
 *
 * @return string
 */
function include_bi($object_or_id, $params = array(), $html_options = array())
{
  $client = __bi_get_client();

  $html_options['src'] = $client->url($object_or_id, $params);

  $fallback_anchor = content_tag('a', 'See the report', array('href' => $html_options['src'], 'target' => '_blank'));

  return content_tag('iframe', $fallback_anchor, $html_options);
}

/**
 * Shorthand method for using include_bi() with Reports.
 *
 * @param  string $identifier   The identifier of the report without the leading 'Report::'.
 * @param  array  $params       Optional parameters for the report.
 * @param  array  $html_options Optional HTML attributes for the iframe tag.
 *
 * @return string
 */
function include_report($identifier, $params = array(), $html_options = array())
{
  return include_bi('Report::'.$identifier, $params, $html_options);
}

/**
 * Include a link to a BI Object as an HTML <a> tag.
 *
 * @param  string $name         The content of the <a> tag.
 * @param  mixed  $object_or_id The BI Object or its identifier.
 * @param  array  $params       Optional parameters for the object.
 * @param  array  $html_options Optional HTML attributes for the a tag.
 *
 * @return string
 */
function link_to_bi($name, $object_or_id, $params = array(), $html_options = array())
{
  $client = __bi_get_client();

  return link_to($name, $client->url($object_or_id, $params), $html_options);
}

/**
 * Shorthand method for using link_to_bi() with Reports.
 *
 * @param  string $name         The content of the <a> tag.
 * @param  string $identifier   The identifier of the report without the leading 'Report::'.
 * @param  array  $params       Optional parameters for the report.
 * @param  array  $html_options Optional HTML attributes for the a tag.
 *
 * @return string
 */
function link_to_report($name, string $identifier, $params = array(), $html_options = array())
{
  return link_to_bi($name, 'Report::'.$identifier, $params, $html_options);
}

/**
 * Get a BI Server client object.
 *
 * @return BIServerClient
 */
function __bi_get_client()
{
  return BIServerClient::create();
}