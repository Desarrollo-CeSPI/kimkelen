<?php

/**
 * BIObjectsFactory
 *
 * @author José Nahuel Cuesta Luengo <ncuesta@cespi.unlp.edu.ar>
 */
class BIObjectsFactory
{
  /**
   * Create a BI Object from its descripting identifier, which *must* match the following format:
   *   <type>::<identifier>
   *
   * where
   *   - <type> is the type of the BI Object (for example, 'Report' for BIReport objects).
   *   - <identifier> is the internal identifier that will be passed to the constructor of the object.
   *
   * @throws InvalidArgumentException
   *
   * @param  string $identifier The descripting identifier.
   *
   * @return BaseBIObject
   */
  static public function create($identifier)
  {
    if (!preg_match('#([A-Za-z0-9_]+)::(.*)#', $identifier, $matches))
    {
      throw new InvalidArgumentException(sprintf('The provided identifier does not match the expected format (\'<type>::<identifier>\'): %s', $identifier));
    }

    $bi_class = sprintf('BI%s', ucfirst(strtolower($matches[1])));

    if (class_exists($bi_class))
    {
      $reflection = new ReflectionClass($bi_class);

      if ($reflection->isSubclassOf('BaseBIObject'))
      {
        return new $bi_class($matches[2]);
      }
      else
      {
        throw new InvalidArgumentException(sprintf('Referenced class "%s" does not extend BaseBIObject.', $bi_class));
      }
    }
    else
    {
      throw new InvalidArgumentException(sprintf('Unable to find "%s" class.', $bi_class));
    }
  }

}
