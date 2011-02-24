<?php

class GraphBuilderPGraph extends GraphBuilder {

    public function  doDisplay() {

        return image_tag($this->getGraphPath(), $this->getAttributes());
    }
}

