<?php

class GraphBuilderPChart extends GraphBuilder {

    public function doDisplay() {

        $path = str_replace('images/','',$this->getGraphPath('web'));
        return image_tag($path, $this->getAttributes());
    }

    public function doGenerate() {

        $data = $this->buildData();

        $picture = $this->buildPicture($data);

        $picture->render($this->getGraphPath('system'));

        sfContext::getInstance()->getLogger()->info(sprintf('Rendering graph %s with pGraph.',$this->getGraphPath()));

    }

    protected function buildData() {

        $myData = new pData();
        $myData->addPoints(array(-29, 22, 25, -40, -41, 22, -27, -9), "Serie1");
        $myData->setSerieDescription("Serie1", "Serie 1");
        $myData->setSerieOnAxis("Serie1", 0);

        $myData->addPoints(array(17, -17, 17, 37, -41, 50, -48, -4), "Serie2");
        $myData->setSerieDescription("Serie2", "Serie 2");
        $myData->setSerieOnAxis("Serie2", 0);

        $myData->addPoints(array(35, 40, 15, -1, 8, -25, 0, -16), "Serie3");
        $myData->setSerieDescription("Serie3", "Serie 3");
        $myData->setSerieOnAxis("Serie3", 0);

        $myData->addPoints(array("January", "February", "March", "April", "May", "June", "July", "August"), "Absissa");
        $myData->setAbscissa("Absissa");

        $myData->setAxisPosition(0, AXIS_POSITION_LEFT);
        $myData->setAxisName(0, "1st axis");
        $myData->setAxisUnit(0, "km");

        return $myData;
    }

    protected function buildPicture($data) {

        $myPicture = new pImage(700, 230, $data);
        $myPicture->drawRectangle(0, 0, 699, 229, array("R" => 0, "G" => 0, "B" => 0));

        $myPicture->setShadow(TRUE, array("X" => 1, "Y" => 1, "R" => 50, "G" => 50, "B" => 50, "Alpha" => 20));

        $myPicture->setFontProperties(array("FontName" => sfConfig::get('sf_web_dir')."/fonts/Bedizen.ttf", "FontSize" => 14));
        $TextSettings = array("Align" => TEXT_ALIGN_MIDDLEMIDDLE
            , "R" => 48, "G" => 48, "B" => 48);
        $myPicture->drawText(350, 25, "cvbxcfhfghx vhn", $TextSettings);

        $myPicture->setShadow(FALSE);
        $myPicture->setGraphArea(50, 50, 675, 190);
        $myPicture->setFontProperties(array("R" => 0, "G" => 0, "B" => 0, "FontName" => sfConfig::get('sf_web_dir')."/fonts/pf_arma_five.ttf", "FontSize" => 6));

        $Settings = array("Pos" => SCALE_POS_LEFTRIGHT
            , "Mode" => SCALE_MODE_FLOATING
            , "LabelingMethod" => LABELING_ALL
            , "GridR" => 177, "GridG" => 200, "GridB" => 204, "GridAlpha" => 50, "TickR" => 0, "TickG" => 0, "TickB" => 0, "TickAlpha" => 50, "LabelRotation" => 0, "CycleBackground" => 1, "DrawXLines" => 1, "DrawSubTicks" => 1, "SubTickR" => 255, "SubTickG" => 0, "SubTickB" => 0, "SubTickAlpha" => 50, "DrawYLines" => ALL);
        $myPicture->drawScale($Settings);

        $myPicture->setShadow(TRUE, array("X" => 1, "Y" => 1, "R" => 50, "G" => 50, "B" => 50, "Alpha" => 10));

        $Config = array("ForceTransparency" => 30, "AroundZero" => 1);
        $myPicture->drawAreaChart($Config);

        $Config = array("FontR" => 0, "FontG" => 0, "FontB" => 0, "FontName" => sfConfig::get('sf_web_dir')."/fonts/GeosansLight.ttf", "FontSize" => 6, "Margin" => 6, "Alpha" => 30, "BoxSize" => 5, "Style" => LEGEND_BOX
            , "Mode" => LEGEND_VERTICAL
            , "Family" => LEGEND_FAMILY_CIRCLE
        );
        $myPicture->drawLegend(651, 16, $Config);

        return $myPicture;
    }

}

