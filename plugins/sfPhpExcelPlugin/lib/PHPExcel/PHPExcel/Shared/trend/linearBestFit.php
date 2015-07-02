<?php


$testData = array( '1' => 1.5,
				   '2' => 1.6,
				   '3' => 2.1,
				   '4' => 3.0
				 );

$testData2 = array( '1.5' => 1.5,
					'2.5' => 1.6,
					'3.5' => 2.1,
					'4.5' => 3.0
				  );

$testData3 = array( '160' => 126,
					'180' => 103,
					'200' => 82,
					'220' => 75,
					'240' => 82,
					'260' => 40,
					'280' => 20
				  );

$testData4 = array( '1.5' => 1.5,
					'2.5' => 2,
					'3.5' => 2.5,
					'4.5' => 3.0
				  );




function getYPointFromPointsOnLine($x0,$y0,$x1,$y1,$x) {
	$slope = ($y1 - $y0) / ($x1 - $x0);
	return $y0 + ($x - $x0) * $slope;
}	//	function getYPointFromPointsOnLine()


function getYPointFromSlopeIntersect($slope, $intersect, $x) {
	return $x * $slope + $intersect;
}	//	function getYPointFromSlopeIntersect()


function getGoodnessOfFit($x, $y) {
	$bestFit = linear_regression($x, $y);
	$meanY = array_sum($y) / count($y);

	$SSreg = $SStot = 0.0;
	foreach($x as $xKey => $xValue) {
		$bestFitY = getYPointFromSlopeIntersect($bestFit['slope'], $bestFit['intercept'], $xValue);

		$SSreg += ($y[$xKey] - $bestFitY) * ($y[$xKey] - $bestFitY);
		$SStot += ($y[$xKey] - $meanY) * ($y[$xKey] - $meanY);
	}
	if (($SStot == 0.0) || ($SSreg == $SStot)) {
		return 1;
	}

	return 1 - ($SSreg / $SStot);
}	//	function getYPointFromSlopeIntersect()


/**
 * linear regression function
 * @param $x array x-coords
 * @param $y array y-coords
 * @returns array() slope=>slope, intercept=>intercept
 */
function linear_regression($x, $y) {
	// calculate number of points
	$n = count($x);

	// ensure both arrays of points are the same size
	if ($n != count($y)) {
		trigger_error("linear_regression(): Number of elements in coordinate arrays do not match.", E_USER_ERROR);
	}

	foreach($x as $key => $value) {
		if (is_string($value)) {
			$x[$key] = floatval($value);
		}
	}

	// calculate sums
	$x_sum = array_sum($x);
	$y_sum = array_sum($y);

	$xx_sum = 0;
	$xy_sum = 0;

	for($i = 0; $i < $n; $i++) {
		$xy_sum += $x[$i] * $y[$i];
		$xx_sum += $x[$i] * $x[$i];
	}

	// calculate slope
	$m = (($n * $xy_sum) - ($x_sum * $y_sum)) / (($n * $xx_sum) - ($x_sum * $x_sum));

	// calculate intercept
	$b = ($y_sum - ($m * $x_sum)) / $n;

	// return result
	return array( 'slope'		=> $m,
				  'intercept'	=> $b
				);
}	//	function linear_regression()


echo 'X Values<br />';
var_dump(array_keys($testData));
echo 'Y Values<br />';
var_dump(array_values($testData));
echo '<hr />';

$bestFit = linear_regression(array_keys($testData), array_values($testData));
echo '<pre>';
print_r($bestFit);
echo '</pre>';
echo 'y = '.$bestFit['slope'].'x + '.$bestFit['intercept'].'<br />';

echo '<br />';
foreach(array_keys($testData) as $xValue) {
	$xValue = floatval($xValue);
	$yValue = $xValue * $bestFit['slope'] + $bestFit['intercept'];
	echo 'X = '.$xValue.' Y = '.$yValue.'<br />';
}
echo '<br />';
echo 'Goodness of fit = '.getGoodnessOfFit(array_keys($testData), array_values($testData)).'<br />';
echo '<br />';

$xValue = 3;
$yValue = getYPointFromPointsOnLine(1, 1.5, 4, 3, $xValue);
echo 'From Points on Line:<br />When X = '.$xValue.', then Y = '.$yValue.'<br />';
$yValue = getYPointFromSlopeIntersect($bestFit['slope'], $bestFit['intercept'], $xValue);
echo 'From Slope and Intersect:<br />When X = '.$xValue.', then Y = '.$yValue.'<br />';


echo '<hr /><hr />';


echo 'X Values<br />';
var_dump(array_keys($testData2));
echo 'Y Values<br />';
var_dump(array_values($testData2));
echo '<hr />';

$bestFit = linear_regression(array_keys($testData2), array_values($testData2));
echo '<pre>';
print_r($bestFit);
echo '</pre>';
echo 'y = '.$bestFit['slope'].'x + '.$bestFit['intercept'].'<br />';

echo '<br />';
foreach(array_keys($testData2) as $xValue) {
	$xValue = floatval($xValue);
	$yValue = $xValue * $bestFit['slope'] + $bestFit['intercept'];
	echo 'X = '.$xValue.' Y = '.$yValue.'<br />';
}
echo '<br />';
echo 'Goodness of fit = '.getGoodnessOfFit(array_keys($testData2), array_values($testData2)).'<br />';
echo '<br />';

$xValue = 3;
$yValue = getYPointFromPointsOnLine(1.5, 1.5, 4.5, 3, $xValue);
echo 'From Points on Line:<br />When X = '.$xValue.', then Y = '.$yValue.'<br />';
$yValue = getYPointFromSlopeIntersect($bestFit['slope'], $bestFit['intercept'], $xValue);
echo 'From Slope and Intersect:<br />When X = '.$xValue.', then Y = '.$yValue.'<br />';


echo '<hr /><hr />';


echo 'X Values<br />';
var_dump(array_keys($testData3));
echo 'Y Values<br />';
var_dump(array_values($testData3));
echo '<hr />';

$bestFit = linear_regression(array_keys($testData3), array_values($testData3));
echo '<pre>';
print_r($bestFit);
echo '</pre>';
echo 'y = '.$bestFit['slope'].'x + '.$bestFit['intercept'].'<br />';

echo '<br />';
foreach(array_keys($testData3) as $xValue) {
	$xValue = floatval($xValue);
	$yValue = $xValue * $bestFit['slope'] + $bestFit['intercept'];
	echo 'X = '.$xValue.' Y = '.$yValue.'<br />';
}
echo '<br />';
echo 'Goodness of fit = '.getGoodnessOfFit(array_keys($testData3), array_values($testData3)).'<br />';
echo '<br />';

$xValue = 220;
$yValue = getYPointFromPointsOnLine(160, 126, 280, 20, $xValue);
echo 'From Points on Line:<br />When X = '.$xValue.', then Y = '.$yValue.'<br />';
$yValue = getYPointFromSlopeIntersect($bestFit['slope'], $bestFit['intercept'], $xValue);
echo 'From Slope and Intersect:<br />When X = '.$xValue.', then Y = '.$yValue.'<br />';


echo '<hr /><hr />';


echo 'X Values<br />';
var_dump(array_keys($testData4));
echo 'Y Values<br />';
var_dump(array_values($testData4));
echo '<hr />';

$bestFit = linear_regression(array_keys($testData4), array_values($testData4));
echo '<pre>';
print_r($bestFit);
echo '</pre>';
echo 'y = '.$bestFit['slope'].'x + '.$bestFit['intercept'].'<br />';

echo '<br />';
foreach(array_keys($testData4) as $xValue) {
	$xValue = floatval($xValue);
	$yValue = $xValue * $bestFit['slope'] + $bestFit['intercept'];
	echo 'X = '.$xValue.' Y = '.$yValue.'<br />';
}
echo '<br />';
echo 'Goodness of fit = '.getGoodnessOfFit(array_keys($testData4), array_values($testData4)).'<br />';
echo '<br />';

$xValue = 2.5;
$yValue = getYPointFromPointsOnLine(1.5, 1.5, 4.5, 3, $xValue);
echo 'From Points on Line:<br />When X = '.$xValue.', then Y = '.$yValue.'<br />';
$yValue = getYPointFromSlopeIntersect($bestFit['slope'], $bestFit['intercept'], $xValue);
echo 'From Slope and Intersect:<br />When X = '.$xValue.', then Y = '.$yValue.'<br />';


?>
