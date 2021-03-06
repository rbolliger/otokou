<?php

/**
 * Chart
 * 
 * This class has been auto-generated by the Doctrine ORM Framework
 * 
 * @package    otokou
 * @subpackage model
 * @author     Raffaele Bolliger
 * @version    SVN: $Id: Builder.php 7490 2010-03-29 19:53:27Z jwage $
 */
class Chart extends BaseChart {
   
    public function getChartFileWebPath() {

        return $this->getChartsWebPath() . '/' . $this->getChartName();
    }
    
    public function getChartFileSystemPath() {

        return $this->getChartsSystemPath() . '/' . $this->getChartName();
    }

    public function getChartsWebPath() {

        $path = sfConfig::get('app_charts_base_path', '/charts');

        $path = ($path[0] == '/' ? substr($path, 1) : $path);

        return $path;
    }

    public function getChartsSystemPath() {

        $path = sfConfig::get('app_charts_base_path', '/charts');

        $path = ($path[0] == '/' ? $path : '/' . $path);

        // adding "images" folder
        $path = '/' . sfConfig::get('sf_web_images_dir_name', 'images') . $path;

        $path = $this->convertToSystemPath($path);

        $path = sfConfig::get('sf_web_dir') . $path;

        return $path;
    }

    public function getChartName() {
        return $this->getHash() . '.' . $this->getChartFormat();
    }

    public function getChartFormat() {

        $format = $this->getFormat();

        $format = (!$format || is_null($format)) ?
                sfConfig::get('app_chart_default_format', 'png') :
                $format;

        return $format;
    }

    protected function convertToSystemPath($path) {

        return str_replace("/", DIRECTORY_SEPARATOR, $path);
    }

    public function delete(Doctrine_Connection $conn = null) {

        parent::delete($conn);

        $path = $this->getChartPath('system');

        if (file_exists($path)) {
            $fs = new sfFilesystem(new sfEventDispatcher());
            $fs->remove($path);
        }
    }

    public function getHash() {
        return sha1($this->getSlug());
    }

}
