<?php

class GraphBuilderPChart extends GraphBuilder {

    public function doDisplay() {


        $path = str_replace('images/', '', $this->getGraphPath('web'));
        return image_tag($path, $this->getAttributes());
    }

    public function doGenerate() {

        $done = true;

        $name = $this->getParameter('graph_name');

        switch ($name) {
            case 'cost_per_km':
                $data = $this->buildCostPerKmGraphData();

                break;

            default:

                throw new sfException(sprintf('Unknown graph name %s', $name));
                break;
        }


        $picture = $this->buildPicture($data);

        $picture->render($this->getGraphPath('system'));

        sfContext::getInstance()->getLogger()->info(sprintf('Rendering graph %s with pGraph.', $this->getGraphPath()));

        return $done;
    }

    protected function buildPicture($data) {

        $myPicture = new pImage(700, 230, $data);
        $myPicture->drawRectangle(0, 0, 699, 229, array("R" => 0, "G" => 0, "B" => 0));

        $myPicture->setShadow(TRUE, array("X" => 1, "Y" => 1, "R" => 50, "G" => 50, "B" => 50, "Alpha" => 20));

        $myPicture->setFontProperties(array("FontName" => sfConfig::get('sf_web_dir') . "/fonts/Bedizen.ttf", "FontSize" => 14));
        $TextSettings = array("Align" => TEXT_ALIGN_MIDDLEMIDDLE
            , "R" => 48, "G" => 48, "B" => 48);
        $myPicture->drawText(350, 25, "cvbxcfhfghx vhn", $TextSettings);

        $myPicture->setShadow(FALSE);
        $myPicture->setGraphArea(50, 50, 675, 190);
        $myPicture->setFontProperties(array("R" => 0, "G" => 0, "B" => 0, "FontName" => sfConfig::get('sf_web_dir') . "/fonts/pf_arma_five.ttf", "FontSize" => 6));

        $Settings = array("Pos" => SCALE_POS_LEFTRIGHT
            , "Mode" => SCALE_MODE_FLOATING
            , "LabelingMethod" => LABELING_ALL
            , "GridR" => 177, "GridG" => 200, "GridB" => 204, "GridAlpha" => 50, "TickR" => 0, "TickG" => 0, "TickB" => 0, "TickAlpha" => 50, "LabelRotation" => 0, "CycleBackground" => 1, "DrawXLines" => 1, "DrawSubTicks" => 1, "SubTickR" => 255, "SubTickG" => 0, "SubTickB" => 0, "SubTickAlpha" => 50, "DrawYLines" => ALL);
        $myPicture->drawScale($Settings);

        $myPicture->setShadow(TRUE, array("X" => 1, "Y" => 1, "R" => 50, "G" => 50, "B" => 50, "Alpha" => 10));

        $Config = array("ForceTransparency" => 30, "AroundZero" => 1);
        $myPicture->drawAreaChart($Config);

        $Config = array("FontR" => 0, "FontG" => 0, "FontB" => 0, "FontName" => sfConfig::get('sf_web_dir') . "/fonts/GeosansLight.ttf", "FontSize" => 6, "Margin" => 6, "Alpha" => 30, "BoxSize" => 5, "Style" => LEGEND_BOX
            , "Mode" => LEGEND_VERTICAL
            , "Family" => LEGEND_FAMILY_CIRCLE
        );
        $myPicture->drawLegend(651, 16, $Config);

        return $myPicture;
    }

    protected function buildCostPerKmGraphData() {

        $gs = $this->getGraphSource();


        $myData = new pData();

        // X-axis
        $axis_params = $this->getAxisParametersByRangeType($this->getParameter('range_type'));

        $x_column = $gs->getSeriesDataByColumn($axis_params['column'], $axis_params['format']);

        $x_data = $gs->buildXAxisData($x_column);

        $myData->addPoints($x_data, "x-axis");
        $myData->setSerieDescription("x-axis", $axis_params['label']);
        $myData->setAbscissa("x-axis");


        // Y-axis
        $y_columns = $gs->getSeriesDataByColumn('amount');

        $y_data = array();

        foreach ($x_data as $bkey => $bound) {


            foreach ($y_columns as $ykey => $y_values) {

                // removing x elements that are larger than bound
                $filter = $gs->filterValuesLargerThan($x_column[$ykey], $bound);
                
                
                // getting corresponding y elements
                $y_filtered = array_intersect_key($y_values, $filter);


                if (!count($y_filtered)) {
                    $cost = VOID;
                } else {
                    $cost = array_sum($y_filtered) / $bound;
                }

                // calculating relative cost
                $y_data[$ykey][$bkey] = $cost;

            }
        }


        $y_series = $gs->getSeries();


        foreach ($y_series as $key => $serie) {
            $myData->addPoints($y_data[$key], $serie->getId());
            $myData->setSerieDescription($serie->getId(), $serie->getLabel());
            $myData->setSerieOnAxis($serie->getId(), 0);
        }

        $myData->setAxisPosition(0, AXIS_POSITION_LEFT);
        $myData->setAxisName(0, "Cost");
        $myData->setAxisUnit(0, "CHF");

        return $myData;
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

            case 'kilometers':

                $params = array(
                    'label' => 'Distance',
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

}

