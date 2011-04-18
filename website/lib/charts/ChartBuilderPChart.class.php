<?php

class ChartBuilderPChart extends ChartBuilder {

    public function doDisplay() {


        $path = str_replace('images/', '', $this->getChartPath('web'));
        return image_tag($path, $this->getAttributes());
    }

    public function doGenerate() {

        $data = parent::doGenerate();
        if(!$data){
            return $data;
        }

        $name = $this->getParameter('chart_name');

        switch ($name) {
            case 'cost_per_km':
                $picture = $this->buildPicture($data);
                $chart = $this->plotScatterChart($picture, $data);
                break;

            case 'cost_per_year':
                $picture = $this->buildPicture($data);
                $picture = $this->plotBarChart($picture);
                break;

            case 'cost_pie':

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

                $picture = $this->buildPicture($data);
                $picture = $this->plotBarChart($picture);
                break;

            case 'trip_monthly':

                $picture = $this->buildPicture($data);

                $options = array(
                    'LabelSkip' => 5,
                    'LabelRotation' => 90,
                );
                $picture = $this->plotBarChart($picture, $options);
                break;

            case 'consumption_per_distance':

                $picture = $this->buildPicture($data);
                $chart = $this->plotScatterChart($picture, $data);
                break;

            default:

                throw new sfException(sprintf('Unknown chart name %s', $name));
                break;
        }

        $picture->render($this->getChartPath('system'));

        sfContext::getInstance()->getLogger()->info(sprintf('Rendering chart %s with pChart.', $this->getChartPath()));

        return true;
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

    protected function buildCostPerKmChartData($cd = array()) {

        if (!$cd) {
            $cd = parent::buildCostPerKmChartData();
        }
        if(!$cd){
            return $cd;
        }

        $this->setOption('title', $cd['title']);

        $myData = new pData();

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

        $data = parent::buildCostPerYearChartData();
        if(!$data){
            return $data;
        }

        $this->setOption('title', $data['title']);

        $myData = new pData();

        // x-axis
        $x_id = $data['x']['id'];
        $myData->addPoints($data['x']['values'], $x_id);
        $myData->setSerieDescription($x_id, $data['x']['description']);
        $myData->setAbscissa($x_id);

        // Y-axis
        $gs = $this->getChartSource();
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

    protected function buildCostPieChartData() {

        $data = parent::buildCostPieChartData();
        if(!$data){
            return $data;
        }

        $this->setOption('title', $data['title']);

        $myData = new pData();

        $y_series = $data['y']['series'];
        // building chart data for each vehicle
        foreach ($y_series as $key => $y) {

            $myData->addPoints($y['values'], $y['id']);
            $myData->setSerieDescription($y['id'], $y['label']);
            $myData->setSerieDrawable($y['id'], false); // series will be activated in plotPieChart
        }

        $x = $data['x'];
        // adding labels
        $myData->addPoints($x['values'], $x['id']);
        $myData->setAbscissa($x['id']);


        return $myData;
    }

    protected function buildConsumptionPerDistanceChartData() {

        $data = parent::buildConsumptionPerDistanceChartData();
        if(!$data){
            return $data;
        }

        $myData = $this->buildCostPerKmChartData($data);

        return $myData;
    }

    protected function buildTripChartData($unit) {

        $data = parent::buildTripChartData($unit);
        if(!$data){
            return $data;
        }

        $this->setOption('title', $data['title']);

        $myData = new pData();

        // x-axis
        $x_id = $data['x']['id'];
        $myData->addPoints($data['x']['values'], $x_id);
        $myData->setSerieDescription($x_id, $data['x']['description']);
        $myData->setAbscissa($x_id);


        // Y-axis
        $myData->setAxisName(0, $data['y']['description']);

        foreach ($data['y']['series'] as $key => $serie) {

            $y_id = $serie['id'];
            $myData->addPoints($serie['values'], $y_id);
            $myData->setSerieDescription($y_id, $serie['label']);
        }

        return $myData;
    }

}

