<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of otkHasher
 *
 * @author Raffaele Bolliger <raffaele.bolliger at gmail.com>
 */
class otkHasher {


    public static function hash($proposal,$record) {

        return sha1($proposal);

    }
}

