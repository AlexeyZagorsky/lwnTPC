<?php
/**
 * Created by PhpStorm.
 * User: Mazey_AI
 * Date: 26.06.2017
 * Time: 16:18
 */

class htmlAnalysis {

    const TPL_FILE_NAME = 'htmlAnalysis.tpl';
    const TPL_KEY_LP = '{';
    const TPL_KEY_RP = '}';

    private $Data;
    private $Rcm;
    private $Values;
    private $html;

    public function __construct($A) {
        $this->Data = $A;
        $this->Values = array();
        $this->Rcm = array();
        $this->html = '';
        /*echo '<pre>';
        var_dump($this->Data);
        echo '</pre>';*/
    }

    private function loadTemplate() {
        if (file_exists($this::TPL_FILE_NAME)) {
            $this->html = file_get_contents($this::TPL_FILE_NAME);
            $retValue = true;
        } else {
            $retValue = false;
        }
        return $retValue;
    }

    private function setTemplateKey($key, $value) {
        $key = $this::TPL_KEY_LP . $key . $this::TPL_KEY_RP;
        $this->Values[$key] = $value;
    }

    private function parseTemplate() {
        foreach ($this->Values as $mask => $replace) {
            $this->html = str_replace($mask, $replace, $this->html);
        }
    }

    private function getRecommendations($rcm) {
        $str = '';
        if (!empty($rcm)) {
            $str = '<div class="ha-recommendation">Recommendations:</div><ol>';
            $n = count($rcm);
            for ($i = 0; $i < $n; $i++) {
                $str .= '<li>' . $rcm[$i] . '</li>';
            }
            $str .= '</ol>';
        }
        return $str;
    }

    private function analyzeGenInfo() {
        $rcm = array();
        $p = strrpos($this->Data['filename'], '.');
        $fileExt = substr($this->Data['filename'], $p + 1, strlen($this->Data['filename']) - $p - 1);
        if ($fileExt != 'html') {
            if ($fileExt == 'htm') {
                $rcm[0] = 'Old style of ';
            } else {
                $rcm[0] = 'Weird ';
            }
            $rcm[0] .= 'HTML file extension detected. Change it to .html';
        }
        $this->Rcm['genInfo'] = $this->getRecommendations($rcm);
    }

    private function analyzeDoctype() {
        $rcm = array();
        if (!$this->Data['doctypeExists']) {
            $rcm[0] = 'There is no doctype in the document. Add it.';
            if ($this->Data['htmlVersion'] < 5) {
                $rcm[1] = 'Auto-generated doctype refers to old HTML version.';
            }
        }
        if ($this->Data['htmlVersion'] < 5) {
            $rcm[2] = 'Old HTML version is supposed.';
        }
        $this->Rcm['doctype'] = $this->getRecommendations($rcm);
    }

    private function analyzeEncoding() {
        $rcm = array();
        if (!preg_match('/\"UTF/i', $this->Data['encoding'])) {
            $rcm[0] = 'Obsoleted charset is detected. Convert the document encoding to UTF.';
        }
        if (!$this->Data['charsetTagExists']) {
            $rcm[1] = 'There is no &lt;meta charset&gt; tag in the document. Add it.';
        }
        $this->Rcm['encoding'] = $this->getRecommendations($rcm);
    }

    private function analyzeTitle() {
        $rcm = array();
        if ($this->Data['title'] == '') {
            $rcm[0] = 'No title detected in the HTML document. Add it.';
        }
        $this->Rcm['title'] = $this->getRecommendations($rcm);
    }

    protected function analyze() {
        $this->analyzeGenInfo();
        $this->analyzeDoctype();
        $this->analyzeEncoding();
        $this->analyzeTitle();
    }

    public function display() {
        if ($this->loadTemplate()) {
            if (!$this->Data['isNew']) {
                $this->analyze();

                $this->setTemplateKey('HTML_FILE_NAME', $this->Data['filename']);
                $this->setTemplateKey('HTML_FILE_SIZE', $this->Data['filesize']);
                $this->setTemplateKey('RCM_GEN_INFO', $this->Rcm['genInfo']);

                if ($this->Data['doctypeExists']) {
                    $this->setTemplateKey('DOCTYPE_SRC', $this->Data['doctype']);
                    $this->setTemplateKey('DOCTYPE_GENERATED', 'not required');
                } else {
                    $this->setTemplateKey('DOCTYPE_SRC', 'not found');
                    $this->setTemplateKey('DOCTYPE_GENERATED', $this->Data['doctype']);
                }
                $this->setTemplateKey('DOCTYPE_HTML_VERSION', $this->Data['htmlVersion']);
                $this->setTemplateKey('RCM_DOCTYPE',$this->Rcm['doctype']);

                $this->setTemplateKey('ENCODING_INITIAL', $this->Data['initialCharset']);
                if ($this->Data['charsetTagExists']) {
                    $this->setTemplateKey('ENCODING_TAG', $this->Data['charsetTag']);
                    $this->setTemplateKey('ENCODING_TAG_GENERATED', 'not required');
                } else {
                    $this->setTemplateKey('ENCODING_TAG', 'not found');
                    $this->setTemplateKey('ENCODING_TAG_GENERATED', $this->Data['charsetTag']);
                }
                $this->setTemplateKey('ENCODING_CHARSET', $this->Data['encoding']);
                $this->setTemplateKey('RCM_ENCODING', $this->Rcm['encoding']);

                $this->setTemplateKey('TITLE_SRC', $this->Data['title']);
                $this->setTemplateKey('RCM_TITLE', $this->Rcm['title']);

                if ($this->Data['stylesInt'] == '') {
                    $this->setTemplateKey('CSS_INT', 'not found');
                } else {
                    $this->setTemplateKey('CSS_INT', 'found');
                }
                if (empty($this->Data['stylesExt'])) {
                    $this->setTemplateKey('CSS_EXT', 'not found');
                } else {
                    $this->setTemplateKey('CSS_EXT', 'found');
                }
                $this->setTemplateKey('RCM_CSS', '');

                if ($this->Data['scriptsInt'] == '') {
                    $this->setTemplateKey('JS_INT', 'not found');
                } else {
                    $this->setTemplateKey('JS_INT', 'found');
                }
                if (empty($this->Data['scriptsExt'])) {
                    $this->setTemplateKey('JS_EXT', 'not found');
                } else {
                    $this->setTemplateKey('JS_EXT', 'found');
                }
                $this->setTemplateKey('RCM_SCRIPTS', '');

                //$this->setTemplateKey('', );

                $this->parseTemplate();
                print($this->html);
            }
        } else {
            echo 'Analysis template is not found.';
        }
    }

}