<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of ChartBuilder
 *
 * @author Raffaele Bolliger (raffaele.bolliger@gmail.com)
 */
class ChartBuilder {

    protected $parameters;
    protected $chart_query;
    protected $options = array();
    protected $attributes = array();
    protected $chart;
    protected $chart_source;
    protected $logger;

    public function __construct(array $parameters, array $options = array(), array $attributes = array()) {
       
        $this->setParameters($parameters);


        $this->setOptions($this->getDefaultOptions());
        $this->setOptions($options);

        $this->setAttributes($attributes);

        $this->setLogger($this->getOption('logger', new sfNoLogger(new sfEventDispatcher())));
    }

    public function doDisplay() {
        return 'This function cannot directly plot a chart.';
    }

    public function doGenerate() {

        $done = true;

        $name = $this->getParameter('chart_name');
               
        switch ($name) {
            case 'cost_per_km':

                $data = $this->buildCostPerKmChartData();

                break;

            case 'cost_per_year':
                $data = $this->buildCostPerYearChartData();

                break;

            case 'cost_pie':

                $this->setParameter('category_display', 'single');

                $data = $this->buildCostPieChartData();
                break;

            case 'trip_annual':

                //$this->setParameter('range_type', 'date');
                $this->setParameter('category_display', 'stacked');

                $options = array(
                   'range_type' => $this->getParameter('range_type'),
                   'unit'       => 'year',
                );
                $data = $this->buildTripChartData($options);
                break;

            case 'trip_monthly':

                //$this->setParameter('range_type', 'date');
                $this->setParameter('category_display', 'stacked');

                $options = array(
                   'range_type' => $this->getParameter('range_type'),
                   'unit'       => 'month',
                );
                $data = $this->buildTripChartData($options);
                break;

            case 'consumption_per_distance':

                $cid = Doctrine_Core::getTable('Category')->findOneByName('Fuel')->getId();
                $this->setParameter('categories_list', array($cid));

                $data = $this->buildConsumptionPerDistanceChartData();
                break;

            default:

                throw new sfException(sprintf('Unknown chart name %s', $name));
                break;
        }

        return $data;
    }

    public function __toString() {

        return $this->display();
    }

    public function setLogger(sfLogger $logger) {
        $this->logger = $logger;
    }

    public function getLogger() {
        return $this->logger;
    }

    public function display() {

        $done = $this->generate();

        if ($done) {
            return $this->doDisplay();
        } else {
            return 'Not enough data do draw a chart. Please, change some criteria.';
        }
    }

    public function addAttributes(array $attributes) {

        $this->attributes = array_merge($this->attributes, $attributes);
    }

    public function setAttributes(array $attributes) {

        $this->attributes = $attributes;
    }

    public function getAttributes() {

        return $this->attributes;
    }

    public function getChartsWebPath() {

        return $this->getChart()->getChartsWebPath();
    }
    
    public function getChartFileSystemPath() {

        return $this->getChart()->getChartFileSystemPath();
    }
    
    public function getChartFileWebPath() {

        return $this->getChart()->getChartFileWebPath();
    }

    public function getChartsSystemPath() {

        return $this->getChart()->getChartsSystemPath();
    }

    public function getChartName() {
        return $this->getChart()->getChartName();
    }

    public function getChartFormat() {

        return $this->getChart()->getChartFormat();
    }

    public function getChart() {
        if (!isset($this->chart) || !$this->chart) {
            $this->retrieveOrCreate();
        }

        return $this->chart;
    }

    public function getOption($name, $default = null) {

        return isset($this->options[$name]) ? $this->options[$name] : $default;
    }

    public function setOptions($options) {
        $this->options = array_merge($this->options, $options);
    }

    public function setOption($option, $value) {
        $this->options[$option] = $value;
    }

    public function setParameters(array $parameters) {

        $this->parameters = array_merge($this->getDataDefaults(), $parameters);
        $this->clearGeneratedElements();
    }

    public function setParameter($parameter, $value) {

        $this->parameters[$parameter] = $value;
        $this->clearGeneratedElements();
    }

    public function addParameters(array $parameters) {

        $this->parameters = array_merge($this->parameters, $parameters);

        $this->clearGeneratedElements();
    }

    public function getParameter($name, $default = null) {

        return isset($this->parameters[$name]) ? $this->parameters[$name] : $default;
    }

    public function getParameters() {

        return $this->parameters;
    }

    public function retrieveOrCreate() {

        if (!$this->chart_query) {
            $this->buildChartsQuery();
        }
        $coll = $this->getChartsQueryResults();

        // Ensuring that at most one element is retrieved
        if (!count($coll)) {

            $chart = $this->saveNewChart();
        }

        // Ok, we have already one
        else {

            $this->chart = $coll[0];
        }

        return $this->chart;
    }

    public function generate() {

        $done = true;

        // Does the Chart object has been retrived from the DB?
        if (!$this->chart) {
            $this->retrieveOrCreate();
        }

        // If the source is already available, we stop here
        if ($this->chartSourceIsAvailable() && !$this->doForceGenerate()) {
            $this->getLogger()->info(sprintf('Chart %s exists. Skipping generation.',$this->getChartFileSystemPath()));

            return $done;
        }

        if (!$this->doForceGenerate()) {
            $this->getLogger()->info(sprintf('Chart %s picture does not exist exist. Generating it.',$this->getChartFileSystemPath()));
        } else {
            $this->getLogger()->info(sprintf('Chart %s generation was forced or file does not exist. File is generated.',$this->getChartFileSystemPath()));
        }


        return $this->doGenerate();
    }

    public function chartSourceIsAvailable() {

        // Does the Chart object has been retrived from the DB?
        $this->getChart();

        // Checking that the base path exists
        $this->checkPath($this->getChartsSystemPath());

        return $this->checkPath($this->getChartFileSystemPath(), false);
    }

    public function checkPath($path, $create = true) {

        if (false === strpos($path, sfConfig::get('sf_root_dir'))) {
            throw new sfException(sprintf('checkPath() only accepts system paths. Got "%s" instead.', $path));
        }

        $exists = file_exists($path);
        if (!$exists && $create) {
            $fs = new sfFilesystem();
            $fs->mkdirs($path);
            $exists = true;
        }

        return $exists;
    }

    public function getChartsQueryResults() {

        if (!$this->chart_query) {
            $this->buildChartsQuery();
        }

        // trying to recover a chart in DB, if it exists
        $chart = $this->chart_query->execute();


        if ($count = count($chart) > 1) {

            throw new sfException('More than one chart can be retrieved with the requested criteria. Something wrong here!');
        }

        return $chart;
    }

    public function getDefaultOptions() {

        return array(
        );
    }

    public function getQuery() {

        if (!$this->chart_query) {
            $this->buildChartsQuery();
        }

        return $this->chart_query;
    }

    /**
     * Builds and stores a ChartSource object, containing the raw data required to
     * plot the chart.
     *
     * @see ChartSource
     *
     * @return true of false, depending if a ChartSource has been correctly built.
     */
    public function buildChartSource() {

        $gs = new ChartSource();
        $gs->addParams($this->getParameters());

        // setting chart parameters
        $gs->addParams($this->getOption('chart_parameters'));

        // getting source data
        $vehicle_display = $this->getParameter('vehicle_display', 'single');
        $category_display = $this->getParameter('category_display', 'stacked');

        $series = $this->getChartSourceData($vehicle_display, $category_display);

        if (!$series) {
            return false;
        }

        $gs->setSeries($series);

        $this->getLogger()->info(sprintf('Chart %s source does not exist exist. Building chart data source.', $this->getChartName()));

        $this->chart_source = $gs;

        return true;
    }

    public function getChartSourceData($vehicle_display, $category_display) {



        if ($vehicle_display == 'stacked' && $category_display == 'stacked') {
            $case = 1;
        } elseif ($vehicle_display == 'stacked' && $category_display == 'single') {
            $case = 2;
        } elseif ($vehicle_display == 'single' && $category_display == 'stacked') {
            $case = 3;
        } elseif ($vehicle_display == 'single' && $category_display == 'single') {
            $case = 4;
        } else {
            throw new sfException(sprintf('Something wrong with nput parameters "%s" and "%s". Cannot define a case', $vehicle_display, $category_display));
        }


        $vl = self::getVehiclesList($this->getParameter('vehicles_list', array()), $this->getParameter('user_id'));
        $cl = self::getCategoriesList($this->getParameter('categories_list', array()));

        // If no cars, we won't display anything
        if (empty($vl['list'])) {

            $this->getLogger()->info('No cars found. Chart won\'t be generated.');

            return false;
        }

        // Building arrays containing the indexes of categories and vehicles to be considered to build each serie
        switch ($case) {
            case 1:

                $v_idx[0] = $vl['list'];
                $c_idx[0] = $cl['list'];

                break;

            case 2:

                for ($indexC = 0; $indexC < $cl['count']; $indexC++) {

                    $v_idx[$indexC] = $vl['list'];
                    $c_idx[$indexC] = $cl['list'][$indexC];
                }
                break;

            case 3:
                for ($indexV = 0; $indexV < $vl['count']; $indexV++) {

                    $v_idx[$indexV] = $vl['list'][$indexV];
                    $c_idx[$indexV] = $cl['list'];
                }

                break;


            case 4:
                $ns = -1;
                for ($indexC = 0; $indexC < $cl['count']; $indexC++) {
                    for ($indexV = 0; $indexV < $vl['count']; $indexV++) {

                        $ns++;

                        $v_idx[$ns] = $vl['list'][$indexV];
                        $c_idx[$ns] = $cl['list'][$indexC];
                    }
                }

                break;

            default :
                throw new sfException('Something wrong here. Cannot get the right case.');
                break;
        }


        $ns = -1;
        $series = array();
        foreach ($v_idx as $key => $vid) {

            $cid = $c_idx[$key];


            $q = $this->buildChargeQuery($vid, $cid);

            $charges = $q->execute();
 
            if (!count($charges)) {
                continue;
            }

            $label = $this->buildLabelForSerie(array(
                        'vehicle_display' => $vehicle_display,
                        'vehicle_id' => $vid,
                        'category_display' => $category_display,
                        'category_id' => $cid,
                    ));

            $ns++;

            $params = array(
                'id' => 'Serie_' . $ns,
                'raw_data' => $charges,
                'label' => $label,
                'vehicle_id' => $vid,
                'category_id' => $cid,
            );

            $series[$ns] = new ChartDataSerie($params);
        }


        if (!count($series)) {
            return false;
        }

        return $series;
    }

    /**
     * Returns a ChartSource object, containing the raw data required to build the chart.
     * 
     * @return ChartSource
     * @see ChartSource
     */
    public function getChartSource() {

        if (!$this->chart_source) {
            $done = $this->buildChartSource();

            if (!$done) {
                return $done;
            }
        }

        return $this->chart_source;
    }

    public function reloadChart() {

        $g = $this->getChart();

        $g->refresh();
    }

    public function doForceGenerate() {

        return sfConfig::get('app_charts_force_generate', false);
    }

    protected function buildLabelForSerie($params) {


        if ($params['vehicle_display'] == 'single' && $params['category_display'] == 'stacked') {

            $v = Doctrine_Core::getTable('Vehicle')->findOneById($params['vehicle_id']);

            $label = $v->getName();
        }

        if ($params['vehicle_display'] == 'single' && $params['category_display'] == 'single') {

            $v = Doctrine_Core::getTable('Vehicle')->findOneById($params['vehicle_id']);
            $c = Doctrine_Core::getTable('Category')->findOneById($params['category_id']);

            $label = $v->getName() . ' - ' . $c->getName();
        }

        if ($params['vehicle_display'] == 'stacked' && $params['category_display'] == 'single') {
            $c = Doctrine_Core::getTable('Category')->findOneById($params['category_id']);

            $label = $c->getName();
        }

        if ($params['vehicle_display'] == 'stacked' && $params['category_display'] == 'stacked') {

            $label = 'All vehicles and categories';
        }

        return $label;
    }

    protected function saveNewChart() {

        $chart = new Chart();

        $params = $this->parameters;

        $foreign = array(
            'categories_list' => 'Categories',
            'vehicles_list' => 'Vehicles',
        );


        $chart->fromArray($this->parameters);

        foreach ($foreign as $field => $class) {

            if (isset($params[$field])) {

                $chart->link($class, $params[$field]);
            }
        }

        try {
            $chart->save();
        } catch (Exception $exc) {
            $sfe = new sfException();
            throw $sfe->createFromException($exc);
        }



        $this->chart = $chart;

        return $chart;
    }

    protected function buildChartsQuery() {

        $locals = array(
            'vehicle_display',
            'user_id',
            'category_display',
            'date_from',
            'date_to',
            'kilometers_from',
            'kilometers_to',
            'range_type',
            'format',
            'chart_name',
        );

        $foreign = array(
            'vehicles_list' => array('model' => 'ChartVehicle', 'column' => 'vehicle_id'),
            'categories_list' => array('model' => 'ChartCategory', 'column' => 'category_id'),
        );


        $q = Doctrine_Query::create()->from('Chart g')->select('g.*');
        $q->addGroupBy('g.id');

        foreach ($this->parameters as $key => $value) {

            // if this is a local column
            if (in_array($key, $locals)) {

                if (count($value) == 1) {
                    $q->andWhere('g.' . $key . ' = ?', $value);
                } elseif (count($value) > 1) {
                    $q->andWhereIn('g.' . $key, $value);
                } else {
                    $q->andWhere('g.' . $key . ' IS NULL');
                }
            }
            // if this is a foreign reference
            elseif (in_array($key, array_keys($foreign))) {

                $fname = $key . '_all';
                $q->leftJoin('g.' . $foreign[$key]['model'] . ' ' . $fname);

                // ensuring that there are no more and no less foreign elements than those requested
                $suff = '_all_sq';
                $fname = $key . $suff;
                $root = 'g' . $fname;
                $sq = $q->createSubquery()
                                ->addSelect($root . '.id')
                                ->addFrom('Chart ' . $root)
                                ->leftJoin($root . '.' . $foreign[$key]['model'] . ' ' . $fname)
                                ->addGroupBy($root . '.id')
                                ->addHaving('COUNT(' . $fname . '.chart_id) = ' . count($value));

                $q->andWhere('g.id IN (' . $sq->getDql() . ')');

                // if one or more values are set
                if ($value) {

                    // getting all foreign elements having $value
                    $suff = '_lim_sq';
                    $fname = $key . $suff;
                    $root = 'g' . $fname;
                    $sq2 = $q->createSubquery()
                                    ->addSelect($root . '.id')
                                    ->addFrom('Chart ' . $root)
                                    ->leftJoin($root . '.' . $foreign[$key]['model'] . ' ' . $fname)
                                    ->andWhere($fname . '.' . $foreign[$key]['column'] . ' IN (' . implode(',', $value) . ')')
                                    ->addGroupBy($root . '.id')
                                    ->addHaving('COUNT(' . $fname . '.chart_id) = ' . count($value));

                    $q->andWhere('g.id IN (' . $sq2->getDql() . ')');
                }
            }
        }

        $this->chart_query = $q;

        return $this->chart_query;
    }

    protected function buildChargeQuery($vehicles = array(), $categories = array()) {

        $full_history = $this->getParameter('full_history', 'nothing');
        if ('nothing' === $full_history) {
            throw new sfException('Parameter "full_history" is required');
        }

        $q = Doctrine_Query::create()->from('Charge c')->select('c.*');


        $q->andWhere('c.user_id = ?', $this->getParameter('user_id'));


        if (!$full_history && $p = $this->getParameter('date_from')) {
            $q->andWhere('c.date >= ?', $p);
        }

        if (!$full_history && $p = $this->getParameter('date_to')) {
            $q->andWhere('c.date <= ?', $p);
        }

        if (!$full_history && $p = $this->getParameter('kilometers_from')) {
            $q->andWhere('c.kilometers >= ?', $p);
        }

        if (!$full_history && $p = $this->getParameter('kilometers_to')) {
            $q->andWhere('c.kilometers <= ?', $p);
        }

        if ($vehicles) {
            $q->andWhereIn('c.vehicle_id', $vehicles);
        }

        if ($categories) {
            $q->andWhereIn('c.category_id', $categories);
        }

        return $q;
    }

    protected function getDataDefaults() {

        $fields = Doctrine_Core::getTable('Chart')->getFieldNames();

        $defaults = array_combine($fields, array_fill(0, count($fields), null));

        unset(
                $defaults['created_at'],
                $defaults['updated_at'],
                $defaults['slug'],
                $defaults['id']
        );

        $foreign = array(
            'vehicles_list' => null,
            'categories_list' => null,
        );

        return array_merge($defaults, $foreign);
    }

    protected function clearGeneratedElements() {

        $this->chart = null;
        $this->chart_source = null;
    }

    public static function getCategoriesList($categories = array()) {


        // If no categories are specified by the user, we get all categories
        if (!$categories) {
            $category_objects = Doctrine_Core::getTable('Category')->findAll(Doctrine_Core::HYDRATE_ARRAY);
        } else {
            $category_objects = Doctrine_Core::getTable('Category')->createQuery('c')->whereIn('c.id', $categories)
                            ->execute(array(), Doctrine_Core::HYDRATE_ARRAY);
        }


        $categories = array();
        $names = array();

        foreach ($category_objects as $key => $values) {
            $categories[] = $values['id'];
            $names[] = $values['name'];
        }
        $nb_categories = count($categories);

        return array(
            'list' => $categories,
            'count' => $nb_categories,
            'names' => $names,
        );
    }

    public static function getVehiclesList($vehicles = array(), $user_id) {

        $nb_vehicles = count($vehicles);

        // If no vehicles are specified by the user, we get all vehicles
        if (!$vehicles) {
            $q = Doctrine_Core::getTable('Vehicle')->getVehiclesByUserIdQuery($user_id);

            $vehicle_objects = $q->execute(array(), Doctrine_Core::HYDRATE_ARRAY);
            foreach ($vehicle_objects as $key => $values) {
                $vehicles[] = $values['id'];
            }
            $nb_vehicles = count($vehicles);
        }

        return array(
            'list' => $vehicles,
            'count' => $nb_vehicles,
        );
    }

    protected function buildCostPerYearChartData() {

        $this->setParameter('full_history', false);

        $gs = $this->getChartSource();
        if (!$gs) {
            return $gs;
        }

        $data = $gs->buildCostPerYearChartData($this->getParameter('range_type'));

        return $data;
    }

    protected function buildCostPerKmChartData() {

        $this->setParameter('full_history', true);

        $gs = $this->getChartSource(); 
        if (!$gs) {
            return $gs;
        }

        $data = $gs->buildCostPerKmChartData($this->getParameter('range_type'));

        return $data;
    }

    protected function buildCostPieChartData() {

        $this->setParameter('full_history', false);

        // get data series
        $gs = $this->getChartSource();
        if (!$gs) {
            return $gs;
        }

        // building chart data
        $categories = self::getCategoriesList($this->getParameter('categories_list', null));
        $options = array(
            'categories' => $categories,
            'vehicle_display' => $this->getParameter('vehicle_display'),
        );
        $data = $gs->buildCostPieChartData($options);

        return $data;
    }

    protected function buildTripChartData($options) {

        $this->setParameter('full_history', false);

        // get data series
        $gs = $this->getChartSource();
        if (!$gs) {
            return $gs;
        }

        $data = $gs->buildTripChartData($options);

        return $data;
    }

    protected function buildConsumptionPerDistanceChartData() {

        $this->setParameter('full_history', true);

        $gs = $this->getChartSource();
        if (!$gs) {
            return $gs;
        }

        $data = $gs->buildConsumptionPerDistanceChartData($this->getParameter('range_type'));

        return $data;
    }

}

