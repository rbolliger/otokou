<?php

/**
 * Description of ChartSource
 *
 * @author Raffaele Bolliger (raffaele.bolliger at gmail.com)
 */
class ChartSource {

    protected $parameters = array();

    public function __construct() {

    }

    public function setParam($param, $value) {
        $this->parameters[$param] = $value;
    }

    public function getParam($param, $default = null) {

        return isset($this->parameters[$param]) ? $this->parameters[$param] : $default;
    }

    public function getParams() {
        return $this->parameters;
    }

    public function getSeriesCount() {

        $data = $this->getParam('series');

        return $data ? count(array_keys($data)) : null;
    }

    public function getSeriesDataByColumn($column, $type='number', $params = array()) {

        if (!in_array($type, array('number', 'datetime'))) {
            throw new sfException(sprintf('Unknown type "%s"', $type));
        }


        if (!$column) {
            throw new sfException('No column defined. Cannot get series data!');
        }

//        $min = isset($params['min']) ? $params['min'] : null;
//        $max = isset($params['max']) ? $params['max'] : null;

        $series = $this->getSeries();

        $series_data = array();
        $values_found = false;

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

//                // filtering values
//                if ($min && $v < $min) {
//                    continue;
//                }
//                if ($max && $v > $max) {
//                    continue;
//                }

                $data[$key] = $v;

                if ($v) {
                    $values_found = true;
                }
            }

            $series_data[$s] = $data;
        }


        return $values_found ? $series_data : false;
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

    /**
     * Returns an array containing all the required data to build the x-axis of a chart.
     * The function provides data for two range types (if $range_type and $base_type are different)
     * in order to build the axis labels and the x-axis values required to build a chart.
     *
     * @param string $range_type Range type. Value must be one given by ChartTable::getRangeTypes()
     * @param string $base_type  Base type. Value must be one given by ChartTable::getRangeTypes()
     * @param array $options Function options. Available options are:
     *                      check_zeroes: the function checks if any value is equal to 0. This may
     *                                      create problems when the y-axis value is divided by the x-axis value.
     *                      zero_approx: this options sets the value to assign when 0 is found.
     * @return array  Array containing all required data to build the x-axis
     */
    public function buildXAxisDataByRangeTypeAndCalculationBase($range_type, $base_type, $options = array()) {

        // Checking that range_type is known
        if (!in_array($range_type, array_keys(ChartTable::getRangeTypes()))) {
            throw new sfException(sprintf('Unknown range type "%s".', $range_type));
        }

        // Checking that base_type exists
        if (!in_array($range_type, array_keys(ChartTable::getRangeTypes()))) {
            throw new sfException(sprintf('Unknown range type "%s".', $base_type));
        }


        // getting axis parameters
        $x_params = $this->getAxisParametersByRangeType($range_type);
        $base_params = $this->getAxisParametersByRangeType($base_type);

        // getting data for x-axis column
        $x_column = $this->getSeriesDataByColumn($x_params['column'], $x_params['format']);

        // getting maxium and minimum value for each serie
        $x_limits = $this->calcualteLimitsForSeriers($x_column);

        // building x-axis data
        $x_data = $this->buildXAxisData($x_column);

        // getting data used as base column. This column is used to compute the chart values.
        if ($x_params['column'] === $base_params['column']) {
            $base_data = $x_data;
            $base_column = $x_column;
            $base_limits = $x_limits;
        } else {

            $base_column = $this->getSeriesDataByColumn($base_params['column'], $base_params['format']);

            // getting limits corresponding to x-axis limits
            $base_limits = array();
            $entries = array('min', 'max');
            foreach ($base_column as $key => $serie) {

                $x_c = $x_column[$key];
                if (!$x_c) {
                    $base_limits[$key]['min'] = null;
                    $base_limits[$key]['max'] = null;
                } else {

                    foreach ($entries as $entry) {
                        // finding the position of the minimum
                        $k = array_keys($x_c, $x_limits[$key][$entry]);
                        $values = array_intersect_key($serie, array_combine($k, $k));

                        $base_limits[$key][$entry] = call_user_func($entry, $values);
                    }
                }
            }


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

            $keys = array_keys($base_data, 0);

            foreach ($keys as $key) {
                $base_data[$key] = isset($options['zero_approx']) ? $options['zero_approx'] : 0.01;
            }
        }

        // filtering data if any limit is set
        if ($x_params['min']) {

            $f = $this->filterValuesSmallerThan($x_data, $x_params['min']);
            if ($f) {
                $min_id = min(array_keys($x_data, min($f)));
            } else {
                $min_id = 0;
            }
        } else {
            $min_id = 0;
        }

        if ($x_params['max']) {
            $max_id = max(array_keys($x_data, max($this->filterValuesLargerThan($x_data, $x_params['max']))));
        } else {
            $max_id = count($x_data);
        }

        $length = $max_id - $min_id + 1;
        $x_data = array_slice($x_data, $min_id, $length);
        $base_data = array_slice($base_data, $min_id, $max_id - $min_id + 1);

        $data = array(
            'value' => $x_data,
            'base' => $base_data,
            'x_column' => $x_column,
            'base_column' => $base_column,
            'x_limits' => $x_limits,
            'base_limits' => $base_limits,
        );

        return array_merge($data, $x_params);
    }

    public function buildXAxisDataByDateRange($dates, $unit) {

        if (!$dates) {
            throw new sfException('No dates provided. Cannot build axis');
        }


        $units = array('year', 'month');
        if (!in_array($unit, $units)) {
            throw new sfException(sprintf('Unknown unit "%s". Accepted values are %s', $unit, implode(', ', $units)));
        }


        $date_max = array();
        $date_min = array();
        foreach ($dates as $d) {

            if (!is_array($d)) {
                throw new sfException(__METHOD__ . ' expects a two-level array $dates. Single-level array provided.');
            }

            $date_max[] = max($d);
            $date_min[] = min($d);
        }

        $date_max = max($date_max);
        $date_min = min($date_min);

        $year_min = date('Y', $date_min);
        $year_max = date('Y', $date_max);


        $dates_range = array();
        $labels = array();

        if ('year' == $unit) {

            $lab = range($year_min, $year_max);

            $labels = array();
            foreach ($lab as $l) {
                $labels[] = (string) $l;
            }

            foreach (range($year_min, $year_max + 1) as $key => $year) {
                $dates_range[] = strtotime($year . '-1-1');
            }

            $description = 'Years';
        } else {

            $month_min = date('m', $date_min);
            $month_max = date('m', $date_max);

            $years = range($year_min, $year_max);


            foreach ($years as $key => $year) {

                if ($year_min === $year_max) {
                    $idx_start = $month_min;
                    $idx_stop = $month_max;
                } elseif ($key == 0) {
                    $idx_start = $month_min;
                    $idx_stop = 12;
                } elseif ($key == count($years) - 1) {
                    $idx_start = 1;
                    $idx_stop = $month_max;
                } else {
                    $idx_start = 1;
                    $idx_stop = 12;
                }

                for ($month = $idx_start; $month < $idx_stop + 1; $month++) {

                    $date = new DateTime(sprintf('%s-%s-01', $year, $month));

                    $labels[] = $date->format('Y-M');

                    $dates_range[] = strtotime(sprintf('%s-%s-01', $year, $month));
                }
            }

            // adding one more value to ranges
            if ($month < 12) {
                $month++;
            } else {
                $year++;
                $month = 1;
            }
            $dates_range[] = strtotime(sprintf('%s-%s-01', $year, $month));

            $description = 'Month';
        }


        return array(
            'year_min' => $year_min,
            'year_max' => $year_max,
            'labels' => $labels,
            'range' => $dates_range,
            'description' => $description,
        );
    }

    protected function getAxisParametersByRangeType($type, $calculate_limits = true) {

        switch ($type) {
            case 'date':

                $min = false;
                $max = false;
                if ($calculate_limits) {

                    // lower bound
                    if ($p = $this->getParam('date_from', false)) {
                        $min = strtotime($p);
                    } elseif ($p = $this->getParam('kilometers_from')) {
                        $min = $this->getDateForDistance($p, 'min');
                    }

                    // upper bound
                    if ($p = $this->getParam('date_to', false)) {
                        $max = strtotime($p);
                    } elseif ($p = $this->getParam('kilometers_to')) {
                        $max = $this->getDateForDistance($p, 'max');
                    }
                }

                $params = array(
                    'label' => 'Date',
                    'format' => 'datetime',
                    'column' => 'date',
                    'min' => $min,
                    'max' => $max,
                );

                break;

            case 'distance':

                $min = false;
                $max = false;
                if ($calculate_limits) {

                    // lower bound
                    if ($p = $this->getParam('kilometers_from', false)) {
                        $min = $p;
                    } elseif ($p = $this->getParam('date_from')) {
                        $min = $this->getDistanceForDate($p, 'min');
                    }

                    // upper bound
                    if ($p = $this->getParam('kilometers_to', false)) {
                        $max = $p;
                    } elseif ($p = $this->getParam('date_to')) {
                        $max = $this->getDistanceForDate($p, 'max');
                    }
                }

                $params = array(
                    'label' => 'Distance [km]',
                    'format' => 'number',
                    'column' => 'kilometers',
                    'min' => $min,
                    'max' => $max,
                );

                break;

            default:

                throw new sfException(sprintf('Unknown range type %s', $type));
                break;
        }

        return $params;
    }

    /**
     * Returns data series as an array of ChartDataSerie
     * 
     * @return array of ChartDataSerie
     * @see ChartDataSerie
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

            if (!is_a($serie, 'ChartDataSerie')) {
                throw new sfException('setSeries() only accept ChartDataSerie instances');
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

    public static function filterValuesSmallerThan($array, $bound) {

        $filtered_array = array_filter($array, function ($element) use ($bound) {
                            return ($element >= $bound);
                        });


        return $filtered_array;
    }

    public static function filterValuesDifferentThan($array, $value) {

        $filtered_array = array_filter($array, function ($element) use ($value) {
                            return ($element == $value);
                        });


        return $filtered_array;
    }

    public static function filterValuesOutsideRange($array, $min, $max) {

        $filtered_array = array_filter($array, function ($element) use ($min, $max) {
                            return ($element < $max && $element >= $min);
                        });


        return $filtered_array;
    }

    /**
     * Builds the data required to build a cost-per-km chart
     *
     * @param string $range_type Defines the type of range to be used as x-axis labels. The value must be one in
     *                              ChartTable::getRangeTypes().
     *
     * @return array $data 
     */
    public function buildCostPerKmChartData($range_type) {

        $data = array();
        $data['title'] = 'Relative Cost [CHF/km]';

        // X-axis
        $options = array(
            'check_zeroes' => true,
            'zero_approx' => 0.01,
        );
        $x_data = $this->buildXAxisDataByRangeTypeAndCalculationBase($range_type, 'distance', $options);

        $data['x'] = array(
            'id' => 'x-axis',
            'values' => $x_data['value'],
            'description' => $x_data['label'],
        );


        // Y-axis
        $cost = $this->buildYAxisDataBySum('amount', $x_data, 'calculateCostSum');

        $data['y']['series'] = $cost;
        $data['y']['description'] = 'Cost [CHF/km]';

        return $data;
    }

    public function buildCostPerYearChartData($range_type) {

        $data = array();
        $data['title'] = 'Total Cost [CHF/year]';

        // x-axis
        $x_data = $this->buildXAxisDataByRangeTypeAndCalculationBase($range_type, 'date');
        $x_dates = $this->buildXAxisDataByDateRange(array($x_data['base']), 'year');

        $data['x']['id'] = 'x-axis';
        $data['x']['values'] = $x_dates['labels'];
        $data['x']['description'] = $x_dates['description'];
        // Y-axis
        $data['y']['series'] = array();
        $data['y']['description'] = "Annual costs [CHF/year]";

        $costs = $this->getSeriesDataByColumn('amount', 'number');
        $y_series = $this->getSeries();

        $y_data = array();
        foreach ($x_data['base_column'] as $skey => $serie) {

            for ($index = 0; $index < count($x_dates['range']) - 1; $index++) {

                // removing x elements that are larger than bound
                $filter = $this->filterValuesOutsideRange($serie, $x_dates['range'][$index], $x_dates['range'][$index + 1]);

                if (!$filter) {
                    $y_cost = 0;
                } else {

                    // getting corresponding y elements
                    $y_filtered = array_intersect_key($costs[$skey], $filter);

                    $y_cost = array_sum($y_filtered);
                }

                $y_data[$skey][$index] = $y_cost;
            }

            $data['y']['series'][$skey] = array(
                'id' => $y_series[$skey]->getId(),
                'label' => $y_series[$skey]->getLabel(),
                'values' => $y_data[$skey],
            );
        }

        return $data;
    }

    public function buildCostPieChartData($options = array()) {

        if (!isset($options['categories'])) {
            throw new sfException('Categories list is required.');
        }

        if (!isset($options['vehicles'])) {
            throw new sfException('Vehicles list is required.');
        }

        if (!isset($options['vehicle_display'])) {
            throw new sfException('vehicle_display option is required.');
        }


        $data = array();
        $data['title'] = 'Cost allocation [CHF]';

        // get amounts for each serie
        $amounts = $this->getSeriesDataByColumn('amount', 'number');

        // list of all the requested categories and vehicles
        $categories = $options['categories'];
        $vehicles = $options['vehicles'];
        $vehicles_display = $options['vehicle_display'];

        $vid_default = 1;

        // initialization: a cell for each initially requested category and vehicle
        $values = array_combine($categories['list'], array_fill(0, $categories['count'], 0));

        if ('stacked' == $vehicles_display) {
            $keys = array_fill(0, $vehicles['count'], $vid_default);
        } else {
            $keys = $vehicles['list'];
        }
        $values = array_combine($keys, array_fill(0, $vehicles['count'], $values));

        // filling $data with the real values
        $series = $this->getSeries();
        $description = array();
        foreach ($series as $key => $serie) {
            $vid = $serie->getVehicleId();

            $description[] = count($vid) > 1 ? 'All vehicles' : Doctrine_Core::getTable('Vehicle')->findOneById($vid)->getName();

            // if $vid has more than one element, vehicles are stacked, so we got only one chart
            if (count($vid) > 1) {
                $vid = $vid_default;
            }

            $cid = $serie->getCategoryId();
            if (count($cid) != 1) {
                throw new sfException(sprintf('Serie "%s" is defined by more than one category. This is not allowed by this function. Category display must be single.', $serie->getLabel()));
            }

            $value = array_sum($amounts[$key]);
            $values[$vid][$cid[0]] = $value;
        }

        $data['y']['series'] = array();
        $data['y']['description'] = "Cost allocation [CHF]";


        // building chart data for each vehicle
        $counter = -1;
        foreach ($values as $key => $value) {

            $counter++;

            $points = array_values($values[$key]);

            // If all values are VOID, we skip the serie
            if ($points === array_fill(0, count($points), 0)) {
                continue;
            }

            $id = $series[$counter]->getId();
            $data['y']['series'][$counter] = array(
                'id' => $id,
                'label' => $description[$counter],
                'values' => $points,
            );
        }

        // adding labels
        $data['x'] = array(
            'id' => 'labels',
            'values' => $categories['names'],
            'description' => '',
        );

        return $data;
    }

    public function buildTripChartData($unit) {

        $data = array();


        $units = array('year', 'month');
        if (!in_array($unit, $units)) {
            throw new sfException(sprintf('Unknown unit "%s". Accepted values are %s', $unit, implode(', ', $units)));
        }

        $cd = $this->getParam('category_display');
        if ('stacked' !== $cd) {
            throw new sfException(sprintf('category_display must be set to "stacked" in ' . __METHOD__ . ' The value is set to "%s" ', $cd));
        }

        $title = $unit == 'year' ? 'Annual travel [km/year]' : 'Monthly travel [km/year]';
        $data['title'] = $title;


        // x-axis
        $dates = $this->getSeriesDataByColumn('date', 'datetime');

        $x_dates = $this->buildXAxisDataByDateRange($dates, $unit);

        $data['x'] = array(
            'id' => 'x-axis',
            'values' => $x_dates['labels'],
            'description' => $x_dates['description'],
        );


        // Y-axis
        $kilometers = $this->getSeriesDataByColumn('kilometers', 'number');
        $y_series = $this->getSeries();

        $title = $unit == 'year' ? "Annual travel [km/year]" : 'Monthly travel [km/month]';

        $data['y']['series'] = array();
        $data['y']['description'] = $title;

        $y_data = array();
        foreach ($dates as $skey => $serie) {

            $prev_y = false;

            for ($index = 0; $index < count($x_dates['range']) - 1; $index++) {
                // removing elements outside the required range
                $filter = $this->filterValuesOutsideRange($serie, $x_dates['range'][$index], $x_dates['range'][$index + 1]);


                if (!$filter) {
                    $y_travel = 0;
                } else {

                    // getting corresponding y elements
                    $y_filtered = array_intersect_key($kilometers[$skey], $filter);


                    // set the first value form prev_y, which is not 0, but the lowest distance runned by
                    // the vehicle. This depends on the filters applied by the user.
                    if (false === $prev_y) {

                        $m_y = array();
                        $min = min($filter);
                        foreach ($filter as $k => $v) {
                            if ($v == $min) {
                                array_push($m_y, $y_filtered[$k]);
                            }
                        }
                        $prev_y = min($m_y);
                    }

                    $m_y = array();
                    $max = max($filter);
                    foreach ($filter as $k => $v) {
                        if ($v == $max) {
                            array_push($m_y, $y_filtered[$k]);
                        }
                    }
                    $max = max($m_y);


                    $y_travel = $max - $prev_y;
                    $prev_y = $max;
                }

                $y_data[$skey][$index] = $y_travel;
            }


            $id = $y_series[$skey]->getId();
            $data['y']['series'][$skey] = array(
                'id' => $id,
                'label' => $y_series[$skey]->getLabel(),
                'values' => $y_data[$skey],
            );
        }

        return $data;
    }

    public function buildConsumptionPerDistanceChartData($range_type) {

        // checking that the right category is selected
        $cid = Doctrine_Core::getTable('Category')->findOneByName('Fuel')->getId();
        $cid = array($cid);
        if ($cid != $this->getParam('categories_list')) {
            throw new sfException(sprintf('This function requires that only category "Fuel" is selected. It seems that other categories (on none) are also selected (ids: %s).', implode(', ', $this->getParam('categories_list', array('None')))));
        }


        $data = array();
        $data['title'] = 'Consumption [l/100km]';


        // X-axis
        $x_data = $this->buildXAxisDataByRangeTypeAndCalculationBase($range_type, 'distance');

        $data['x'] = array(
            'id' => 'x-axis',
            'values' => $x_data['value'],
            'description' => $x_data['label'],
        );

        // Y-axis
        $consumption = $this->buildYAxisDataBySum('quantity', $x_data, 'calculateConsumptionPerDistance');

        $data['y']['series'] = $consumption;
        $data['y']['description'] = 'Consumption [l/100km]';

        return $data;
    }

    function buildYAxisDataBySum($column, $x_data, $y_eval) {


        $y_columns = $this->getSeriesDataByColumn($column);

        $x_base = $x_data['base'];
        $x_values = $x_data['value'];
        $x_column = $x_data['x_column'];
        $x_limits = $x_data['x_limits'];
        $base_limits = $x_data['base_limits'];

        $y_data = array();
        $y_series = $this->getSeries();

        $y_sum = array();
//print_r($x_data);
        foreach ($y_columns as $ykey => $y_values) {

            $base_0 = $base_limits[$ykey]['min'];

            foreach ($x_values as $bkey => $bound) {

                // removing x elements that are larger than bound
                $filter = $this->filterValuesLargerThan($x_column[$ykey], $bound);


                if (!count($filter) || $bound > $x_limits[$ykey]['max']) {
                    $cost = null;
                } else {

                    // getting corresponding y elements
                    $y_filtered = array_intersect_key($y_values, $filter);
//print_r($y_filtered); echo "distance: ".$x_base[$bkey].' - '.$base_0."\n";
                    $distance = $x_base[$bkey] - $base_0;

                    $cost = $this->$y_eval($y_filtered, $distance);
                }

                // assigning result to temporary array
                $y_sum[$ykey][$bkey] = $cost;
            }


            $y_data[$ykey] = array(
                'id' => $y_series[$ykey]->getId(),
                'label' => $y_series[$ykey]->getLabel(),
                'values' => $y_sum[$ykey],
            );
        }

        return $y_data;
    }

    protected function calculateCostSum($costs, $distance) {

        if ($distance == 0) {
            return null;
        }

        return array_sum($costs) / $distance;
    }

    protected function calculateConsumptionPerDistance($consumptions, $distance) {

        if ($distance == 0) {
            $distance = 0.01;
        }

        return array_sum($consumptions) / $distance * 100;
    }

    protected function getDateForDistance($dist, $minOrMax) {

        $dates = $this->getSeriesDataByColumn('date', 'datetime');
        $kilometers = $this->getSeriesDataByColumn('kilometers', 'number');

        $dt = array();
        foreach ($kilometers as $skey => $serie) {

            if ('max' == $minOrMax) {
                $filter = $this->filterValuesLargerThan($serie, $dist);
            } else {
                $filter = $this->filterValuesSmallerThan($serie, $dist);
            }


            if ($filter) {
                foreach ($filter as $kkey => $kvalue) {
                    $dt[] = $dates[$skey][$kkey];
                }
            }
        }

        if ('max' == $minOrMax) {
            $value = max($dt);
        } else {
            $value = min($dt);
        }

        return $value;
    }

    protected function getDistanceForDate($date, $minOrMax) {

        $dates = $this->getSeriesDataByColumn('date', 'datetime');
        $kilometers = $this->getSeriesDataByColumn('kilometers', 'number');

        $date = strtotime($date);

        $dt = array();
        foreach ($dates as $skey => $serie) {

            if ('max' == $minOrMax) {
                $filter = $this->filterValuesLargerThan($serie, $date);
            } else {
                $filter = $this->filterValuesSmallerThan($serie, $date);
            }


            if (count($filter)) {
                foreach ($filter as $kkey => $kvalue) {
                    $dt[] = $kilometers[$skey][$kkey];
                }
            }
        }

        if (!count($dt)) {
            return false;
        }

        if ('max' == $minOrMax) {
            $value = max($dt);
        } else {
            $value = min($dt);
        }

        return $value;
    }

    protected function calcualteLimitsForSeriers($series) {

        $limits = array();
        foreach ($series as $key => $serie) {
            if (count($serie)) {
                $limits[$key] = array(
                    'min' => min($serie),
                    'max' => max($serie),
                );
            } else {
                $limits[$key] = array(
                    'min' => null,
                    'max' => null,
                );
            }
        }

        return $limits;
    }

}

