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
                $picture = $this->buildPicture($data);
                $chart = $this->plotScatterChart($picture, $data);
                break;

            case 'cost_per_year':
                $data = $this->buildCostPerYearGraphData();
                $picture = $this->buildPicture($data);
                $picture = $this->plotBarChart($picture);
                break;

            default:

                throw new sfException(sprintf('Unknown graph name %s', $name));
                break;
        }

        $picture->render($this->getGraphPath('system'));

        sfContext::getInstance()->getLogger()->info(sprintf('Rendering graph %s with pGraph.', $this->getGraphPath()));

        return $done;
    }

    protected function buildPicture(pData $data) {


        // Color scheme from kuler "Q10 Chart"


        $myPicture = new pImage(900, 475, $data);
        //$myPicture->drawRectangle(0, 0, 899, 474, array("R" => 0, "G" => 0, "B" => 0));

        $myPicture->setShadow(TRUE, array("X" => 1, "Y" => 1, "R" => 50, "G" => 50, "B" => 50, "Alpha" => 20));

        $myPicture->setFontProperties(array("FontName" => sfConfig::get('sf_web_dir') . "/fonts/Ubuntu-R.ttf", "FontSize" => 14));
        $TextSettings = array("Align" => TEXT_ALIGN_MIDDLEMIDDLE
            , "R" => 40, "G" => 40, "B" => 43);
        $myPicture->drawText(350, 25, $this->getOption('title'), $TextSettings);

        $myPicture->setShadow(FALSE);
        $myPicture->setGraphArea(90, 50, 649, 360);
        $myPicture->setFontProperties(array("R" => 40, "G" => 40, "B" => 43, "FontName" => sfConfig::get('sf_web_dir') . "/fonts/DejaVuSans-ExtraLight.ttf", "FontSize" => 9));

        return $myPicture;
    }

    protected function plotScatterChart(pImage $picture, pData $data) {



        $myScatter = new pScatter($picture, $data);

        $Settings = array(
            "Pos" => SCALE_POS_LEFTRIGHT,
            "Mode" => SCALE_MODE_FLOATING,
            "DrawXLines" => FALSE,
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
        $myScatter->drawScatterScale($Settings);

        $picture->setShadow(TRUE, array("X" => 1, "Y" => 1, "R" => 0, "G" => 0, "B" => 0, "Alpha" => 10));

        $myScatter->drawScatterLineChart();
        //$myScatter->drawScatterPlotChart();

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
            "Mode" => LEGEND_VERTICAL,
            "Family" => LEGEND_FAMILY_LINE,
        );
        $myScatter->drawScatterLegend(655, 50, $Config);
    }

    protected function plotBarChart(pImage $picture) {

        $picture->setShadow(FALSE);

        $Settings = array(
            "Pos" => SCALE_POS_LEFTRIGHT,
            "Mode" => SCALE_MODE_FLOATING,
            "DrawXLines" => FALSE,
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
        $picture->drawScale($Settings);

        $options = array(
            'AroundZero' => true,
            'Draw0Line'  => true,
        );
        $picture->drawBarChart($options);

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
            "Mode" => LEGEND_VERTICAL,
            "Family" => LEGEND_FAMILY_LINE,
        );
        $picture->drawLegend(655, 50, $Config);

        return $picture;
    }

    protected function buildCostPerKmGraphData() {

        $gs = $this->getGraphSource();

        $myData = new pData();

        // X-axis
        $options = array(
            'check_zeroes' => true,
            'zero_approx' => 0.01,
        );
        $axis_data = $gs->buildXAxisDataByRangeTypeAndCalculationBase($this->getParameter('range_type'), 'distance', $options);

        $x_id = "x-axis";
        $myData->addPoints($axis_data['value'], "x-axis");

        $myData->setAxisName(0, $axis_data['label']);
        $myData->setAxisXY(0, AXIS_X);
        $myData->setAxisPosition(0, AXIS_POSITION_BOTTOM);

        $is_date = $this->getParameter('range_type') == 'date' ? true : false;
        $display_mode = $is_date ? AXIS_FORMAT_DATE : AXIS_FORMAT_DEFAULT;
        $display_format = $is_date ? 'd-M-Y' : null;

        $myData->setAxisDisplay(0, $display_mode, $display_format);


        // Y-axis

        $y_columns = $gs->getSeriesDataByColumn('amount');

        $x_data = $axis_data['base'];
        $x_column = $axis_data['base_column'];
        $y_data = array();


        foreach ($x_data as $bkey => $bound) {


            foreach ($y_columns as $ykey => $y_values) {

                // removing x elements that are larger than bound
                $filter = $gs->filterValuesLargerThan($x_column[$ykey], $bound);

                // getting corresponding y elements
                $y_filtered = array_intersect_key($y_values, $filter);

                // calculating relative cost
                if (!count($y_filtered)) {
                    $cost = VOID;
                } else {

                    $cost = array_sum($y_filtered) / $bound;
                }

                // assigning result to temporary array
                $y_data[$ykey][$bkey] = $cost;
            }
        }

        $y_series = $gs->getSeries();

        foreach ($y_series as $key => $serie) {
            $myData->addPoints($y_data[$key], $serie->getId());
            $myData->setSerieOnAxis($serie->getId(), 1);

            $myData->setScatterSerie($x_id, $serie->getId(), $key);
            $myData->setScatterSerieDescription($key, $serie->getLabel());
            $myData->setScatterSerieWeight($key, 0.7);
        }

        $myData->setAxisName(1, 'Cost [CHF/km]');
        $myData->setAxisXY(1, AXIS_Y);
        $myData->setAxisPosition(1, AXIS_POSITION_LEFT);

        return $myData;
    }

    protected function buildCostPerYearGraphData() {

        $gs = $this->getGraphSource();

        $myData = new pData();

        // x-axis
        $dates = $gs->getSeriesDataByColumn('date', 'datetime');

        $date_max = array();
        $date_min = array();
        foreach ($dates as $d) {
            $date_max[] = max($d);
            $date_min[] = min($d);
        }

        $date_max = max($date_max);
        $date_min = min($date_min);


        $year_min = date('Y', $date_min);
        $year_max = date('Y', $date_max);
        $years = range($year_min, $year_max);

        // building limits for each x-axis serie
        $dates_range = array();
        foreach (range($year_min, $year_max + 1) as $year) {
            $dates_range[] = strtotime($year . 'jan-1');
        }


        $x_id = "x-axis";
        $myData->addPoints($years, $x_id);
        $myData->setSerieDescription($x_id, 'Years');
        $myData->setAbscissa($x_id);


        // Y-axis
        $costs = $gs->getSeriesDataByColumn('amount', 'number');
        $y_series = $gs->getSeries();

        $myData->setAxisName(0, "Annual costs [CHF/year]");

        $y_data = array();
        foreach ($dates as $skey => $serie) {

            for ($index = 0; $index < count($dates_range) - 1; $index++) {
                // removing x elements that are larger than bound

                $filter = $gs->filterValuesOutsideRange($serie, $dates_range[$index], $dates_range[$index + 1]);



                if (!$filter) {
                    $y_cost = 0;
                } else {

                    // getting corresponding y elements
                    $y_filtered = array_intersect_key($costs[$skey], $filter);

                    $y_cost = array_sum($y_filtered);
                }

                $y_data[$skey][$index] = $y_cost;
            }

            $y_id = $y_series[$skey]->getId();
            $myData->addPoints($y_data[$skey], $y_id);
            $myData->setSerieDescription($y_id, $y_series[$skey]->getLabel());
        }




        return $myData;
    }

}

