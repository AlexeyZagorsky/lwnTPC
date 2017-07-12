<?php
/**
 * Created by PhpStorm.
 * User: Mazey_AI
 * Date: 26.06.2017
 * Time: 16:18
 */

class htmlAnalysis {

    const TPL_FILE_NAME = 'htmlAnalysis.tpl';

    private $tpl;
    private $Data;

    public function __construct($A) {
        $this->Data = $A;
    }

    private function loadTemplate() {
        if (file_exists($this::TPL_FILE_NAME)) {
            $this->tpl = file_get_contents($this::TPL_FILE_NAME);
            $retValue = $this->tpl;
        } else {
            $retValue = false;
        }
        return $retValue;
    }

    public function display() {

    }

}