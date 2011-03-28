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

        if (!in_array($type,array('number','datetime'))) {
            throw new sfException(sprintf('Unknown type "%s"',$type));
        }


        if (!$column) {
            throw new sfException('No column defined. Cannot get series data!');
        }

        $series = $this->getSeries();

        $series_data = array();

        for ($s = 0; $s < count($series); $s++) {

            $serie = $series[$s];

            $raw_data = $serie->getRawData();


            $data = array();
            foreach ($raw_data as $key => $charge) {



                $v = $charge->get($column);
                if (!$v) {
                    throw new sfException(sprintf('Cannot get a value for column %s for charge id %d', $column, $charge->getId()));
                }

                if ('datetime' == $type) {
                    $v = strtotime($v);
                }

                $data[$key] = $v;
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


    public function buildXAxisDataByDateRange($dates) {

        $date_max = array();
        $date_min = array();
        foreach ($dates as $d) {
            $date_max[] = max($d);
            $date_min[] = min($d);
        }

        $date_max = max($date_max);
        $date_min = min($date_min);


        $year_min = date('Y', $date_min);
        $year_max = date('Y', $date_max);
        $years = range($year_min, $year_max);

        // building limits for each x-axis serie
        $dates_range = array();
        foreach (range($year_min, $year_max + 1) as $year) {
            $dates_range[] = strtotime($year . 'jan-1');
        }

        return array(
            'year_min'  => $year_min,
            'year_max'  => $year_max,
            'years'     => $years,
            'range'     => $dates_range,
        );

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


    /**
     * Returns data series as an array of GraphDataSerie
     * 
     * @return array of GraphDataSerie
     * @see GraphDataSerie
     */
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

    public static function filterValuesOutsideRange($array,$min,$max) {

        $filtered_array = array_filter($array, function ($element) use ($min, $max) {
                            return ($element <= $max && $element >= $min);
                        });


        return $filtered_array;

    }

}

