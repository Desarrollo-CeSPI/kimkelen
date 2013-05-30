# ncBiPlugin

symfony plugin aimed at providing a programmer-friendly API for the use of Pentaho's BI Server facillities.

## Configuration

Add a `nc_bi_plugin` section in your application's `app.yml` file, specifying your settings.

The following is an example with the default parameters:

    all:
      nc_bi_plugin:
        bi_server_url: http://localhost:8080/pentaho  # Base Server URL
        report_suffix: content/reporting              # Server path to the reporting component
        timeout:       30                             # Maximum number of seconds to wait for a response from the server on a request
        probe_timeout: 5                              # Maximum number of seconds to wait for a response from the server on a probe (test for reach)

You may override any of this settings for your convenience.

## Usage

After configuring the plugin, you can use it directly in your templates with the provided helper `BIHelper`.

You can either refer to the BI objects either by an identifier or using the builtin subclasses of `BaseBIObject`
- or you may even create your own subclasses to extend the functionality of this plugin.

All classes and helper methods are heavily documented!

### Simple usage

    <!-- In your template -->
    <?php use_helper('BI') ?>

    <!-- link_to_bi() creates a link to a BI Object -->
    <?php echo link_to_bi('Download report', 'Report::Solution/path/to/report.prpt', array('my_param' => 'my_param value', 'other_param' => 2), array('class' => 'bi-report-link')) ?>
    <!-- link_to_report() is a shorthand method for link_to_bi(), so the following line is equivalent to the last one -->
    <?php echo link_to_report('Download report', 'Solution/path/to/report.prpt', array('my_param' => 'my_param_value', 'other_param' => 2), array('class' => 'bi-report-link')) ?>

    <!-- include_bi() creates an iframe including a BI Object -->
    <?php echo include_bi('Report::Solution/report.prpt') ?>
    <!-- include_report() is a shorthand method for include_bi() -->
    <?php echo include_report('Report::Solution/report.prpt') ?>

### Advanced usage

You can create the BI Objects by hand in your controller:

    // In your controller
    public function executeMyAction(sfWebRequest $request)
    {
      $parameters = array(
        'my_param' => 'my_param_value',
        'format'   => BIReport::FORMAT_PDF
      );

      $this->report = new BIReport('Solution/report.prpt', $parameters);
    }

And then pass them to your view:

    <!-- In your template -->
    <?php use_helper('BI') ?>

    <!-- include_bi() and link_to_bi() can take BaseBIObject subclasses as their first argument. -->
    <?php echo include_bi($report) ?>
    <!-- If additional parameters are passed, they are set to the BI object. -->
    <?php echo link_to_bi($report, array('my_param' => 'another value')) ?>


Or you can directly use the provided `BIServerClient` object and avoid the usage of the `BIHelper`:

    // In your controller
    public function executeMyAction(sfWebRequest $request)
    {
      $client = new BIServerClient();
      // ... define $params ...
      // Get the content of the BI Object
      $report_content = $client->get('Report::Solution/path/to/report.prpt', $params);
      // Get the URL of the BI Object
      $report_url = $client->url('Report::Solution/path/to/another_report.prpt', $params);

      return $this->renderText($report_content);
    }
