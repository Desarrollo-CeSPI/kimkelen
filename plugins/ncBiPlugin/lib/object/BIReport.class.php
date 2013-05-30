<?php

/**
 * BIReport.
 * Subclass of BaseBIObject that represents BI Reports (*.prpt).
 *
 * The properties allowed in objects of this class are:
 *   * format: The format of the report output. Can be any of the FORMAT_* constants.
 *
 * @author José Nahuel Cuesta Luengo <ncuesta@cespi.unlp.edu.ar>
 */
class BIReport extends BaseBIObject
{
  const FORMAT_HTML_PAGEABLE = 'table/html;page-mode=page';
  const FORMAT_HTML_STREAM   = 'table/html;page-mode=stream';
  const FORMAT_CSV           = 'table/csv;page-mode=stream';
  const FORMAT_RTF           = 'table/rtf;page-mode=flow';
  const FORMAT_PDF           = 'pageable/pdf';
  const FORMAT_TEXT          = 'pageable/text';
  const FORMAT_XML           = 'pageable/xml';
  const FORMAT_PNG           = 'pageable/X-AWT-Graphics;image-type=png';
  const FORMAT_EMAIL         = 'mime-message/text/html';

  /**
   * Get the default properties for BI Report objects.
   *
   * @return array
   */
  protected function getDefaultProperties()
  {
    return array(
      'format' => self::FORMAT_HTML_STREAM
    );
  }

  /**
   * Get the names of the admitted properties for this object that are not parameters.
   *
   * @return array
   */
  protected function getAdmittedProperties()
  {
    return array('format');
  }

  /**
   * Add any additional parameters that are inferred from the properties of this BI Object
   * and return the resulting array.
   *
   * @return array
   */
  protected function addParameters(array $parameters)
  {
    list($solution, $path, $name) = $this->explodeIdentifier();

    return array_merge(array(
      'renderMode'    => 'REPORT',
      'output-target' => $this->getProperty('format'),
      'solution'      => $solution,
      'name'          => $name,
      'path'          => $path
    ), $parameters);
  }

  /**
   * Explode the report identifier into the three members that identify a report
   * in a BI Server (solution, path and name). The format of $report should be
   * a forward slash ('/')-delimited string made of (at least) solution/name.
   * The return value will be a 3-members array, with the solution, path and
   * name in that order.
   * I.E.:
   *
   * The identifier:
   *   Solution/path/inside/the/solution/to/report.prpt
   *
   * will produce:
   *   array(
   *     'Solution',                    # Solution member
   *     'path/inside/the/solution/to', # Path member
   *     'report.prpt'                  # Name member
   *   )
   *
   * While the identifier:
   *   Solution/report.prpt
   *
   * will produce:
   *   array(
   *     'Solution',                    # Solution member
   *     null,                          # Path member
   *     'report.prpt'                  # Name member
   *   )
   *
   * @throws RuntimeException On invalid input.
   *
   * @return array
   */
  protected function explodeIdentifier()
  {
    $slashes = substr_count($this->identifier, '/');

    if ($slashes == 1)
    {
      // Only the solution and the report name are provided
      $path = null;
      list($solution, $name) = explode('/', $this->identifier, 2);
    }
    else if ($slashes >= 2)
    {
      // All members are provided
      $first_slash = strpos($this->identifier, '/');
      $last_slash  = strrpos($this->identifier, '/');

      $solution = substr($this->identifier, 0, $first_slash);
      $path     = substr($this->identifier, $first_slash + 1, $last_slash - $first_slash - 1);
      $name     = substr($this->identifier, $last_slash + 1);
    }
    else
    {
      throw new RuntimeException(sprintf('Unable to identify report for the given string: "%s".', $this->identifier));
    }

    return array($solution, $path, $name);
  }

  /**
   * Get the relative path in the server for this kind of BI Objects.
   *
   * @return string
   */
  public function getRelativeServerPath()
  {
    return sfConfig::get('app_nc_bi_plugin_report_suffix', 'content/reporting');
  }

}
