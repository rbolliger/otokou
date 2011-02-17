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


        $q = Doctrine_Core::getTable('Graph')->createQuery('g')->select('g.*');

        foreach ($this->data as $key => $value) {

            // if this is a local column
            if (in_array($key, $locals)) {

                if ($value) {
                    $q->andWhereIn('g.' . $key, $value);
                } else {
                    $q->andWhere('g.' . $key . ' IS NULL');
                }
            }
            // if this is a foreign reference
            else {

                // ensuring that there are no more and no less foreign elements than those requested
                $q->leftJoin($q->getRootAlias() . '.' . $foreign[$key]['model'] . ' ' . $key . '_bis');
                $q->addSelect('COUNT(' . $key . '_bis.' . $foreign[$key]['column'] . ') as num_foreign');
                $q->having('num_foreign = ?', count($value));

                // if one or more values are set
                if ($value) {

                    // getting all foreign elements having $value
                    $q->innerJoin($q->getRootAlias() . '.' . $foreign[$key]['model'] . ' ' . $key);
                    $q->whereIn($key . '.' . $foreign[$key]['column'], $value);
                }
            }
        }


        $this->query = $q;
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

        return $defaults;
    }

}

