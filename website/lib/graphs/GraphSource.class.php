<?php

/**
 * Description of GraphSource
 *
 * @author Raffaele Bolliger (raffaele.bolliger at gmail.com)
 */
class GraphSource {

    protected $parameters = array();

    public function __construct() {
        
    }

    public function setParam($param, $value) {
        $this->parameters[$param] = $value;
    }

    public function getParam($param, $default = null) {

        return isset($this->parameters[$param]) ? $this->parameters[$param] : $default;
    }

    public function getSeriesCount() {

        $data = $this->getParam('series');

        return $data ? count(array_keys($data)) : null;
    }

    public function getSeriesDataByColumn($column, $type='number') {

        if (!$column) {
            throw new sfException('No column defined. Cannot get series data!');
        }

        $series = $this->getSeries();

        $series_data = array();

        for ($s = 0; $s < count($series); $s++) {

            $serie = $series[$s];

            $raw_data = $serie->getRawData();
            

            $data = array();
            foreach ($raw_data as $charge) {


                $v = $charge->get($column);
                if (!$v) {
                    throw new sfException(sprintf('Cannot get a value for column %s for charge id %d', $column, $charge->getId()));
                }

                if ('datetime' == $type) {
                    $v = strtotime($v);
                }

                $data[] = $v;
            }

            $series_data[$s] = $data;
        }

        return $series_data;
    }

    public function addParams($params) {

        if (!is_array($params) && !$params) {
            return;
        }

        foreach ($params as $key => $value) {
            $this->setParam($key, $value);
        }
    }

    public function buildXAxisData($series) {

        $data = array();
        foreach ($series as $serie) {
            $data = array_merge($data, $serie);
        }

        $data = array_unique($data);
        sort($data);

        return $data;
    }

    public function getYAxisDataByColumn($xi, $x_column, $y_column, $x_type='number', $y_type='number') {

//
//        $x_series = $this->getSeriesDataByColumn($x_column,$x_type);
//        $y_series = $this->getSeriesDataByColumn($y_column,$y_type);
//
//
//        $yi = array();
//        foreach ($x_series as $key => $x_values) {
//
//            $MIL = new Math_Interpolation_Lagrange($x_values, $y_series[$key], $xi);
//
//            $yi[$key] = $MIL->getInterpolants();
//        }
//
//        return $yi;
    }

    public function getSeries() {
        $series = $this->getParam('series');
        if (!$series) {
            throw new sfException('No series data found.');
        }

        return $series;
    }

    public function setSeries(array $series) {

        $s = array();
        foreach ($series as $serie) {

            if (!is_a($serie, 'GraphDataSerie')) {
                throw new sfException('setSeries() only accept GraphDataSerie instances');
            }
        }


        $this->setParam('series', $series);
    }

    public static function filterValuesLargerThan($array, $bound) {

        $filtered_array = array_filter($array, function ($element) use ($bound) {
                            return ($element <= $bound);
                        });


        return $filtered_array;
    }

}

