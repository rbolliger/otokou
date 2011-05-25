<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of otkTestUtility
 *
 * @author Raffaele Bolliger <raffaele.bolliger at gmail.com>
 */
class otkTestUtility {

    public function getUserId($username, $throw = true) {
        $v = Doctrine_Core::getTable('sfGuardUser')->findOneByUsername($username);

        if (!$v && $throw) {
            throw new sfException(sprintf('Cannot find any user with username %s', $username));
        } elseif (!$v && !$throw) {
            return null;
        }

        return $v->getId();
    }

    public function getVehicleId($name, $throw = true) {

        $v = Doctrine_Core::getTable('Vehicle')->findOneBySlug($name);

        if (!$v && $throw) {
            throw new sfException(sprintf('Cannot find any vehicle with slug %s', $name));
        } elseif (!$v && !$throw) {
            return null;
        }

        return $v->getId();
    }

    public function getOneChargeByParams($params = array()) {

        if (!$params) {
            throw new sfException('At least one parameter must be specified');
        }

        $q = Doctrine_Core::getTable('Charge')->createQuery('c');

        foreach ($params as $key => $value) {
            $q->andWhere('c.' . $key . ' = ?', $value);
        }

        return $q->fetchOne();
    }

    public function getIdForCategory($category, $throw = true) {
        $c = Doctrine_Core::getTable('Category')->findOneByName($category);

        if (!$c && $throw) {
            throw new sfException(sprintf('Cannot find any category with name %s', $category));
        } elseif (!$c && !$throw) {
            return null;
        }
        return $c->getId();
    }

    public function rmDirTree($dir) {

        if (is_dir($dir)) {
            $objects = scandir($dir);
            foreach ($objects as $object) {
                if ($object != "." && $object != "..") {
                    if (filetype($dir . "/" . $object) == "dir")
                        rrmdir($dir . "/" . $object); else
                        unlink($dir . "/" . $object);
                }
            }
            reset($objects);
            rmdir($dir);
        }
    }

}

