<?php

require_once dirname(__FILE__) . '/../lib/vendor/symfony/lib/autoload/sfCoreAutoload.class.php';
sfCoreAutoload::register();

class ProjectConfiguration extends sfProjectConfiguration {

    public function setup() {
        $this->enablePlugins('sfDoctrinePlugin');
        $this->enablePlugins('sfDoctrineGuardPlugin');
        $this->enablePlugins('ioMenuPlugin');
        $this->enablePlugins('sfFormExtraPlugin');
        $this->enablePlugins('sfDoctrineApplyPlugin');
        $this->enablePlugins('sfTCPDFPlugin');
        $this->enablePlugins('sfTaskExtraPlugin');
        $this->enablePlugins('sfZurbFoundationPlugin');

        //// changing webDir if on donax
        if (@$_SERVER['HTTP_HOST'] == 'otokou.donax.ch') {
            $this->setWebDir($this->getRootDir() . '/..');
        }
    }

}
