<?php
/**
 * Project: Elwen Text Processing Center (lwnTPC)
 * File: htmlDocument.php
 * Date: 24.05.2016
 *
 * Class htmlDocument
 * Extends DOMDocument and provides various methods to process html data.
 *
 * @copyright 2017 VITIM CS
 * @author Alex Zagorsky
 * @package lnw\TPC\htmlDocument
 * @version 0.2
 */

require_once('htmlDoctype.php');

class htmlDocument extends DOMDocument {

    /** @var string $srcFileName	Source HTML file name to be processed. */
    private $srcFileName;
    /** @var string $resFileName	Resulting HTML file name to save the data after processing. */
    private $resFileName;
    /** @var bool $fileOverwrite    True if you'd like to overwrite the source file. */
    public $fileOverwrite;
    /** @var htmlDoctype $docType   Doctype object for the HTML document. */
    private $docType;
	/** @var string $htmlCharset    Actual encoding of the HTML document, e.g. UTF-8. */
	private $htmlCharset;
    /** @var int $htmlVersion       HTML version of the document according to doctype tag. */
	private $htmlVersion;
	/** @var bool $analysisDone     True if the HTML document has been analyzed by analyze() method. */
	private $analysisDone;
	/** @var bool $doctypeTagExists True if a doctype tag physically exists in the HTML document. */
	private $doctypeTagExists;
	/** @var bool $charsetTagExists True if a meta charset tag physically exists in the HTML document. */
	private $charsetTagExists;

	/**
	 * htmlDocument constructor.
	 *
	 * @param string $encoding      The encoding of the document.
	 */
    public function __construct($encoding = 'UTF-8') {
        parent::__construct('1.0', $encoding);
        $this->docType = null;
		$this->srcFileName = '';
		$this->resFileName = '';
        $this->fileOverwrite = false;
        $this->htmlCharset = $encoding;
        $this->encoding = $encoding;
        $this->substituteEntities = false;
        $this->formatOutput = true;
        $this->analysisDone = false;
    }

    /**
     * Create the resulting HTML file name.
     * Required if you'd like to save the results in a separate file instead of overwriting the source one.
     *
     * @return string      Generated file name to save HTML data in it.
     */
    private function createResFileName() {
        if ($this->fileOverwrite) {
            $resFileName = $this->srcFileName;
        } elseif (!$this->resFileName) {
            $resFileName = substr_replace($this->srcFileName, '.result', strrpos($this->srcFileName, '.'), 0);
        } else {
            $resFileName = $this->resFileName;
        }
        return $resFileName;
    }

    /**
     * Return $htmlVersion property value.
     *
     * @return int      Property value.
     */
    public function getHtmlVersion() {
        return $this->htmlVersion;
    }

    /**
     * Set htmlVersion property value.
     *
     * @param int $newVersion     A value to set.
     *
     * @return void
     */
    public function setHtmlVersion($newVersion) {
        if ($newVersion >= 5) {
            $this->htmlVersion = $newVersion;
        } else {
            $this->htmlVersion = 4;
        }
    }

	/**
	 * Check if the class instance is new.
     * The instance is considered new, if it has not been loaded or saved yet (not linked to a file).
	 *
	 * @return bool     True, if the class instance is new, otherwise false.
	 */
	private function isNew() {
		$result = false;
		if ($this->srcFileName == '' && $this->resFileName == '') {
			$result = true;
		}
		return $result;
	}

    /**
     * Add a doctype tag to the HTML document. HTML 5 doctype format is used.
     *
     * @return void
     */
	public function setDocType() {
	    if ($this->getHtmlVersion() == 5) {
            $data = $this->saveHTML();
            $sp = strpos($data, '<!DOCTYPE html');
            $ep = strpos($data, '>');
            $data = substr_replace($data, '<!DOCTYPE html>', $sp, $ep - $sp + 1);
            $this->loadHTML($data);
        }
	}

    /**
     * Locate <meta charset> node in the HTML document.
     *
     * @return DOMNode|null      Meta tag object or null if it was not found.
     */
	private function locateCharsetTag() {
        $charsetTag = null;
        $metaTags = $this->getElementsByTagName('meta');
        for ($i = 0; $i < $metaTags->length; $i++) {
            $tag = $metaTags->item($i);
            if ( $tag->hasAttribute('charset') || $tag->hasAttribute('http-equiv') && ($tag->getAttribute('http-equiv') == 'Content-Type') ) {
                $charsetTag = $tag;
                break;
            }
        }
        return $charsetTag;
    }

    /**
     * Create <meta charset> element with attributes which are specific to HTML version defined in $htmlVersion property.
     *
     * @return DOMElement       Meta tag object.
     */
    private function createCharsetTag() {
        $charsetTag = $this->createElement('meta');
        if ($this->htmlVersion < 5) {
            $charsetTag->setAttribute('http-equiv', 'Content-Type');
            $charsetTag->setAttribute('content', 'text/html; charset=' . $this->encoding);
        } else {
            $charsetTag->setAttribute('charset', $this->encoding);
        }
        return $charsetTag;
    }

    /**
     * Return <meta charset> element as a string.
     * Use it instead of standard DOMElement::C14N().
     *
     * @param DOMElement $charsetElement        Meta tag object.
     *
     * @return string       String representation of the <meta charset> element.
     */
    protected function getCharsetTag($charsetElement) {
		$str = '<' . $charsetElement->tagName;
		for ($i = 0; $i < $charsetElement->attributes->length; $i++) {
            $str .= ' ' . $charsetElement->attributes->item($i)->nodeName . '="' . $charsetElement->attributes->item($i)->nodeValue . '"';
        }
        $str .= '>';
		return $str;
    }

    /**
     * Insert <meta charset> tag into the HTML document.
     *
     * @param $destCharset string       Charset value, e.g. UTF-8. If not specified, the $encoding property is used.
     *
     * @return void
     */
	public function setCharsetTag($destCharset = '') {
		if ($destCharset == '') {
		    $this->encoding = $this->htmlCharset;
        } else {
		    $this->encoding = $destCharset;
            $this->htmlCharset = $destCharset;
        }
		// create new meta charset tag
        $newCharsetTag = $this->createCharsetTag();
		// locate the old meta charset tag (if it exists)
		$oldCharsetTag = $this->locateCharsetTag();
		if ($oldCharsetTag) {
			// replace the old meta charset tag with the new one
            $oldCharsetTag->parentNode->replaceChild($newCharsetTag, $oldCharsetTag);
		} else {
			// insert the new meta charset tag into the <head> section of the HTML document
			$headElement = $this->getElementsByTagName('head');
			if ($headElement) {
				$headElement->item(0)->appendChild($newCharsetTag);
			}
		}
	}

    /**
     * Convert HTML file encoding to specified charset. The source encoding is defined by the $encoding property.
     *
     * @param string $destCharset       Destination charset.
     *
     * @return void
     */
	public function convertEncoding($destCharset) {
        $data = $this->saveHTML();
        $data = iconv($this->encoding, $destCharset, $data);
        // todo: implement more reliable way of the meta charset tag update.
        // Need to locate this meta tag, detecting its start and end positions in $data, then extract this tag in a string.
        // Then update charset in that string and update $data.
        $data = str_ireplace('charset=' . $this->encoding, 'charset=' . $destCharset, $data);

        $this->encoding = $destCharset;
		$this->htmlCharset = $destCharset;
		$this->setCharsetTag($destCharset);
        $this->loadHTML($data);
	}

    /**
     * Read HTML data from file to a string.
     * If there is no meta charset tag detected there, it will be inserted.
     * The source file is specified by the $srcFileName property. That file must exist.
     *
     * @return string|bool      The HTML contents of the file or false on failure.
     */
	private function readHTMLFile() {
	    if (!$this->isNew()) {
            $data = file_get_contents($this->srcFileName);
            if ($data !== false) {
                // Detect HTML version. We cannot use htmlDoctype because it has not been created yet.
                $this->htmlVersion = 4; // supposed by default
                if (stripos($data, '<!DOCTYPE html>') !== false) {
                    $this->htmlVersion = 5;
                }
                // Detect meta charset tag
                $head_pos = stripos($data, '<head>');
                $metaTag = '';
                if ($this->htmlVersion < 5) {
                    if (stripos($data, '<meta http-equiv=') === false) {
                        $metaTag = '<meta http-equiv="Content-Type" content="text/html; charset=' . $this->htmlCharset . '">';
                    }
                } else {
                    if (stripos($data, '<meta charset=') === false) {
                        $metaTag = '<meta charset="' . $this->htmlCharset . '">';
                    }
                }
                if ($metaTag != '') {
                    $data = substr($data, 0, $head_pos + 6) . $metaTag . substr($data, $head_pos + 7, strlen($data) - $head_pos - 7);
                }
            }
        } else {
	        $data = false;
        }
        return $data;
    }

    /**
     * Load HTML data from the file $html_file_name. Overrides the parent DOMDocument::loadHTMLFile method.
     *
     * @param string $html_file_name    The path to HTML file.
     * @param int $options              Additional libxml parameters. Optional, 0 by default.
     *
     * @return bool     True on success, false on failure.
     */
    public function loadHTMLFile($html_file_name, $options = 0) {
        if ( file_exists($html_file_name) ) {
            $this->srcFileName = $html_file_name;
            $data = $this->readHTMLFile();
            libxml_use_internal_errors(true);
            if (PHP_VERSION_ID < 50400) {
                // use @ to suppress possible warnings regarding malformed HTML
                $result = @$this->loadHTML($data);
            } else {
                $result = @$this->loadHTML($data, $options);
            }
            if ($result) {
                $this->docType = new htmlDoctype($this->doctype, $html_file_name);
            }
            libxml_clear_errors();
        } else {
            $result = false;
        }
        return $result;
    }

    /**
     * Save HTML data into a file using HTML formatting. Overrides the parent DOMDocument::saveHTMLFile method.
     *
     * @param   string   $res_file_name     The path to HTML file. Optional. If not specified, the resulting file name
     *                                      will be created by the default algorithm. If specified, the fileOverwrite
     *                                      property will be ignored.
     *
     * @return  int|bool        The number of bytes written or FALSE on failure
     */
    public function saveHTMLFile($res_file_name = '') {
        if ($res_file_name) {
            $this->resFileName = $res_file_name;
        } else {
            $this->resFileName = $this->createResFileName();
        }
		if (stripos($this->htmlCharset, 'UTF-') !== false) {
            $data = mb_convert_encoding($this->saveHTML(), $this->htmlCharset, 'HTML-ENTITIES');
        } else {
            $data = $this->saveHTML();
        }
        return file_put_contents($this->resFileName, $data);
    }

	/**
	 * Analyze loaded HTML file.
	 *
     * @param boolean $reportMode       True, if you'd like to display the analysis in a browser.
	 */
	public function analyze($reportMode = false) {
        $this->doctypeTagExists = false;
        $this->charsetTagExists = false;
	    $report = "<pre><strong>Document analysis</strong>\n";
		if (!$this->isNew()) {
		    $report .= "\nHTML file has been loaded: " . $this->srcFileName . "\n";
            $report .= "File size: " . filesize($this->srcFileName) . " bytes\n";
            // Doctype and HTML version
            $report .= "\nDoctype tag ";
			if ($this->docType->isExists()) {
				$this->doctypeTagExists = true;
                $report .= "found: " . htmlspecialchars($this->docType) . "\n";
			} else {
                $report .= "was not found.\nSupposed: " . htmlspecialchars($this->docType) . "\n";
            }
            $this->htmlVersion = $this->docType->getHtmlVersion();
            $report .= "HTML version according to doctype: " . $this->htmlVersion . "\n";
			// Encoding info
            $report .= "\nEncoding: ";
			if ($this->encoding) {
				$this->charsetTagExists = true;
                $report .= $this->encoding . ". Meta charset tag found.";
			} else {
                $report .= "meta charset tag not found.";
            }
            $report .= "\nInitially set: " . $this->htmlCharset . "\n";
		} else {
            $report .= "\nA new HTML file is handled.\n";
        }
        $report .= '</pre>';
		$this->analysisDone = true;
		if ($reportMode) {
		    echo $report;
        }
	}
	
}
