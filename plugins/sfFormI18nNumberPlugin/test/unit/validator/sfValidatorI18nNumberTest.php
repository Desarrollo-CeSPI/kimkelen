<?php

/*
 * This file is part of the symfony package.
 * (c) Fabien Potencier <fabien.potencier@symfony-project.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

require_once(dirname(__FILE__).'/../../bootstrap/unit.php');

$t = new lime_test(13, new lime_output_color());

$v = new sfValidatorI18nNumber();

// ->clean() - no culture
$t->diag('->clean() - standard culture = en');

$v->setOption('culture','en');
$t->is($v->clean(12.3), 12.3, '->clean() returns the numbers unmodified');
$t->is($v->clean('12.3'), 12.3, '->clean() converts strings to numbers');

$t->is($v->clean(12.12345678901234), 12.12345678901234, '->clean() returns the numbers unmodified');
$t->is($v->clean('12.12345678901234'), 12.12345678901234, '->clean() converts strings to numbers');

$t->is($v->clean('123,456.78'), 123456.78, '->clean() convert grouped numbers');

try
{
  $v->clean('123,456.789,012');
  $t->fail('->clean fails wrong grouped numbers');
}
catch (sfValidatorError $e)
{
  $t->pass('->clean throws a sfValidatorError if the value is grouped wrong');
}

try
{
  $v->clean('not a float');
  $t->fail('->clean() throws a sfValidatorError if the value is not a number');
}
catch (sfValidatorError $e)
{
  $t->pass('->clean() throws a sfValidatorError if the value is not a number');
}

$t->diag('->clean() - culture = de');
$v->setOption('culture','de');
$t->is($v->clean("12,3"),"12.3",'->clean() return the normalized string');
$t->is($v->clean('12,12345678901234'), 12.12345678901234, '->clean() converts strings to normalized numbers');
$t->is($v->clean('123.456,78'), 123456.78, '->clean() convert grouped numbers');
$t->is($v->clean('100.000'), 100000, '->clean() convert grouped numbers');

try
{
  $v->clean('123.456,789.012');
  $t->fail('->clean fails wrong grouped numbers');
}
catch (sfValidatorError $e)
{
  $t->pass('->clean throws a sfValidatorError if the value is grouped wrong');
}

try
{
  $v->clean("12.3");
  $t->fail('->clean() throws a sfValidatorError if the value is not in localized format');
}
catch  (sfValidatorError $e)
{
  $t->pass('->clean() throws a sfValidatorError if the value is not in localized format');
}

