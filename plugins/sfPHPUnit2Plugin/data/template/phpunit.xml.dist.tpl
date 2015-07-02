<phpunit
  colors="true"
  convertErrorsToExceptions="true"
  convertNoticesToExceptions="true"
  convertWarningsToExceptions="true"
  stopOnFailure="true">
  
  <testsuites>
    <testsuite name="Unit Tests">
      <directory>test/phpunit/unit/</directory>
    </testsuite>
     <testsuite name="Functional Tests">
       <directory>test/phpunit/functional/</directory>
    </testsuite>
  </testsuites>
</phpunit>