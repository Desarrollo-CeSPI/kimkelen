ncPropelChangeLogBehaviorPlugin
===============================

The `ncPropelChangeLogBehaviorPlugin` provides a Behavior for Propel objects so that any changes made to them are registered an available for later audit or inspection.

Installation
------------

  * Install the plugin

        $ symfony plugin:install ncPropelChangeLogBehaviorPlugin

  * Clear the cache

        $ symfony cache:clear

Model integration
-----------------

This Behavior registers itself for the following hooks:

  * :save:pre    => preSave()

  * :save:post   => postSave()

  * :delete:post => postDelete()

and adds the following methods to the objects using it:

  * getChangeLog(): provides an easy way of obtaining the whole changelog for a specific object;

  * getRelatedChangeLog(): provides an easy way of obtaining the whole changelog for the related tables of an object;

  * getChangeLogRoute(): returns the '@nc_change_log' string to access the object's change log list, with the mandatory parameters already set.

To use this behavior, Propel behaviors must be activated in your model, and you'll have to add the Behavior at the end of all the classes of your model that
should keep a Change Log. To do so, simply add the following lines to your model classes (normally under your project's lib/model/ directory):

        <?php
        // in lib/model/MyModelClass.php
        class MyModelClass extends BaseMyModelClass
        {
          // ...
        }
        sfPropelBehavior::add('MyModelClass', array('changelog'));

After this, rebuild your model, clean your cache and you're done!


Change Log presentation
-----------------------

A module is provided with a very simple but fully functional interface for you to show any object's change log.

Two routing rules are defined:

  * 'nc_change_log': the list of change log entries for a specific object. Takes two parameters: 'class' (the string name of the class) and 'pk' (the object's primary key).

  * 'nc_change_log_detail': the detail for a specific change log entry. Takes one parameter: 'id' (the primary key of the ncChangeLogEntry object).

If you wish to use this module, you'll have to enable it in your application's configuration file:

        ## in apps/<application>/config/settings.yml
        all:
          .settings:
          ## add 'ncchangelogentry' to your already enabled modules:
            enabled_modules: [default, ncchangelogentry]

Further configuration
---------------------

The plugin uses some configuration values, that can be overridden in your app.yml file.

The following code shows the keys used, along with the default values:

        ## app.yml
        all:
          nc_change_log_behavior:
            ## sfUser attribute used when obtaining the 'username' of the person performing the changes.
            username_method:          getUsername
            ## 'username' value used when running a task from cli that registers changes in the model.
            username_cli:             cli
            ## ncChangeLogEntryFormatter child class used when formatting the text
            formatter_class:          ncChangeLogEntryFormatter
            ## try to get foreign values
            get_foreign_values:       false
            ## Instance method used when trying to translate the class name for an object. This method may not exist.
            translation_object_method: getHumanName
            ## Instance method used when trying to translate a field name for an object. This method may not exist.
            translation_field_method: translateField
            ## If this is set to true, the messages will be translated using the catalogue app_name/i18n/tables/table_name. Only valid when using the default translation methods.
            translation_use_i18n:     false
            ## This is the format of the date
            app_nc_change_log_behavior_date_format: 'Y/m/d H:i:s'
            ## the date format
            date_format:                  'Y/m/d'
            ## the date time format
            date_time_format:             'Y/m/d H:i:s'
            ## the time format
            time_format:                  'H:i:s'
            ## If this is set to true, the values shown will be escaped with sfOutputEscaper::unescape. (useful when rendering values throw signals).
            escape_values:                false
            ## Tries to retrieve the object and use its '__toString' method to render it when the field is a foreign key. ###!!EXPERIMENTAL!!##
            get_foreign_values:           false

            ## Fields that should be ignored when looking for changes in an object...
            ignore_fields:
                ## ...for MyClass (THESE are the actual default values)
                my_class: [created_at, created_by, updated_at, updated_by]
                ## ...for MyOtherClass and so on... (this is just an examplo, not a default value)
                my_other_class: [name, height]

Custom formatter
----------------

The configured formatter class (subclass of ncChangeLogEntryFormatter) is used when showing the details of a change log entry.

You might extend this class and define your custom format strings, simply by overriding its instance protected variables. After doing so, just change the configuration value (see above) regarding the formatter class and the details will be formatted using your new class.

See ncChangeLogEntryFormatter class for further information.


Fields formatting
-----------------
  Whenever a field is rendered, a signal 'table_name.field_name' is emmitted (with sfEventDispatcher::filter) with the value of the field as the parameter. This is very useful when rendering primary keys or constants. Example of use:

  We have a table which name is 'summary' and has an integer field named 'current_state'.

frontendConfiguration.class.php
[code]

  $this->dispatcher->connect('summary.render_current_state', array('Summary', 'renderCurrentState'));

[/code]

  Now in the class 'Summary' we associate the integer value with a string.

[code]

  static public function renderCurrentState($event, $value)
  {
    switch ($value)
    {
      case 1:
        $string_representation = 'New';
        break;
      case 2:
        $string_representation = 'Sold';
        break;
      default:
        $string_representation = 'Nonexistent';
        break;
    }
    return $string_representation;
  }

[/code]

  If any of these handlers will return html or javascript code, you you should activate the 'escape_values' parameters in the app.yml

Foreign keys <- Experimental ->
------------
  When a table has a foreign key, the plugin will show the primary key for this column. The plugin can also try to retrieve the object pointed by this column and render it by using its '__toString' method. For this, the 'get_foreign_values' has to be activated in the app.yml.

TODO
----

  * Test fully.
