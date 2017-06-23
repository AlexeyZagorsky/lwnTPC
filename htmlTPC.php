<?php
/**
 * Project: Elwen Text Processing Center (lwnTPC)
 * File: htmlDocument.php
 * Date: 27.05.2016
 *
 * Class htmlTPC
 * Provides various methods to process html data.
 *
 * @copyright 2016 VITIM CS
 * @author Alex Zagorsky
 * @package lnw\TPC\htmlTPC
 * @version 0.1
 */

require_once('htmlDocument.php');

class htmlTPC {
	
    private $htmlDoc;
	private $htmlDocColl;

	public function __construct($encoding) {
		$this->htmlDoc = new htmlDocument($encoding);
		$this->htmlDocColl = null;
	}
	
	public function getDocument() {
		return $this->htmlDoc;
	}
	
	public function load($src_html_file) {
		return $this->htmlDoc->loadHTMLFile($src_html_file);
	}
	
	public function save() {
		return $this->htmlDoc->saveHTMLFile();
	}
	
	public function analyze($mode = false) {
		$this->htmlDoc->analyze($mode);
	}
	
	public function convert($destCharset) {
        $this->htmlDoc->setHtmlVersion(5);
        $this->htmlDoc->convertEncoding($destCharset);
        $this->normalize();
	}

    /**
     * Transform the file to HTML 5 format
     */
	protected function normalize() {
        // set doctype
        $this->htmlDoc->setDocType();
		// set charset
		$this->htmlDoc->setCharsetTag();
	}
	
}

?>