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

        $this->generate();

        return $this->doDisplay();
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

        return $this->getGraphBasePath($type) . '/' . $this->getGraphName();
    }

    public function getGraphBasePath($type = 'web') {

        $path = $this->getOption('base_path', sfConfig::get('app_graph_base_path', '/graphs'));


        switch ($type) {
            case 'web':
                $path = ($path[0] == '/' ? substr($path, 1) : $path);

                break;

            case 'system':
                $path = ($path[0] == '/' ? $path : '/' . $path);

                // adding "images" folder
                $path = '/' . sfConfig::get('sf_web_images_dir_name', 'images') . $path;

                $path = $this->convertToSystemPath($path);

                $path = sfConfig::get('sf_web_dir') . $path;


                break;

            default:

                throw new sfException('Unknown option ' . $type);
        }

        return $path;
    }

    public function getGraphName() {
        return $this->getGraph()->getSha() . '.' . $this->getGraphFormat();
    }

    public function getGraphFormat() {

        $format = $this->getGraph()->getFormat();

        $format = (!$format == '' || !is_null($format)) ?
                $format :
                sfConfig::get('app_graph_default_format', 'png');

        return $format;
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

        // Does the Graph object has been retrived from the DB?
        if (!$this->graph) {
            $this->retrieveOrCreate();
        }

        // If the source is already available, we stop here
        if ($this->graphSourceIsAvailable()) {
            $this->getLogger()->info(sprintf('Graph %s exists. Skipping generation.', $this->getGraphPath('system')));

            return;
        }

        if (!$this->graph_source) {
            $this->getGraphSource();
        }

        $this->getLogger()->info(sprintf('Graph %s picture does not exist exist. Generating it.', $this->getGraphPath('system')));


        $this->doGenerate();
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

            throw new sfException('More than one graph can be retrieved from requested criteria. Something wrong here!');
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

        // setting decorations
        $params = array(
            'title',
            'x_axis_label',
            'y_axis_label',
        );

        foreach ($params as $param) {
            $gs->setParam($param, $this->getParameter($param));
        }

        // getting source data
        $vehicle_display = $this->getParameter('vehicle_display', 'single');
        $category_display = $this->getParameter('category_display', 'stacked');

        $data = $this->getGraphSourceData($vehicle_display, $category_display);

        $gs->setParam('raw_data', $data);

        $this->getLogger()->info(sprintf('Graph %s source does not exist exist. Building graph data source.', $this->getGraphName()));

        return $this->graph_source = $gs;
    }

    public function getGraphSourceData($vehicle_display, $category_display) {

        $data = array();

        if ($vehicle_display == 'stacked' && $category_display == 'stacked') {
            $case = 1;
        } elseif ($vehicle_display == 'stacked' && $category_display == 'single') {
            $case = 2;
        } elseif ($vehicle_display == 'single' && $category_display == 'stacked') {
            $case = 3;
        } elseif ($vehicle_display == 'single' && $category_display == 'single') {
            $case = 4;
        }

        $vehicles = $this->getParameter('vehicles_list', null);
        $categories = $this->getParameter('categories_list', null);
        $nb_categories = count($categories);
        $nb_vehicles = count($vehicles);

        switch ($case) {
            case 1:
                $ns = 0;

                $q = $this->buildChargeQuery($vehicles, $categories);
                $charges = $q->execute();

                $data[$ns++] = $charges;

                break;

            case 2:
                $ns = 0;
                for ($indexC = 0; $indexC < $nb_categories; $indexC++) {

                    $q = $this->buildChargeQuery($vehicles, $categories[$indexC]);
                    $charges = $q->execute();

                    $data[$ns++] = $charges;
                }

                break;

            case 3:
                $ns = 0;
                for ($indexV = 0; $indexV < $nb_vehicles; $indexV++) {

                    $q = $this->buildChargeQuery($vehicles[$indexV], $categories);
                    $charges = $q->execute();

                    $data[$ns++] = $charges;
                }

                break;


            case 4:
                $ns = 0;
                for ($indexC = 0; $indexC < $nb_categories; $indexC++) {
                    for ($indexV = 0; $indexV < $nb_vehicles; $indexV++) {

                        $q = $this->buildChargeQuery($vehicles[$indexV], $categories[$indexC]);
                        $charges = $q->execute();

                        $data[$ns++] = $charges;
                    }
                }

                break;

            default:
                break;
        }


        return $data;
    }

    public function getGraphSource() {

        if (!$this->graph_source) {
            $this->buildGraphSource();
        }

        return $this->graph_source;
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
            else {

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

    protected function convertToSystemPath($path) {

        return str_replace("/", DIRECTORY_SEPARATOR, $path);
    }

}

