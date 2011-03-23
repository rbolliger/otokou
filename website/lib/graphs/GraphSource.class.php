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

    public function buildXAxisDataByRangeTypeAndCalculationBase($range_type, $base_type, $options = array()) {

        // Checking that range_type is known
        if (!in_array($range_type, array_keys(GraphTable::getRangeTypes()))) {
            throw new sfException(sprintf('Unknown range type "%s".', $range_type));
        }

        // Checking that base_type exists
        if (!in_array($range_type, array_keys(GraphTable::getRangeTypes()))) {
            throw new sfException(sprintf('Unknown range type "%s".', $base_type));
        }


        // getting axis parameters
        $axis_params = $this->getAxisParametersByRangeType($range_type);
        $base_params = $this->getAxisParametersByRangeType($base_type);

        // getting data for x-axis column
        $x_column = $this->getSeriesDataByColumn($axis_params['column'], $axis_params['format']);


        // building x-axis data
        $x_data = $this->buildXAxisData($x_column);




        // getting data used as base column. This column is used to compute the chart values.
        if ($axis_params['column'] === $base_params['column']) {
            $base_data = $x_data;
            $base_column = $x_column;
        } else {

            $base_column = $this->getSeriesDataByColumn($base_params['column'], $base_params['format']);

            $base_data = array();

            foreach ($x_data as $key => $value) {

                $bda = array();

                foreach ($x_column as $xkey => $serie) {

                    $k = array_keys($serie, $value);

                    if (!$k) {
                        continue;
                    }

                    $bd = array_intersect_key($base_column[$xkey], array_combine($k, $k));

                    $bda = array_merge($bda, $bd);
                }

                $base_data[$key] = max($bda);
            }
        }


        // looking for data == 0 and changing its value
                if (isset($options['check_zeroes']) && $options['check_zeroes'] == true) {

                    $keys = array_keys($base_data,0);

                    foreach ($keys as $key) {
                        $base_data[$key] = isset($options['zero_approx']) ? $options['zero_approx'] : 0.01;
                    }
                    
                }


        $data = array(
            'value' => $x_data,
            'base' => $base_data,
            'x_column' => $x_column,
            'base_column' => $base_column,
        );

        return array_merge($data, $axis_params);
    }

    protected function getAxisParametersByRangeType($type) {

        switch ($type) {
            case 'date':

                $params = array(
                    'label' => 'Date',
                    'format' => 'datetime',
                    'column' => 'date',
                );

                break;

            case 'distance':

                $params = array(
                    'label' => 'Distance [km]',
                    'format' => 'number',
                    'column' => 'kilometers',
                );

                break;

            default:

                throw new sfException(sprintf('Unknown range type %s', $type));
                break;
        }

        return $params;
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

    public static function filterValuesDifferentThan($array, $value) {

        $filtered_array = array_filter($array, function ($element) use ($bound) {
                            return ($element == $value);
                        });


        return $filtered_array;
    }

}

