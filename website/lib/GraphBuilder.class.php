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

    public function __construct(array $parameters, array $options = array(), array $attributes = array()) {

        $this->setParameters($parameters);


        $this->setOptions($this->getDefaultOptions());
        $this->setOptions($options);

        $this->setAttributes($attributes);
    }

    public function __toString() {

        $this->display();
    }

    public function display() {

        $this->retrieveOrCreate();
        return image_tag($this->getGraphPath(), $this->getAttributes());
    }

    public function setAttributes(array $attributes) {

        $this->attributes = array_merge($this->attributes, $attributes);
    }

    public function getGraphPath() {

        return $this->getGraphImageBasePath() . '/' . $this->getGraphName();
    }

    public function getGraphImageBasePath() {
        return $this->getOption('image_base_path', sfConfig::get('app_graph_image_base_path', '/web/images/graphs'));
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
        unset($this->graph);
    }

    public function addParameters(array $parameters) {

        $this->parameters = array_merge($this->parameters, $parameters);
        unset($this->graph);
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
            'base_path' => '/graphs',
        );
    }

    public function getQuery() {

        if (!$this->graph_query) {
            $this->buildGraphsQuery();
        }

        return $this->graph_query;
    }

    public function getParam($name,$default = null) {

        return isset($this->parameters[$name]) ? $this->parameters[$name] : $default;
    }


    public function buildGraphSource() {

        $gs = new GraphSource();

        $params = array(
            'title',
            'x_axis_label',
            'y_axis_label',
         );

         foreach ($params as $param) {
             $gs->setParam($param, $this->getParam($param));
         }


         $vehicle_display = $this->getParam('vehicle_display','single');
         $category_display = $this->getParam('category_display','stacked');

         $data = $this->getGraphSourceData($vehicle_display,$category_display);

         $gs->setParam('series',$data);

         return $this->graph_source = $gs;


        }


        public function getGraphSourceData($vehicle_display,$category_display) {
            
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
//                unset($params[$field]);
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


        $q->andWhere('c.user_id = ?', $this->getParam('user_id'));


        if ($p = $this->getParam('date_from')) {
            $q->andWhere('c.date >= ?', $p);
        }

        if ($p = $this->getParam('date_to')) {
            $q->andWhere('c.date <= ?', $p);
        }

        if ($p = $this->getParam('kilometers_from')) {
            $q->andWhere('c.kilometers >= ?', $p);
        }

        if ($p = $this->getParam('kilometers_to')) {
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

}

