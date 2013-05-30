<?php

/**
 * BIServerTransport
 *
 * Base class for BI Server communication transport classes.
 * This class is a basic implementation using PHP's builtin file_get_contents()
 * which can be used as a failover transport when no better ways of making
 * requests to the BI Server are available.
 *
 * If any better way of talking to the BI Server is found, the corresponding
 * subclass will be returned upon creation of the Transport.
 *
 * @author José Nahuel Cuesta Luengo <ncuesta@cespi.unlp.edu.ar>
 */
class BIServerTransport
{
  /**
   * Create a new instance of a BIServerTransport.
   * This static method is the factory for BIServerTransport objects.
   * This method will try to determine the best available way of communicating
   * with the BI Server and return an instance of the according subclass.
   *
   * @return BIServerTransport
   */
  static public function create()
  {
    if (self::isAvailable('curl'))
    {
      // Curl is available, use that transport
      return new BIServerTransportCurl();
    }

    // Failover to this class
    return new self();
  }

  /**
   * Perform a GET request on $url passing along $params.
   *
   * If no content is returned from the server a RuntimeException is thrown.
   *
   * @throws RuntimeException
   *
   * @param  string $url    The URL to request.
   * @param  array  $params Optional parameters for the request.
   *
   * @return mixed
   */
  public function get($url, $params = array())
  {
    $probe = array_key_exists('_probe', $params) && $params['_probe'];
    unset($params['_probe']);

    $url     = $this->url($url, $params);
    $context = $this->createContext(array(), $probe);
    $content = file_get_contents($url, null, $context);

    if ($content === false)
    {
      throw new RuntimeException(sprintf('Unable to get contents of %s', $url));
    }

    return $content;
  }

  public function post($url, $params = array())
  {
    $context = $this->createContext($params);
    $content = file_get_contents($url, null, $context);

    if ($content === false)
    {
      throw new RuntimeException(sprintf('Unable to get contents of %s', $url));
    }

    return $content;
  }

  public function url($url, $params = array())
  {
    if (count($params) > 0)
    {
      $query = $this->buildQuery($params);
      $url  .= sprintf('%s%s', (false === strpos($url, '?') ? '?' : '&'), $query);
    }

    return $url;
  }

  protected function createContext($params = array(), $probe = false)
  {
    $options = array(
      'http' => array(
        'method'  => count($params) == 0 ? 'GET' : 'POST',
        'timeout' => $this->getTimeout($probe)
    ));

    if ($options['http']['method'] == 'POST')
    {
      $options['http']['header']  = "Content-type: application/x-www-form-urlencoded\r\n";
      $options['http']['content'] = $this->buildQuery($params);
    }

    return stream_context_create($options);
  }

  protected function buildQuery($params)
  {
    $query = array();

    foreach ($params as $key => $value)
    {
      if (is_array($value))
      {
        foreach ($value as $v)
        {
          $query[] = sprintf('%s=%s', $key, urlencode($v));
        }
      }
      else
      {
        $query[] = sprintf('%s=%s', $key, urlencode($value));
      }
    }

    return implode('&', $query);
  }

  protected function __construct()
  {
    // Make the constructor protected so that the only way of creating new
    // instances of BIServerTransport objects is through the factory method
    // BIServerTransport::create()
  }

  /**
   * Get the timeout for the next request, as configured in the app.yml file.
   * 
   * @param bool $probe Whether or not the request will be a probe to the server.
   *
   * @return float
   */
  protected function getTimeout($probe = false)
  {
    if ($probe)
    {
      return (float) sfConfig::get('app_nc_bi_plugin_probe_timeout', 5.0);
    }
    else
    {
      return (float) sfConfig::get('app_nc_bi_plugin_timeout', 30.0);
    }
  }

  /**
   * Check if the extension $library is available.
   *
   * @param  string $library The library to check for availability.
   *
   * @return bool
   */
  static protected function isAvailable($library)
  {
    return in_array($library, get_loaded_extensions());
  }

}
