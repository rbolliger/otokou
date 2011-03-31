<?php
/**
* Math_Interpolation_Lagrange.php
*
* The Lagrange interpolation object accepts three arguments:
*
* 1. A data vector of x values
* 2. A data vector of y values
* 3. A vector of ix values, in the same range as your x values, that
*    you want to find the cooresponding interpolated values iy for.
*
* It uses the data vector of x and y values to compute the polynomial
* coefficients c and then uses these coefficients to find the interpolated
* values iy for the supplied ix values.  All supplied and computed results
* are stored as instance variables obtainable by accessor methods or
* direct reference.
*
* @author Paul Meagher
* @license LGPL
* @created Feb 25/2007
* @version 1.0
*/
class Math_Interpolation_Lagrange {

  /**
  * @var array of x values
  */
  var $x  = array();

  /**
  * @var array of y values
  */
  var $y  = array();

  /**
  * @var array of polynomial coefficients
  */
  var $c  = array();

  /**
  * @var array of x values to be interpolated
  */
  var $ix = array();

  /**
  * @var array of interpolated y values
  */
  var $iy = array();

  /**
  * @var number of elements in data arrays x and y
  */
  var $n = 0;

  /**
  * @var number of elements in interpolation arrays ix and iy
  */
  var $m = 0;


  /**
  * Constructor sets up the internal storage variables, calls a
  * method to compute the polynomial cooefficients, then finds
  * the interpolated iy values given the supplied ix values.
  *
  * @param $x array of x values
  * @param $y array of y values
  * @param $ix array of x values to be interpolated
  */
  function Math_Interpolation_Lagrange($x, $y, $ix)
  {

    $this->x  = $x;
    $this->y  = $y;

    $this->ix = $ix;

    $this->n  = count($x);
    $this->m  = count($ix);

    $this->setCoefficients();
    $this->setInterpolants();

  }

  /**
  * Computes the polynomial coefficients.
  */
  function setCoefficients()
  {
    for($i=0; $i<$this->n; $i++) {
      $d[$i] = 1;
      for($j=0; $j<$this->n; $j++) {
        if($i != $j)
          $d[$i] = $d[$i] * ($this->x[$i] - $this->x[$j]);
        $this->c[$i] = $this->y[$i] / $d[$i];
      }
    }
  }

  /**
  * Computes the interpolated iy values given the ix values
  * supplied in the constructor.
  */
  function setInterpolants()
  {
    for($i=0; $i<$this->m; $i++) {
      $this->iy[$i] = 0;
      for($j=0; $j<$this->n; $j++) {
        $d[$j] = 1;
        for($k=0; $k<$this->n; $k++) {
          if($j != $k)
            $d[$j] = $d[$j] * ($this->ix[$i] - $this->x[$k]);
        }
        $this->iy[$i] = $this->iy[$i] + $this->c[$j] * $d[$j];
      }
    }
  }

  /**
  * Recomputes the interpolated iy values given the new ix values.
  *
  * @param $ix array of new x values to be interpolated
  */
  function changeInterpolants($ix)
  {
    $this->ix = $ix;
    $this->iy = array();
    $this->m  = count($ix);
    $this->setInterpolants();
  }

  /**
  * @return the polynomial coefficients
  */
  function getCoefficients()
  {
    return $this->c;
  }

  /**
  * @return array of interpolated values
  */
  function getInterpolants()
  {
    return $this->iy;
  }

  /**
  * @return interpolated values as comma separated string
  */
  function toString()
  {
    return implode(", ", $this->iy);
  }

}
