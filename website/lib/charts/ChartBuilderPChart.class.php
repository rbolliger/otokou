<?php

class ChartBuilderPChart extends ChartBuilder {

    public function doDisplay() {


        $path = str_replace('images/', '', $this->getChartPath('web'));
        return image_tag($path, $this->getAttributes());
    }

    public function doGenerate() {

        $done = true;

        $name = $this->getParameter('chart_name');

        switch ($name) {
            case 'cost_per_km':
                $data = $this->buildCostPerKmChartData();
                $picture = $this->buildPicture($data);
                $chart = $this->plotScatterChart($picture, $data);
                break;

            case 'cost_per_year':
                $data = $this->buildCostPerYearChartData();
                $picture = $this->buildPicture($data);
                $picture = $this->plotBarChart($picture);
                break;

            case 'cost_pie':
                $data = $this->buildCostPieChartData();

                $raw_data = $data->getData();
                // -1 to remove abscissa
                $n_series = count($raw_data['Series']) - 1;

                $options = array(
                    'chart_height' => 310,
                    'chart_area_height' => 310 * ceil(($n_series + 1) / 2),
                    'n_series' => $n_series,
                    'legend_height' => 0,
                    'x_label_height' => 0,
                );
                $picture = $this->buildPicture($data, $options);
                $pie = $this->plotPieChart($picture, $data, $options);
                break;

            case 'trip_annual':
                $data = $this->buildTripChartData('year');
                $picture = $this->buildPicture($data);
                $picture = $this->plotBarChart($picture);
                break;

            case 'trip_monthly':
                $data = $this->buildTripChartData('month');
                $picture = $this->buildPicture($data);

                $options = array(
                    'LabelSkip' => 5,
                    'LabelRotation' => 90,
                );
                $picture = $this->plotBarChart($picture, $options);
                break;

            default:

                throw new sfException(sprintf('Unknown chart name %s', $name));
                break;
        }

        $picture->render($this->getChartPath('system'));

        sfContext::getInstance()->getLogger()->info(sprintf('Rendering chart %s with pChart.', $this->getChartPath()));

        return $done;
    }

    protected function buildPicture(pData $data, $options = array()) {


        $title_height = isset($options['title_height']) ? $options['title_height'] : 50;
        $ga_height = isset($options['chart_area_height']) ? $options['chart_area_height'] : 310;
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
            $picture->drawText($posX, $posY - $chart_height / 2 * .9, $raw_data['Series'][$id]['Description'], $TextSettings);
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

    protected function plotBarChart(pImage $picture, $options = array()) {

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
        $Settings = array_merge($Settings, $options);
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

    protected function buildCostPerKmChartData() {

        $gs = $this->getChartSource();

        $myData = new pData();

        $cd = $gs->buildCostPerKmChartData($this->getParameter('range_type'));

        $x_id = $cd['x']['id'];
        $myData->addPoints($cd['x']['values'], $x_id);

        $myData->setAxisName(0, $cd['x']['description']);
        $myData->setAxisXY(0, AXIS_X);
        $myData->setAxisPosition(0, AXIS_POSITION_BOTTOM);

        $is_date = $this->getParameter('range_type') == 'date' ? true : false;
        $display_mode = $is_date ? AXIS_FORMAT_DATE : AXIS_FORMAT_DEFAULT;
        $display_format = $is_date ? 'd-M-Y' : null;

        $myData->setAxisDisplay(0, $display_mode, $display_format);

        foreach ($cd['y']['series'] as $key => $serie) {

            $y_id = $cd['y']['series'][$key]['id'];
            $myData->addPoints($cd['y']['series'][$key]['values'], $y_id);
            $myData->setSerieOnAxis($y_id, 1);

            $myData->setScatterSerie($x_id, $y_id, $key);
            $myData->setScatterSerieDescription($key, $cd['y']['series'][$key]['label']);
            $myData->setScatterSerieWeight($key, 0.7);
        }

        $myData->setAxisName(1, $cd['y']['description']);
        $myData->setAxisXY(1, AXIS_Y);
        $myData->setAxisPosition(1, AXIS_POSITION_LEFT);

        return $myData;
    }

    protected function buildCostPerYearChartData() {

        $gs = $this->getChartSource();

        $data = $gs->buildCostPerYearChartData();

        $myData = new pData();

        // x-axis
        $x_id = $data['x']['id'];
        $myData->addPoints($data['x']['values'], $x_id);
        $myData->setSerieDescription($x_id, $data['x']['description']);
        $myData->setAbscissa($x_id);

        // Y-axis
        $y_series = $gs->getSeries();

        $myData->setAxisName(0, $data['y']['description']);

        $y_data = array();
        foreach ($y_series as $skey => $serie) {

            $y_id = $data['y']['series'][$skey]['id'];
            $myData->addPoints($data['y']['series'][$skey]['values'], $y_id);
            $myData->setSerieDescription($y_id, $data['y']['series'][$skey]['label']);
        }

        return $myData;
    }

    public function buildCostPieChartData() {

        $myData = new pData();

        // get data series
        $gs = $this->getChartSource();
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
            if (count($vid) > 1) {
                $vid = 1;
            }

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

    protected function buildTripChartData($unit) {

        $units = array('year', 'month');
        if (!in_array($unit, $units)) {
            throw new sfException(sprintf('Unknown unit "%s". Accepted values are %s', $unit, implode(', ', $units)));
        }

        $gs = $this->getChartSource();

        $myData = new pData();

        // x-axis
        $dates = $gs->getSeriesDataByColumn('date', 'datetime');

        $x_dates = $gs->buildXAxisDataByDateRange($dates, $unit);

        $x_id = "x-axis";
        $myData->addPoints($x_dates['labels'], $x_id);
        $myData->setSerieDescription($x_id, $x_dates['description']);
        $myData->setAbscissa($x_id);


        // Y-axis
        $kilometers = $gs->getSeriesDataByColumn('kilometers', 'number');
        $y_series = $gs->getSeries();

        $title = $unit == 'year' ? "Annual travel [km/year]" : 'Monthly travel [km/month]';
        $myData->setAxisName(0, $title);

        $y_data = array();
        $prev_y = false;
        foreach ($dates as $skey => $serie) {

            for ($index = 0; $index < count($x_dates['range']) - 1; $index++) {
                // removing elements outside the required range
                $filter = $gs->filterValuesOutsideRange($serie, $x_dates['range'][$index], $x_dates['range'][$index + 1]);



                if (!$filter) {
                    $y_travel = 0;
                } else {

                    // getting corresponding y elements
                    $y_filtered = array_intersect_key($kilometers[$skey], $filter);

                    // set the first value form prev_y, which is not 0, but the lowest distance runned by
                    // the vehicle. This depends on the filters applied by the user.
                    if (false === $prev_y) {
                        $prev_y = min($y_filtered);
                    }

                    $y_travel = max($y_filtered) - $prev_y;
                    $prev_y = max($y_filtered);
                }

                $y_data[$skey][$index] = $y_travel;

//                 echo $x_dates['range'][$index].
//                ' ('.date('Y-m-d',$x_dates['range'][$index]).') => '.
//                        $x_dates['range'][$index+1].
//                        ' ('.date('Y-m-d',$x_dates['range'][$index+1]).') => '.
//                        implode(', ',$filter).' => '.
//                         implode(', ',$y_filtered).' => '.
//                         '<b>'.$y_travel.'</b>'.
//                        "<br \>";
            }




            $y_id = $y_series[$skey]->getId();
            $myData->addPoints($y_data[$skey], $y_id);
            $myData->setSerieDescription($y_id, $y_series[$skey]->getLabel());
        }


//            foreach ($y_data[0] as $cip => $y) {
//                $x = $x_dates['range'][$cip];
//
//                echo date('Y-M-d',$x)." => ".$y."<br \>\n";
//            }
//        die();

        return $myData;
    }

}

