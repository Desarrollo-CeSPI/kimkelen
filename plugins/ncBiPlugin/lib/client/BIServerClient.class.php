<?php

/**
 * BIServerClient
 *
 * @author José Nahuel Cuesta Luengo <ncuesta@cespi.unlp.edu.ar>
 */
class BIServerClient
{
  static private $instances = array();
  
  /**
   * BI Server's URL.
   *
   * @var string
   */
  private $server_url = null;

  /**
   * Transport object used for talking to the BI Server.
   * 
   * @var BIServerTransport
   */
  private $transport = null;

  /**
   * Get an instance of BIServerClient for $server_url (if provided) or the default BI Server URL.
   * This method has to be used instead of the constructor so that there is at most one instance
   * of BIServerClient per BI Server URL (avoids double-checks of reach, for example).
   *
   * @param  string $server_url Optional BI Server URL. If not provided, the default is taken from configuration.
   *
   * @return BIServerClient
   */
  static public function create($server_url = null)
  {
    $server_url = $server_url === null ? self::defaultURL() : $server_url;

    if (!array_key_exists($server_url, self::$instances))
    {
      self::$instances[$server_url] = new self($server_url);
    }

    return self::$instances[$server_url];
  }

  /**
   * Protected constructor. New instances should be created via BIServerClient::create().
   */
  protected function __construct($server_url = null)
  {
    $this->server_url = $server_url;
    $this->transport  = BIServerTransport::create();

    if (!$this->isReachable())
    {
      throw new RuntimeException(sprintf('Unable to reach BI Server at %s', $this->server_url));
    }
  }

  /**
   * Get a BI Object from the BI Server, returning the contents of the response.
   *
   * @param  mixed $object_or_id Either a BI Object or its identifier.
   * @param  array $parameters   Optional parameters for the BI Object.
   * 
   * @return mixed
   */
  public function get($object_or_id, $parameters = array())
  {
    $object = $this->normalize($object_or_id, $parameters);

    return $this->transport->get($this->getServerURL($object), $object->getParameters());
  }

  /**
   * Get the URL that points to the BI Server for getting the BI Object represented
   * by $object_or_id.
   *
   * @param  mixed $object_or_id Either a BI Object or its identifier.
   * @param  array $parameters   Optional parameters for the BI Object.
   *
   * @return string
   */
  public function url($object_or_id, $parameters = array())
  {
    $object = $this->normalize($object_or_id, $parameters);

    return $this->transport->url($this->getServerURL($object), $object->getParameters());
  }

  /**
   * Get the URL of the BI Server plus a trailing relative path, depending on the
   * kind of BI Object that $object is.
   *
   * @param  BaseBIObject $object The object that will be referenced in the server.
   * 
   * @return string
   */
  protected function getServerURL(BaseBIObject $object)
  {
    return sprintf('%s/%s',
      rtrim($this->server_url, '/'),
      ltrim($object->getRelativeServerPath(). '/')
    );
  }

  /**
   * Turn $object_or_id into a BI Object - if it already isn't - and set $parameters
   * to it.
   *
   * @param  mixed $object_or_id     The info for the object or the BI Object itself.
   * @param  array $parameters       An optional array of parameters for the BI Object.
   *
   * @return BIReport
   */
  protected function normalize($object_or_id, $parameters = array())
  {
    if (!$object_or_id instanceof BaseBIObject)
    {
      $object = BIObjectsFactory::create($object_or_id);
    }
    else
    {
      $object = $object_or_id;
    }
    
    // Merge $parameters
    $object->mergeProperties($parameters);

    return $object;
  }

  /**
   * Answer whether the BI Server is reachable.
   * 
   * @return bool
   */
  protected function isReachable()
  {
    try
    {
      $this->transport->get($this->server_url, array('_probe' => true));
      
      return true;
    }
    catch (Exception $e)
    {
      return false;
    }
  }

  /**
   * Get the default URL for the BI Server.
   *
   * @return string
   */
  static protected function defaultURL()
  {
    return sfConfig::get('app_nc_bi_plugin_bi_server_url', 'http://localhost:8080/pentaho');
  }

}
