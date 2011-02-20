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

    protected $data;
    protected $query;
    protected $options = array();
    protected $attributes = array();
    protected $graph;

    public function __construct(array $data, array $options = array(), array $attributes = array()) {

        $this->data = array_merge($this->getDataDefaults(), $data);


        $this->setOptions($this->getDefaultOptions());
        $this->setOptions($options);

        $this->setAttributes($attributes);
    }

    public function __toString() {

        $this->display();
    }

    public function display() {

        $this->retrieveOrCreate();
        echo image_tag($this->getGraphPath(), $this->getAttributes());
    }

    public function setAttributes(array $attributes) {

        $this->attributes = array_merge($this->attributes, $attributes);
    }

    public function getGraphPath() {

        return $this->getOption('base_path') . '/' . $this->getGraphName();
    }

    public function getGraphName() {
        return $this->graph->getSha() . '.' . $this->graph->getFormat();
    }

    public function getOption($name, $default = null) {

        return isset($this->options[$name]) ? $this->options[$name] : $default;
    }

    public function setOptions($options) {
        $this->options = array_merge($this->options, $options);
    }

    public function retrieveOrCreate() {

        $this->buildGraphsQuery();
        $graph = $this->getGraphsQueryResults();

        // Ensuring that at most one element is retrieved
        if ($count = count($graph) > 1) {

            throw new sfException('More than one graph can be retrieved from requested criteria. Something wrong here!');
        }
        // In none, we generate a nwe graph
        elseif ($count == 0) {

            $graph = $this->saveNewGraph();
        }

        // Ok, we have already one
        else {

            $this->graph = $graph;
        }

        return $graph;
    }

    public function generate() {

    }

    public function getGraphsQueryResults() {

        if (!$this->query) {
            $this->buildGraphsQuery();
        }

        // trying to recover a graph in DB, if it exists
        try {
            $graph = $this->query->execute();
        } catch (Exception $exc) {
            $e = new sfException();
            throw $e->createFromException($exc);
        }

        return $graph;
    }

    public function getDefaultOptions() {

        return array(
            'base_path' => '/graphs',
        );
    }

    public function getQuery() {

        if (!$this->query) {
            $this->buildGraphsQuery();
        }

        return $this->query;
    }

    protected function saveNewGraph() {

        $graph = new Graph();
        $graph->fromArray($this->data);
        $graph->save();

        $this->graph = $graph;

        $this->generate();

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

        foreach ($this->data as $key => $value) {

            // if this is a local column
            if (in_array($key, $locals)) {

                if (count($value) == 1) {
                    $q->andWhere('g.' . $key . ' = ?',$value);

                } elseif (count($value) > 1) {
                    $q->andWhereIn('g.' . $key,$value);

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
                                ->addHaving('COUNT(' . $fname . '.graph_id) = '.count($value));

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
                                    ->andWhere($fname . '.' . $foreign[$key]['column'].' IN ('.implode(',',$value) .')')
                                    ->addGroupBy($root . '.id')
                                    ->addHaving('COUNT(' . $fname . '.graph_id) = '.count($value));

                    $q->andWhere('g.id IN (' . $sq2->getDql() . ')');

                }
            }
        }

        $this->query = $q;

        return $this->query;
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

