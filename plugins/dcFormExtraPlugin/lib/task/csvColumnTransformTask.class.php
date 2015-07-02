<?php

class csvColumnTransformTask extends sfBaseTask
{
  protected function configure()
  {
    // add your own arguments here
    $this->addArguments(array(
      new sfCommandArgument('input_file', sfCommandArgument::REQUIRED, 'The input path'),
    ));

    $this->addOptions(array(
      new sfCommandOption('application', null, sfCommandOption::PARAMETER_REQUIRED, 'The application name', 'frontend'),
      new sfCommandOption('env', null, sfCommandOption::PARAMETER_REQUIRED, 'The environment', 'dev'),
      new sfCommandOption('connection', null, sfCommandOption::PARAMETER_REQUIRED, 'The connection name', 'propel'),
      new sfCommandOption('output_file', null, sfCommandOption::PARAMETER_REQUIRED, 'The output file'),
      new sfCommandOption('input_delimiter', null, sfCommandOption::PARAMETER_REQUIRED, "The field delimiter for the input file", ';'),
      new sfCommandOption('input_enclosure', null, sfCommandOption::PARAMETER_REQUIRED, "The field enclosure characte for the input file", '"'),
      new sfCommandOption('output_delimiter', null, sfCommandOption::PARAMETER_REQUIRED, "The field delimiter for the output file", ';'),
      new sfCommandOption('output_enclosure', null, sfCommandOption::PARAMETER_REQUIRED, "The field enclosure characte for the output file", '"'),
      new sfCommandOption('column', null, sfCommandOption::PARAMETER_REQUIRED, "The column to replace"),
      new sfCommandOption('method_params', null, sfCommandOption::PARAMETER_REQUIRED, "The parameters for the method as integers separated by comma (indexes of the csv line)"),
      new sfCommandOption('method', null, sfCommandOption::PARAMETER_REQUIRED, "The function or class::method to be called."),
      new sfCommandOption('to_string_method', null, sfCommandOption::PARAMETER_OPTIONAL, ""),
    ));

    $this->namespace        = 'dcFormExtra';
    $this->name             = 'csvColumnTransformTask';
    $this->briefDescription = 'Modifies CSV column values by using a user-specified method.';
    $this->detailedDescription = <<<EOF
The [csvUnitCodeToUnitId|INFO] task modifies CSV column values by using a user-specified method.

  [php symfony csvUnitCodeToUnitId|INFO]
EOF;
  }

  protected  function createContextInstance($application, $enviroment)
  {
    $configuration = ProjectConfiguration::getApplicationConfiguration($application, $enviroment, 'false');

    sfContext::createInstance($configuration);
    sfContext::switchTo($application);
  }

  protected function execute($arguments = array(), $options = array())
  {
    $databaseManager = new sfDatabaseManager($this->configuration);
    $connection = $databaseManager->getDatabase($options['connection'] ? $options['connection'] : null)->getConnection();
    $this->createContextInstance($options['application'], $options['env']);
    
    if (file_exists($arguments['input_file']) && !file_exists($options['output_file']))
    {
      $inputHandle = fopen($arguments['input_file'], "r");
      $outputHandle = fopen($options['output_file'], "w");
      if ($inputHandle !== false && $outputHandle !== false)
      {
        while ($line = fgetcsv($inputHandle, 0, $options['input_delimiter'], $options['input_enclosure']))
        {
          if (!empty($line))
          {
            $output = $line;
            $params = array();

            if (strpos($options['method_params'], ',') === FALSE)
            {
              $params[] = $line[$options['method_params']];
            }
            else
            {
              $cols   = explode(",", $options['method_params']);
              foreach ($cols as $c)
              {
                if (!empty($c))
                {
                  $params[] = $line[$c];
                }
              }
            }

            $output[$options['column']] = $this->retrieveValue($line[$options['column']], $options['method'], $params, $options['to_string_method']);
            fputcsv($outputHandle, $output, $options['output_delimiter'], $options['output_enclosure']);
          }
        }

        fclose($inputHandle);
        fclose($outputHandle);
        $this->log("File ".$options['output_file']." created successfully");
      }
      else
      {
        throw new sfException("Input/output could not be opened.");
      }
    }
    else
    {
      throw new sfException("Input file does not exists or output file already exists");
    }
  }

  public function retrieveValue($value, $method, $params, $toStringMethod)
  {
    $method = explode('::', $method);

    if (count($method) == 2)
    {
      $val = eval("return call_user_func(array('".$method[0]."', '".$method[1]."'), ".implode(', ', $params).");");
    }
    else
    {
      $val = eval("return ".$method[0]."(".implode(', ', $params).");");
    }

    if (is_object($val) && !empty($toStringMethod))
    {
      return $val->$toStringMethod();
    }
    return $val;
  }
}
