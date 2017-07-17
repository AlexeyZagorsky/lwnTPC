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
require_once('htmlAnalysis.php');

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
    /** @var htmlAnalysis $analysis HTML analysis object */
	private $analysis;
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
        $this->analysis = null;
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
            $this->htmlVersion = 5;
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
     * @return DOMElement|null      Meta tag object or null if it was not found.
     */
	private function locateCharsetTag(): DOMElement {
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
        if ($this->getHtmlVersion() < 5) {
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
     * Return the tag content. If there are several tags with the specified name,
     * only the first tag content is returned.
     *
     * @param $tagName string       Tag name.
     *
     * @return string       Tag content. Can be empty if there is no content.
     */
	public function getTagContent($tagName) {
	    $content = '';
	    $Tags = $this->getElementsByTagName($tagName);
	    if ($Tags->length > 0) {
            $tag = $Tags->item(0);
            $content = $tag->nodeValue;
        }
        return $content;
    }

    /**
     * Return external CSS file name(s) linked to the HTML document.
     *
     * @return string|array     CSS file name, if only one CSS is linked to the document;
     *                          array of CSS file names, if there are several CSS links found;
     *                          empty string, if no external CSS links found.
     */
    public function getExtCSS() {
	    $cssFileArray = array();
	    $k = 0;
	    $Tags = $this->getElementsByTagName('link');
	    for ($i = 0; $i < $Tags->length; $i++) {
	        $linkTag = $Tags->item($i);
	        if ($linkTag->getAttribute('rel') == 'stylesheet') {
	            $cssFileArray[$k] = $linkTag->getAttribute('href');
	            $k++;
            }
        }
        $retValue = '';
        if ($k > 0) {
	        if ($k == 1) {
	            $retValue = $cssFileArray[0];
            } else {
	            $retValue = $cssFileArray;
            }
        }
        return $retValue;
    }

    /**
     *  Return external script name(s) linked to the HTML document.
     *
     * @return string|array     Script file name, if it is linked to the document;
     *                          array of script file names, if there are several scripts linked;
     *                          empty string, if no external script links found.
     */
    public function getExtScripts() {
        $scriptArray = array();
        $k = 0;
        $Scripts = $this->getElementsByTagName('script');
        for ($i = 0; $i < $Scripts->length; $i++) {
            $script = $Scripts->item($i);
            if ($script->hasAttribute('src') ) {
                $scriptArray[$k] = $script->getAttribute('src');
                $k++;
            }
        }
        $retValue = '';
        if ($k > 0) {
            if ($k == 1) {
                $retValue = $scriptArray[0];
            } else {
                $retValue = $scriptArray;
            }
        }
        return $retValue;
    }

    /**
     * Check HTML links.
     *
     *
     */
    protected function checkHtmlLinks() {
        $Links = $this->getElementsByTagName('a');
        $cnt = 0;
        $cntHtm = 0;
        for ($i = 0; $i < $Links->length; $i++) {
            $link = $Links->item($i);
            if ($link->hasAttribute('href')) {
                $cnt++;
                $attr = $link->getAttribute('href');
                // cut off internal anchor data, i.e. #xxx at the end of a link
                $p = strpos($attr, '#');
                if ($p > 0) {
                    $attr = substr($attr, 0, $p);
                }
                if (preg_match('/^(http:|mailto:)/i', $attr) === 0) {
                    $p = strrpos($attr, '.');
                    $ext = substr($attr, $p + 1, strlen($attr) - $p - 1);
                    if ($ext == 'htm') {
                        $cntHtm++;
                    }
                }
            }
        }
        $Ret = array();
        $Ret[0] = $cnt; // total number of links
        $Ret[1] = $cntHtm; // links referring to .htm files
        return $Ret;
    }

    /**
     * Convert HTML file encoding to specified charset. The source encoding is defined by the $encoding property.
     *
     * @param string $destCharset       Destination charset.
     *
     * @return void
     */
	public function convertEncoding($destCharset) {
	    if ( (strcasecmp($this->encoding, $destCharset) != 0) && (!$this->isNew()) ) {
            $data = $this->saveHTML();
            $data = iconv($this->encoding, $destCharset, $data);
            // Replace the charset value in the <meta charset> tag
            $head_start = stripos($data, '<head>');
            $head_end = stripos($data, '</head>');
            $sp = stripos($data, '<meta ', $head_start + 6);
            $ep = $sp;
            $metaTag = '';
            while ($sp < $head_end || $sp === false) {
                $ep = strpos($data, '>', $sp);
                $metaTag = substr($data, $sp, $ep - $sp + 1);
                if (stripos($metaTag, 'charset')) {
                    $metaTag = str_ireplace('charset=' . $this->encoding, 'charset=' . $destCharset, $metaTag);
                    break;
                }
                $sp = stripos($data, '<meta ', $ep + 1);
            }
            $data = substr($data, 0, $sp) . $metaTag . substr($data, $ep + 1, strlen($data) - $ep - 1);
            $this->encoding = $destCharset;
            $this->htmlCharset = $destCharset;
            $this->loadHTML($data);
        }
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
                $this->setHtmlVersion(4); // supposed by default
                if (stripos($data, '<!DOCTYPE html>') !== false) {
                    $this->setHtmlVersion(5);
                }
                // Detect meta charset tag
                $head_pos = stripos($data, '<head>');
                $metaTag = '';
                if ($this->getHtmlVersion() < 5) {
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
                    $this->charsetTagExists = false;
                } else {
                    $this->charsetTagExists = true;
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
                $report .= "was not found.\nSet by default to: " . htmlspecialchars($this->docType) . "\n";
            }
            $this->setHtmlVersion($this->docType->getHtmlVersion());
            $report .= "HTML version according to doctype: " . $this->getHtmlVersion() . "\n";
			// Encoding info
            $report .= "\nDocument encoding: initially set to " . $this->htmlCharset . "\n";
			if ($this->charsetTagExists) {
                $report .= "Meta charset tag is found: ";
			} else {
                $report .= "Meta charset tag was not found.\nAuto-generated: ";
            }
			$report .= htmlspecialchars($this->getCharsetTag($this->locateCharsetTag()));
		} else {
            $report .= "\nA new HTML file is handled.\n";
        }
        $report .= '</pre>';
		$this->analysisDone = true;
		if ($reportMode) {
		    echo $report;
        }
	}

    /**
     * Analyze loaded HTML file.
     *
     * @param boolean $reportMode       True, if you'd like to display the analysis in a browser.
     */
	public function analyze2($reportMode = false) {
	    // Collect all necessary data
	    $A = array();
        if (!$this->isNew()) {
            // General info
            $A['isNew'] = false;
            $A['filename'] = $this->srcFileName;
            $A['filesize'] = filesize($this->srcFileName);
            // Doctype
            $A['doctypeExists'] = $this->docType->isExists();
            $A['doctype'] = htmlspecialchars($this->docType);
            $this->setHtmlVersion($this->docType->getHtmlVersion());
            $A['htmlVersion'] = $this->getHtmlVersion();
            // Document encoding
            $A['initialCharset'] = $this->htmlCharset;
            $A['charsetTagExists'] = $this->charsetTagExists;
            $A['charsetTag'] = htmlspecialchars($this->getCharsetTag($this->locateCharsetTag()));
            $A['encoding'] = $this->encoding;
            // Document title
            $A['title'] = $this->getTagContent('title');
            // CSS styles
            $A['stylesInt'] = $this->getTagContent('style');
            $A['stylesExt'] = $this->getExtCSS();
            // Scripts
            $A['scriptsInt'] = $this->getTagContent('script');
            $A['scriptsExt'] = $this->getExtScripts();
            // Headers
            $A['htmlHeaders'] = 'not supported yet';
            // Links
            $A['htmlLinks'] = $this->checkHtmlLinks();
        } else {
            $A['isNew'] = true;
        }
        // Pass the collected data to the analyzer
        $this->analysis = new htmlAnalysis($A);
        if ($reportMode) {
            $this->analysis->display();
        }
        $this->analysisDone = true;
	}

}
