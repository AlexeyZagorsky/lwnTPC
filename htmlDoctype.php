<?php
/**
 * Project: Elwen Text Processing Center (lwnTPC)
 * File: htmlDoctype.php
 * Date: 12.06.2017
 *
 * Class htmlDoctype
 * Extends DOMDocumentType and provides additional functionality to process HTML doctypes.
 * This class is a Decorator to DOMDocumentType.
 *
 * @copyright 2017 VITIM CS
 * @author Alex Zagorsky
 * @package lnw\TPC\htmlDoctype
 * @version 0.2
 */

class htmlDoctype extends DOMDocumentType {

    /** @var object     DOMDocumentType object */
    protected $class;
    /** @var string     HTML file name */
    private $htmlFileName;

    /**
     * htmlDoctype constructor.
     *
     * @param   object  $class      DOMDocumentType object to decorate
     * @param   string  $fileName   HTML file name
     */
    public function __construct($class, $fileName) {
        $this->class = $class;
        $this->htmlFileName = $fileName;
    }

    /**
     * Getter. Intercepts all attempts to read $class properties.
     *
     * @param   string  $name       Property name
     * @return  mixed               Property value
     */
    public function __get($name) {
        return $this->class->{$name};
    }

    /**
     * Setter. Intercepts all attempts to write to $class properties.
     *
     * @param   string  $name       Property name
     * @return  void
     */
    public function __set($name, $value) {
        $this->class->{$name} = $value;
    }

    /**
     * Intercept all calls of $class methods.
     *
     * @param   string  $method     Method name
     * @param   array   $args       Method arguments
     * @return  mixed               Method result
     */
    public function __call($method, $args = []) {
        return call_user_func_array([$this->class, $method], $args);
    }

    /**
     * Return the HTML document doctype tag as a string.
     *
     * @return string       HTML doctype tag or empty string if there is no doctype tag in the file
     */
    public function __toString() {
        $strDoctype = '';
        if ($this->class) {
            $strDoctype = '<!DOCTYPE ' . $this->class->name;
            $strDoctype .= $this->class->publicId ? ' PUBLIC "' . $this->class->publicId . '"' : '';
            $strDoctype .= $this->class->systemId ? ' "' . $this->class->systemId . '"' : '';
            $strDoctype .= '>';
        }
        return $strDoctype;
    }

    /**
     * Check if there is a doctype tag in the HTML file.
     *
     * @return	bool        True if a doctype tag found, otherwise false
     */
    public function isExists() {
        $result = false;
        $bufferSize = 4096;
        $offset = 0;
        while ($data = file_get_contents($this->htmlFileName, false, null, $offset, $bufferSize)) {
            if (stripos($data, '<!DOCTYPE html') !== false) {
                $result = true;
                break;
            }
            $offset += $bufferSize;
        }
        return $result;
    }

    /**
     * Return the HTML document version specified in the doctype.
     *
     * @return int      HTML version (4 or 5) or 0 if the version was not detected
     */
    public function getHtmlVersion() {
        $result = 0;
        if ($this->class->publicId) {
            $p = strpos($this->class->publicId, 'DTD HTML ');
            if ($p) {
                $sp = strpos($this->class->publicId, ' ', $p + 10);
                $v = substr($this->class->publicId, $p + 9, $sp - $p - 9);
                $result = (int)floor($v);
            }
        } elseif ($this->class->name == 'html') {
            $result = 5;
        }
        return $result;
    }
}
