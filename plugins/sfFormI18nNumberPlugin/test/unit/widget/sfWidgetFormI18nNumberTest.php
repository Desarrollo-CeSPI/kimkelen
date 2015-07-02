<?php

/*
 * This file is part of the symfony package.
 * (c) Fabien Potencier <fabien.potencier@symfony-project.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

require_once(dirname(__FILE__).'/../../bootstrap/unit.php');

$t = new lime_test(12, new lime_output_color());

$w = new sfWidgetFormI18nNumber();

$t->diag('->clean() - standard culture = en');
$w->setOption('culture','en');

$t->is($w->render('key',12.3), '<input type="text" name="key" value="12.3" id="key" />', '->render() renders the widget as HTML');
$t->is($w->render('key',12.0), '<input type="text" name="key" value="12" id="key" />', '->render() renders the widget as HTML');
$t->is($w->render('key',12), '<input type="text" name="key" value="12" id="key" />', '->render() renders the widget as HTML');
$t->is($w->render('key',12.1234), '<input type="text" name="key" value="12.1234" id="key" />', '->render() renders the widget as HTML');

$w->setOption('culture','de');
$t->is($w->render('key',12.3), '<input type="text" name="key" value="12,3" id="key" />', '->render() renders the widget as HTML');
$t->is($w->render('key',12.0), '<input type="text" name="key" value="12" id="key" />', '->render() renders the widget as HTML');
$t->is($w->render('key',12), '<input type="text" name="key" value="12" id="key" />', '->render() renders the widget as HTML');
$t->is($w->render('key',12.1234), '<input type="text" name="key" value="12,1234" id="key" />', '->render() renders the widget as HTML');

$w->setOption('culture','pl');
$t->is($w->render('key',12.3), '<input type="text" name="key" value="12,3" id="key" />', '->render() renders the widget as HTML');
$t->is($w->render('key',12.0), '<input type="text" name="key" value="12" id="key" />', '->render() renders the widget as HTML');
$t->is($w->render('key',12), '<input type="text" name="key" value="12" id="key" />', '->render() renders the widget as HTML');
$t->is($w->render('key',12.1234), '<input type="text" name="key" value="12,1234" id="key" />', '->render() renders the widget as HTML');

