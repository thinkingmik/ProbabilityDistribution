<?php
/**
 * @author Michele Andreoli <michi.andreoli[at]gmail.com>
 * @name Noise.class.php
 * @version 0.1 updated 19-05-2010
 * @license http://opensource.org/licenses/gpl-license-php GNU Public License
 * @package Noise
 */

/**
 * Class that adding noise in a signal
 */
class Noise {
	
	/**
	 * Return an array of specified size with an uniform distribution between a minimum and maximum value  
	 * @param int $n number of values generated
	 * @param double $min minimum value
	 * @param double $max maximum value
	 * @return array<double>
	 */
	public function interval($n, $min, $max) {
		srand(time(NULL));
		
		for ($i = 0; $i < $n; $i++)
			$r[$i] = ( ($max - $min) * (rand() / getrandmax()) + $min);

		return $r;
	}
	
	/**
	 * Return an array of specified size with a Gaussian distribution
	 * @param int $n number of values generated
	 * @param double $med media for the Gaussian distribution
	 * @param double $var variance for the Gaussian distribution
	 * @return array<double>
	 */
	public function gaussian($n, $med, $var) {
		for($i = 0; $i < $n; $i++) {
			do {
				$v1 = 2.0 * rand() / getrandmax() - 1.0;
				$v2 = 2.0 * rand() / getrandmax() - 1.0;
				$r2 = ($v1 * $v1) + ($v2 * $v2);
			} while ($r2 >= 1 || $r2 == $med);
			
			$fac = sqrt(-2.0 * log($r2) / $r2);
			$r[$i] = $v2 * $fac;
		}
	
		return $r;
	}
	
	/**
	 * Add gaussian noise to the signal in input
	 * @param array<double> $func the signal in input 
	 * @param double $med media for the Gaussian distribution
	 * @param double $var variance for the Gaussian distribution
	 * @return array<double> the input signal with gaussian noise
	 */
	public function addGaussian($func, $med, $var) {
		for($i = 0; $i < count($func); $i++) {
			do {
				$v1 = 2.0 * rand() / getrandmax() - 1.0;
				$v2 = 2.0 * rand() / getrandmax() - 1.0;
				$r2 = ($v1 * $v1) + ($v2 * $v2);
			} while ($r2 >= 1 || $r2 == $med);
			
			$fac = sqrt(-2.0 * log($r2) / $r2);
			$r[$i] = $func[$i] + (sqrt($var) * ($v2 * $fac));
		}
	
		return $r;
	}
	
	/**
	 * Return an array of specified size with a Poisson distribution
	 * @param int $n number of values generated
	 * @param int $med media value for the Poisson distribution
	 * @return array<int> 
	 */
	public function poisson($n, $med) {
		for ($i = 0; $i < $n; $i++)
			$r[$i] = $this->poidev($med);
			
		return $r;
	}
	
	/**
	 * Add poissonian noise to the signal in input (the input function must be positive)
	 * @param array<int> $func the signal in input 
	 * @return array<int> the input signal with poissonian noise
	 */
	public function addPoisson($func) {
		for ($i = 0; $i < count($func); $i++)
			$r[$i] = $this->poidev($func[$i]);
			
		return $r;
	}
	
	private function gammln($xx) {
        $cof = array(76.18009173, -86.50532033, 24.01409822, -1.231739516, 0.120858003e-2, -0.536382e-5);
                 
        $x = $xx - 1.0;
        $tmp = $x + 5.5;
        $tmp -= ($x + 0.5) * log($tmp);
        $ser = 1.0;
        
        for ($j = 0; $j <= 5; $j++) {
        	$x += 1.0;
            $ser += $cof[$j] / $x;
        }
        
        return -$tmp + log(2.50662827465 * $ser);
	}
	
	private function poidev($xm) {
		$oldm = -1;
		$g = 0;
	
		if ($xm < 12) {
			if ($xm != $oldm) {
				$oldm = $xm;
				$g = exp(-$xm);
			}
			
			$em = -1;
			$t = 1;
			
			do {
				$em += 1;
				$t *= rand() / getrandmax();
			} while ($t > $g);			
		} else {
			if ($xm != $oldm) {
				$oldm = $xm;
				$sq = sqrt(2 * $xm);
				$alxm = log($xm);
				$g = $xm * $alxm - $this->gammln($xm + 1);
			}
			do {
				do {
					$y = tan(M_PI * rand() / getrandmax());
					$em = $sq * $y + $xm;
				} while ($em < 0);
				
				$em = floor($em);
				$t = 0.9 * (1 + $y * $y) * exp($em * $alxm - $this->gammln($em + 1) - $g);
			} while (rand() / getrandmax() > $t);
		}
		
		return $em;
	}
	
	/**
	 * Return the media of the array in input
	 * @param array<double> $func
	 * @return double
	 */
	public function media($func) {
		$sum = 0;
		
		for ($i = 0; $i < count($func); $i++)
			$sum += $func[$i];
	
		return $sum / count($func);
	}
	
	/**
	 * Return the variance of the array in input
	 * @param array<double> $func
	 * @return double
	 */
	public function variance($func) {
		$sum = 0;
		$med = $this->media($func);
		
		for ($i = 0; $i < count($func); $i++)
			$sum += pow(($func[$i] - $med), 2);
		
		return $sum / (count($func) - 1);
	}
	
	/**
	 * Return the standard deviation of the array in input
	 * @param array<double> $func
	 * @return double
	 */
	public function stdDev($func) {
		$sum = 0;
		$med = $this->media($func);
		
		for ($i = 0; $i < count($func); $i++)
			$sum += pow(($func[$i] - $med), 2);
		
		return sqrt($sum / (count($func) - 1));
	}
}

?>