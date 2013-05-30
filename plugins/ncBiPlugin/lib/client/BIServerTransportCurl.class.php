<?php

/**
 * BIServerTransportCurl
 *
 * Transport implementation using curl.
 *
 * @author José Nahuel Cuesta Luengo <ncuesta@cespi.unlp.edu.ar>
 */
class BIServerTransportCurl extends BIServerTransport
{
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
    if (array_key_exists('_probe', $params))
    {
      $probe = true;
      unset($params['_probe']);
    }
    else
    {
      $probe = false;
    }

    $url = $this->url($url, $params);

    $curl_handle = $this->initialize($url, $probe);

    return $this->fetch($curl_handle);
  }

  /**
   * Perform a POST request on $url passing along $params.
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
  public function post($url, $params = array())
  {
    $curl_handle = $this->initialize($url, false, 'POST', $params);

    return $this->fetch($curl_handle);
  }

  /**
   * Initialize a cURL resource handler and return it.
   *
   * @throws RuntimeException
   *
   * @param  string $url   The server URL.
   * @param  bool   $probe Whether this is a probe or not.
   * 
   * @return resource
   */
  private function initialize($url, $probe = false, $method = 'GET', $post_params = array())
  {
    $handle = curl_init();

    if (false === $handle)
    {
      throw new RuntimeException(sprintf('An error occurred while trying to initialize cURL session for %s', $url));
    }

    curl_setopt_array($handle, array(
      CURLOPT_URL            => $url,
      CURLOPT_VERBOSE        => false,
      CURLOPT_CONNECTTIMEOUT => $this->getTimeout($probe),
      CURLOPT_TIMEOUT        => $this->getTimeout($probe),
      CURLOPT_RETURNTRANSFER => true,
      CURLOPT_POST           => $method == 'POST'
    ));

    if (count($post_params) > 0)
    {
      curl_setopt($handle, CURLOPT_POSTFIELDS, http_build_query($post_params));
    }

    return $handle;
  }

  /**
   * Fetch the response for $curl_handle, an already initialized cURL resource.
   * Throw a RuntimeException on error.
   *
   * @throws RuntimeException
   *
   * @param  resource $curl_handle The cURL resource to fetch.
   *
   * @return mixed
   */
  private function fetch($curl_handle)
  {
    $contents = curl_exec($curl_handle);

    if (curl_errno($curl_handle))
    {
      throw new RuntimeException(sprintf('An error occurred while trying to fetch with cURL: %s', curl_error($curl_handle)));
    }

    curl_close($curl_handle);

    return $contents;
  }
  
}
