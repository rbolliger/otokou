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

            case 'cost_pie':
                $data = $this->buildCostPieGraphData();

                $raw_data = $data->getData();
                // -1 to remove abscissa
                $n_series = count($raw_data['Series']) - 1;

                $options = array(
                    'chart_height' => 310,
                    'graph_area_height' => 310 * ceil(($n_series + 1) / 2),
                    'n_series' => $n_series,
                    'legend_height' => 0,
                    'x_label_height' => 0,
                );
                $picture = $this->buildPicture($data, $options);
                $pie = $this->plotPieChart($picture, $data, $options);
                break;

            case 'trip_annual':
                $data = $this->buildTripAnnualGraphData();
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

    protected function buildPicture(pData $data, $options = array()) {


        $title_height = isset($options['title_height']) ? $options['title_height'] : 50;
        $ga_height = isset($options['graph_area_height']) ? $options['graph_area_height'] : 310;
        $x_label_height = isset($options['x_label_height']) ? $options['x_label_height'] : 50;

        $height = $title_height + $ga_height + $x_label_height;
        $width = 900;
        // Color scheme from kuler "Q10 Chart"


        $myPicture = new pImage($width, $height, $data);
        $myPicture->drawRectangle(0, 0, $width - 1, $height - 1, array("R" => 0, "G" => 0, "B" => 0));

        $myPicture->setShadow(TRUE, array("X" => 1, "Y" => 1, "R" => 50, "G" => 50, "B" => 50, "Alpha" => 20));

        $myPicture->setFontProperties(array("FontName" => sfConfig::get('sf_web_dir') . "/fonts/Ubuntu-R.ttf", "FontSize" => 14));
        $TextSettings = array("Align" => TEXT_ALIGN_MIDDLEMIDDLE
            , "R" => 40, "G" => 40, "B" => 43);
        $myPicture->drawText($width / 2, $title_height / 2, $this->getOption('title'), $TextSettings);

        $myPicture->setShadow(FALSE);
        $myPicture->setGraphArea(90, 50, 649, $ga_height);
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

    protected function plotPieChart(pImage $picture, pData $data, $options = array()) {


        $pie = new pPie($picture, $data);

        $raw_data = $data->getData();
        $n_series = $options['n_series'];
        $series_names = array_keys($raw_data['Series']);
        $chart_height = $options['chart_height'];
        $title_height = isset($options['title_height']) ? $options['title_height'] : 50;


        $c = -1;
        foreach ($series_names as $key => $id) {

            if ('labels' == $id) {
                continue;
            }

            $c++;

            $data->setSerieDrawable($id, true);

            $posX = ($c == 0) ? 225 :
                    (($c + 1) % 2 == 1 ? 225 + 450 : 225);

            $posY = ($c == 0) ? $chart_height / 2 :
                    $chart_height / 2 + $chart_height * (floor($c / 2) + $c % 2);
            $posY = $title_height + $posY; // title

            $options = array(
                'Radius' => $chart_height / 2 * .9,
                'SkewFactore' => 0.5,
                'SliceHeight' => 10,
                'DataGapAngle' => 0,
                //'DataGapRadius'=>$chart_height/2/10,
                'Border' => TRUE,
                'BorderR' => 255,
                'BorderG' => 255,
                'BorderB' => 255,
                'SecondPass' => true,
                'WriteValues' => true,
                'ValueR' => 0,
                'ValueG' => 0,
                'ValueB' => 0,
            );
            $pie->draw3DPie($posX, $posY, $options);

            $data->setSerieDrawable($id, false);


            $TextSettings = array("Align" => TEXT_ALIGN_MIDDLEMIDDLE
                , "R" => 40, "G" => 40, "B" => 43);
            $picture->drawText($posX, $posY-$chart_height/2*.9, $raw_data['Series'][$id]['Description'], $TextSettings);
        }





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
        $pie->drawPieLegend(450, 50, $Config);
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
            'Draw0Line' => true,
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

        $x_dates = $gs->buildXAxisDataByDateRange($dates);


        $x_id = "x-axis";
        $myData->addPoints($x_dates['years'], $x_id);
        $myData->setSerieDescription($x_id, 'Years');
        $myData->setAbscissa($x_id);


        // Y-axis
        $costs = $gs->getSeriesDataByColumn('amount', 'number');
        $y_series = $gs->getSeries();

        $myData->setAxisName(0, "Annual costs [CHF/year]");

        $y_data = array();
        foreach ($dates as $skey => $serie) {

            for ($index = 0; $index < count($x_dates['range']) - 1; $index++) {
                // removing x elements that are larger than bound

                $filter = $gs->filterValuesOutsideRange($serie, $x_dates['range'][$index], $x_dates['range'][$index + 1]);



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

    public function buildCostPieGraphData() {

        $myData = new pData();

        // get data series
        $gs = $this->getGraphSource();
        $series = $gs->getSeries();

        // get amounts for each serie
        $amounts = $gs->getSeriesDataByColumn('amount', 'number');

        // list of all the requested categories and vehicles
        $categories = $this->getCategoriesList();
        $vehicles = $this->getVehiclesList();

        // initialization: a cell for each initially requested category and vehicle
        $data = array_combine($categories['list'], array_fill(0, $categories['count'], 0));
        $data = array_combine($vehicles['list'], array_fill(0, $vehicles['count'], $data));

        // filling $data with the real values
        $description = array();
        foreach ($series as $key => $serie) {
            $vid = $serie->getVehicleId();

            $description[] = count($vid) > 1 ? 'All vehicles' : Doctrine_Core::getTable('Vehicle')->findOneById($vid)->getName();

            // if $vid has more than one element, vehicles are stacked, so we got only one chart
            $vid = count($vid) > 1 ? 1 : $vid;

            $cid = $serie->getCategoryId();

            $value = array_sum($amounts[$key]);

            $data[$vid][$cid] = $value;
        }


        // building chart data for each vehicle
        $counter = -1;
        foreach ($data as $key => $value) {

            $counter++;

            $points = array_values($data[$key]);

            // If all values are VOID, we skip the serie
            if ($points === array_fill(0, count($points), 0)) {
                continue;
            }


            $id = $series[$counter]->getId();
            $myData->addPoints($points, $id);
            $myData->setSerieDescription($id, $description[$counter]);
            $myData->setSerieDrawable($id, false); // series will be activated in plotPieChart
        }

        // adding labels
        $myData->addPoints($categories['names'], 'labels');
        $myData->setAbscissa('labels');


        return $myData;
    }

     protected function buildTripAnnualGraphData() {

        $gs = $this->getGraphSource();

        $myData = new pData();

        // x-axis
        $dates = $gs->getSeriesDataByColumn('date', 'datetime');

        $x_dates = $gs->buildXAxisDataByDateRange($dates);


        $x_id = "x-axis";
        $myData->addPoints($x_dates['years'], $x_id);
        $myData->setSerieDescription($x_id, 'Years');
        $myData->setAbscissa($x_id);


        // Y-axis
        $kilometers = $gs->getSeriesDataByColumn('kilometers', 'number');
        $y_series = $gs->getSeries();

        $myData->setAxisName(0, "Annual travel [km/year]");

        $y_data = array();
        foreach ($dates as $skey => $serie) {

            for ($index = 0; $index < count($x_dates['range']) - 1; $index++) {
                // removing x elements that are larger than bound

                $filter = $gs->filterValuesOutsideRange($serie, $x_dates['range'][$index], $x_dates['range'][$index + 1]);



                if (!$filter) {
                    $y_travel = 0;
                } else {

                    // getting corresponding y elements
                    $y_filtered = array_intersect_key($kilometers[$skey], $filter);

                    $y_travel = max($y_filtered) - min($y_filtered);
                }

                $y_data[$skey][$index] = $y_travel;
            }

            $y_id = $y_series[$skey]->getId();
            $myData->addPoints($y_data[$skey], $y_id);
            $myData->setSerieDescription($y_id, $y_series[$skey]->getLabel());
        }

        return $myData;
    }

}

