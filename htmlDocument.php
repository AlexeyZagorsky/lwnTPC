<?php
/**
 * Project: Elwen Text Processing Center (lwnTPC)
 * File: htmlDocument.php
 * Date: 24.05.2016
 *
 * Class htmlDocument
 * Extends DOMDocument and provides various methods to process html data.
 *
 * @copyright 2016 VITIM CS
 * @author Alex Zagorsky
 * @package lnw\TPC\htmlDocument
 * @version 0.1
 */

class htmlDocument extends DOMDocument {

    /** @var string $htmlFileName   Contains processed html file name */
    protected $htmlFileName;

    public function __construct() {
        parent::__construct();
        $this->substituteEntities = true;
    }

    /*
     * Load html data from the file $html_file_name. Overrides the parent DOMDocument::loadHTMLFile method.
     *
     * @param   string  $html_file_name     The path to HTML file.
     * @param   int     $options            Additional libxml parameters. Optional, 0 by default.
     *
     * @return  bool    TRUE on success or FALSE on failure.
     */
    public function loadHTMLFile($html_file_name, $options = 0) {
        if ( file_exists($html_file_name) ) {
            $this->htmlFileName = $html_file_name;
            // use @ to supress possible warnings regarding invalid HTML structure
            if (PHP_VERSION_ID < 50400) {
                $result = parent::loadHTMLFile($html_file_name);
            } else {
                $result = @parent::loadHTMLFile($html_file_name, $options);
            }
        } else {
            $result = false;
        }
        return $result;
    }

    /*
     * Convert HTML data encoding from the current charset to the charset specified in $code_page.
     * Add a meta tag with the actual charset to the HTML data.
     *
     * @param   string  $code_page  The output charset.
     *
     * @return  nothing
     */
    public function convertEncoding($code_page)
    {
        /*echo 'Source Encoding: '.$this->encoding."<br>\n";
        echo 'Dest Encoding: '.$code_page."<br>\n";*/
        $data = iconv($this->encoding, $code_page, $this->saveHTML());
        $data = str_ireplace('charset=' . $this->encoding, 'charset=' . $code_page, $data);
        $this->loadHTML($data);
    }

}

?>

