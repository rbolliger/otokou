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

    public function getChartPath($type = 'web') {

        return $this->getChartBasePath($type) . '/' . $this->getChartName();
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

    public function getChartBasePath($type = 'web') {

        $path = sfConfig::get('app_charts_base_path', '/charts');


        switch ($type) {
            case 'web':
                $path = ($path[0] == '/' ? substr($path, 1) : $path);

                break;

            case 'system':
                $path = ($path[0] == '/' ? $path : '/' . $path);

                // adding "images" folder
                $path = '/' . sfConfig::get('sf_web_images_dir_name', 'images') . $path;

                $path = $this->convertToSystemPath($path);

                $path = sfConfig::get('sf_web_dir') . $path;


                break;

            default:

                throw new sfException('Unknown option ' . $type);
        }

        return $path;
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
