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

    protected function buildPicture(pData $data) {


        // Color scheme from kuler "Q10 Chart"
        

        $myPicture = new pImage(700, 400, $data);
        //$myPicture->drawRectangle(0, 0, 699, 399, array("R" => 0, "G" => 0, "B" => 0));

        $myPicture->setShadow(TRUE, array("X" => 1, "Y" => 1, "R" => 50, "G" => 50, "B" => 50, "Alpha" => 20));

        $myPicture->setFontProperties(array("FontName" => sfConfig::get('sf_web_dir') . "/fonts/Ubuntu-R.ttf", "FontSize" => 14));
        $TextSettings = array("Align" => TEXT_ALIGN_MIDDLEMIDDLE
            , "R" => 40, "G" => 40, "B" => 43);
        $myPicture->drawText(350, 25, $this->getOption('title'), $TextSettings);

        $myPicture->setShadow(FALSE);
        $myPicture->setGraphArea(90, 50, 675, 330);
        $myPicture->setFontProperties(array("R" => 40, "G" => 40, "B" => 43, "FontName" => sfConfig::get('sf_web_dir') . "/fonts/DejaVuSans-ExtraLight.ttf", "FontSize" => 9));

        $Settings = array(
            "Pos" => SCALE_POS_LEFTRIGHT,
            "Mode" => SCALE_MODE_FLOATING,
            "LabelingMethod" => LABELING_ALL,
            "DrawXLines"  => false,
            "DrawYLines" => ALL,
            "GridTicks" => 1,
            "GridR" => 168,
            "GridG" => 186,
            "GridB" => 203,
            "GridAlpha" => 30,
            "AxisR" => 40,
            "AxisG" => 40,
            "AxisB" => 43,
            "AxisAlpha" => 100,
            "TickR" => 40,
            "TickG" => 40,
            "TickB" => 43,
            "TickAlpha" => 50,
            "DrawSubTicks" => 1,
            "SubTickR" => 168,
            "SubTickG" => 186,
            "SubTickB" => 203,
            "SubTickAlpha" => 100,
            "DrawArrows" => false,
            "CycleBackground" => false,
                    );
        $myPicture->drawScale($Settings);

        $myPicture->setShadow(TRUE, array("X" => 1, "Y" => 1, "R" => 50, "G" => 50, "B" => 50, "Alpha" => 10));

        $Config = array(
            "ForceTransparency" => 10,
            "AroundZero" => 1,
            "DisplayValues" => false,
            );
        $myPicture->drawAreaChart($Config);

        $Config = array(
            'DisplayValues' => false,
            'DisplayColor'  => DISPLAY_AUTO,
        );
        $myPicture->drawLineChart($Config);

        $Config = array(
            "FontName" => sfConfig::get('sf_web_dir') . "/fonts/Ubuntu-R.ttf",
            "FontSize" => 6,
            "FontR" => 40,
            "FontG" => 40,
            "FontB" => 43,
            "Margin" => 6,
            "Alpha" => 100,
            "BoxSize" => 5,
            "Style" => LEGEND_NOBORDER,
            "Mode" => LEGEND_HORIZONTAL,
            "Family" => LEGEND_FAMILY_LINE,
        );
        $myPicture->drawLegend(20, 375, $Config);

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
            $myData->setSerieWeight($serie->getId(), 1);
        }

        $myData->setAxisPosition(0, AXIS_POSITION_LEFT);
        $myData->setAxisName(0, "Cost [CHF]");
        //$myData->setAxisUnit(0, "CHF");

        return $myData;
    }

    

}

