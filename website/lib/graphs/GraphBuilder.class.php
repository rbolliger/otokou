<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of GraphBuilder
 *
 * @author Raffaele Bolliger (raffaele.bolliger@gmail.com)
 */
class GraphBuilder {

    protected $parameters;
    protected $graph_query;
    protected $options = array();
    protected $attributes = array();
    protected $graph;
    protected $graph_source;
    protected $logger;

    public function doDisplay() {

    }

    public function doGenerate() {

        // false because no graph has been generated. true must be returned only if a graph has been generated
        return false;
    }

    public function __construct(array $parameters, array $options = array(), array $attributes = array()) {

        $this->setParameters($parameters);


        $this->setOptions($this->getDefaultOptions());
        $this->setOptions($options);

        $this->setAttributes($attributes);

        $this->setLogger($this->getOption('logger', sfContext::getInstance()->getLogger()));
    }

    public function __toString() {

        $this->display();
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

    public function getGraphPath($type = 'web') {

        return $this->getGraph()->getGraphPath($type);
    }

    public function getGraphBasePath($type = 'web') {

        return $this->getGraph()->getGraphBasePath($type);
    }

    public function getGraphName() {
        return $this->getGraph()->getGraphName();
    }

    public function getGraphFormat() {

        return $this->getGraph()->getGraphFormat();
    }

    public function getGraph() {
        if (!isset($this->graph) || !$this->graph) {
            $this->retrieveOrCreate();
        }

        return $this->graph;
    }

    public function getOption($name, $default = null) {

        return isset($this->options[$name]) ? $this->options[$name] : $default;
    }

    public function setOptions($options) {
        $this->options = array_merge($this->options, $options);
    }

    public function setParameters(array $parameters) {

        $this->parameters = array_merge($this->getDataDefaults(), $parameters);
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

        if (!$this->graph_query) {
            $this->buildGraphsQuery();
        }
        $coll = $this->getGraphsQueryResults();

        // Ensuring that at most one element is retrieved
        if (count($coll) == 0) {

            $graph = $this->saveNewGraph();
        }

        // Ok, we have already one
        else {

            $this->graph = $coll[0];
        }

        return $this->graph;
    }

    public function generate() {

        $done = true;

        // Does the Graph object has been retrived from the DB?
        if (!$this->graph) {
            $this->retrieveOrCreate();
        }

        // If the source is already available, we stop here
        if ($this->graphSourceIsAvailable() && !$this->doForceGenerate()) {
            $this->getLogger()->info(sprintf('Graph %s exists. Skipping generation.', $this->getGraphPath('system')));

            return $done;
        }

        if (!$this->graph_source) {
            $done = $this->getGraphSource();
        }

        if (!$done) {

            return $done;
        }

        if (!$this->doForceGenerate()) {
            $this->getLogger()->info(sprintf('Graph %s picture does not exist exist. Generating it.', $this->getGraphPath('system')));
        } else {
            $this->getLogger()->info(sprintf('Graph %s generation was forced or file does not exist. File is generated.', $this->getGraphPath('system')));
        }


        return $this->doGenerate();
    }

    public function graphSourceIsAvailable() {

        // Does the Graph object has been retrived from the DB?
        $this->getGraph();

        // Checking that the base path exists
        $this->checkPath($this->getGraphBasePath('system'));

        return $this->checkPath($this->getGraphPath('system'), false);
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

    public function getGraphsQueryResults() {

        if (!$this->graph_query) {
            $this->buildGraphsQuery();
        }

        // trying to recover a graph in DB, if it exists
        $graph = $this->graph_query->execute();


        if ($count = count($graph) > 1) {

            throw new sfException('More than one graph can be retrieved with the requested criteria. Something wrong here!');
        }

        return $graph;
    }

    public function getDefaultOptions() {

        return array(
        );
    }

    public function getQuery() {

        if (!$this->graph_query) {
            $this->buildGraphsQuery();
        }

        return $this->graph_query;
    }

    public function buildGraphSource() {

        $gs = new GraphSource();

        // setting chart parameters
        $gs->addParams($this->getOption('chart_parameters'));

        // getting source data
        $vehicle_display = $this->getParameter('vehicle_display', 'single');
        $category_display = $this->getParameter('category_display', 'stacked');

        $series = $this->getGraphSourceData($vehicle_display, $category_display);

        if (!$series) {
            return false;
        }

        $gs->setSeries($series);

        $this->getLogger()->info(sprintf('Graph %s source does not exist exist. Building graph data source.', $this->getGraphName()));

        $this->graph_source = $gs;

        return true;
    }

    public function getGraphSourceData($vehicle_display, $category_display) {



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


        $vl = $this->getVehiclesList();
        $cl = $this->getCategoriesList();

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
            );

            $series[$ns] = new GraphDataSerie($params);
        }


        if (!count($series)) {
            return false;
        }

        return $series;
    }

    public function getGraphSource() {

        if (!$this->graph_source) {
            $done = $this->buildGraphSource();

            if (!$done) {
                return $done;
            }
        }

        return $this->graph_source;
    }

    public function reloadGraph() {

        $g = $this->getGraph();

        $g->refresh();
    }

    public function doForceGenerate() {

        return sfConfig::get('app_graphs_force_generate', false);
    }

    protected function buildLabelForSerie($params) {

        $label = '';

        if ($params['vehicle_display'] == 'single') {

            $v = Doctrine_Core::getTable('Vehicle')->findOneById($params['vehicle_id']);

            $label .= $v->getName();
        }

        if ($params['category_display'] == 'single') {
            $c = Doctrine_Core::getTable('Category')->findOneById($params['category_id']);

            $label .= ' - ' . $c->getName();
        }

        if ($params['vehicle_display'] == 'stacked' && $params['category_display'] == 'stacked') {

            $label .= 'All vehicles and categories';
        }

        return $label;
    }

    protected function saveNewGraph() {

        $graph = new Graph();

        $params = $this->parameters;

        $foreign = array(
            'categories_list' => 'Categories',
            'vehicles_list' => 'Vehicles',
        );


        $graph->fromArray($this->parameters);

        foreach ($foreign as $field => $class) {

            if (isset($params[$field])) {

                $graph->link($class, $params[$field]);
            }
        }

        $graph->save();

        $this->graph = $graph;

        return $graph;
    }

    protected function buildGraphsQuery() {

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
        );

        $foreign = array(
            'vehicles_list' => array('model' => 'GraphVehicle', 'column' => 'vehicle_id'),
            'categories_list' => array('model' => 'GraphCategory', 'column' => 'category_id'),
        );


        $q = Doctrine_Query::create()->from('Graph g')->select('g.*');
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
                                ->addFrom('Graph ' . $root)
                                ->leftJoin($root . '.' . $foreign[$key]['model'] . ' ' . $fname)
                                ->addGroupBy($root . '.id')
                                ->addHaving('COUNT(' . $fname . '.graph_id) = ' . count($value));

                $q->andWhere('g.id IN (' . $sq->getDql() . ')');

                // if one or more values are set
                if ($value) {

                    // getting all foreign elements having $value
                    $suff = '_lim_sq';
                    $fname = $key . $suff;
                    $root = 'g' . $fname;
                    $sq2 = $q->createSubquery()
                                    ->addSelect($root . '.id')
                                    ->addFrom('Graph ' . $root)
                                    ->leftJoin($root . '.' . $foreign[$key]['model'] . ' ' . $fname)
                                    ->andWhere($fname . '.' . $foreign[$key]['column'] . ' IN (' . implode(',', $value) . ')')
                                    ->addGroupBy($root . '.id')
                                    ->addHaving('COUNT(' . $fname . '.graph_id) = ' . count($value));

                    $q->andWhere('g.id IN (' . $sq2->getDql() . ')');
                }
            }
        }

        $this->graph_query = $q;

        return $this->graph_query;
    }

    protected function buildChargeQuery($vehicles = array(), $categories = array()) {

        $q = Doctrine_Query::create()->from('Charge c')->select('c.*');


        $q->andWhere('c.user_id = ?', $this->getParameter('user_id'));


        if ($p = $this->getParameter('date_from')) {
            $q->andWhere('c.date >= ?', $p);
        }

        if ($p = $this->getParameter('date_to')) {
            $q->andWhere('c.date <= ?', $p);
        }

        if ($p = $this->getParameter('kilometers_from')) {
            $q->andWhere('c.kilometers >= ?', $p);
        }

        if ($p = $this->getParameter('kilometers_to')) {
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

        $fields = Doctrine_Core::getTable('Graph')->getFieldNames();

        $defaults = array_combine($fields, array_fill(0, count($fields), null));

        unset(
                $defaults['created_at'],
                $defaults['updated_at'],
                $defaults['sha'],
                $defaults['id']
        );

        $foreign = array(
            'vehicles_list' => null,
            'categories_list' => null,
        );

        return array_merge($defaults, $foreign);
    }

    protected function clearGeneratedElements() {

        $this->graph = null;
        $this->graph_source = null;
    }

    protected function getVehiclesList() {

        $vehicles = $this->getParameter('vehicles_list', null);

        $nb_vehicles = count($vehicles);

        // If no vehicles are specified by the suer, we get all vehicles
        if (!$nb_vehicles) {
            $q = Doctrine_Core::getTable('Vehicle')->getVehiclesByUserIdQuery($this->getParameter('user_id'));

            $vehicle_objects = $q->execute(array(), Doctrine_Core::HYDRATE_ARRAY);
            foreach ($vehicle_objects as $key => $values) {
                $vehicles[] = $values['id'];
            }
            $nb_vehicles = count($vehicles);
        }

        return $params = array(
    'list' => $vehicles,
    'count' => $nb_vehicles,
        );
    }

    protected function getCategoriesList() {

        $categories = $this->getParameter('categories_list', null);
        $nb_categories = count($categories);


        // If no categories are specified by the user, we get all categories
        if ($nb_categories == 0) {
            $category_objects = Doctrine_Core::getTable('Category')->findAll(Doctrine_Core::HYDRATE_ARRAY);

            foreach ($category_objects as $key => $values) {
                $categories[] = $values['id'];
            }
            $nb_categories = count($categories);
        }

        return $params = array(
    'list' => $categories,
    'count' => $nb_categories,
        );
    }

}

