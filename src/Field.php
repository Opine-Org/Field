<?php
/**
 * Opine\Field
 *
 * Copyright (c)2013, 2014 Ryan Mahoney, https://github.com/Opine-Org <ryan@virtuecenter.com>
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 * 
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 * 
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 */
namespace Opine;

class Field {
    private $db;
    private $fieldContainer = [];
    private $root;
    private $manager;
    
    public function __construct ($root, $db, $manager) {
        $this->db = $db;
        $this->root = $root;
        $this->manager = $manager;
    }

    public function defaultValue (&$field) {
        $default = '';
        if (isset($field['data']) && !empty($field['data'])) {
            $default = $field['data'];
        } else {
            if (!empty($field['default'])) {
                if (is_callable($field['default'])) {
                    $function = $field['default'];
                    $default = $function($field);
                }   else {
                    $default = $field['default'];
                }
            }
        }
        return $default;
    }

    public function render ($type, $metadata, $document) {
        if (!isset($this->fieldContainer[$type])) {
            if (substr_count($type, '\\') > 0) {
                $className = $type;
                $path = $this->root . '/../bundles/' . ltrim(str_replace(['\\', '/Field/'], ['/', '/fields/'], $type), '/') . '.php';
            } else {
                $className = 'Field\\' . $type;
                $path = $this->root . '/../fields/' . $type . '.php';
                if (!file_exists($path)) {
                    $path = __DIR__ . '/../available/' . $type . '.php';
                }
            }
            if (!file_exists($path)) {
                throw new \Exception('Unknown field type: ' . $type);
            }
            require_once $path;
            $instance = new $className($this);
            $instance->db = $this->db;
            $instance->fieldService = $this;
            $instance->manager = $this->manager;
            $instance->document = $document;
            $this->fieldContainer[$type] = $instance;
        }
        $instance = $this->fieldContainer[$type];
        if (!isset($metadata['attributes'])) {
            $metadata['attributes'] = [];
        }
        return $instance->render($metadata);
    }

    public function isAssociative (&$array) {
        if (!is_array($array)) {
            return false;
        }
        if (array_values($array) === $array) {
            return false;
        }
        return true;
    }

    public function arrayToCsv (array $array) {
        foreach ($array as &$value) {
            if (substr_count($value, ',') > 0) {
                $value = '"' . str_replace('"', '\"', $value) . '"';
            }
        }
        return htmlentities(implode(', ', $array));
    }
    
    public function forceAssociative (&$array) {
        if (!is_array($array)) {
            return [];
        }
        $newArray = [];
        foreach ($array as $value) {
            $newArray[(string)$value] = $value;
        }
        return $newArray;
    }

    public function tag (&$field, $tag, $attributes=[], $closed=true, $data='') {
        if (isset($attributes['name']) && substr_count($attributes['name'], '-') > 0) {
            $tmp = explode('[', substr($attributes['name'], 0, -1), 2);
            $marker = $tmp[0];
            $name = $tmp[1];
            $name = explode('-', $name);
            $attributes['name'] = $marker;
            foreach ($name as $namePart) {
                $attributes['name'] .= '[' . $namePart . ']';
            }
        }
        
        $buffer = '';
        $buffer .= '<' . $tag . ' ';
        foreach ($attributes as $attribute => $value) {
            $buffer .= ' ' . $attribute . '="' . $value . '" ';
        }
        if ($closed === true) {
            $buffer .= ' />';
        } elseif ($closed === false) {
            $buffer .= '>' . $data . '</' . $tag . '>';
        } else {
            $buffer .= '>';
        }
        return $buffer;
    }

    public function addClass (Array &$attributes, $class) {
        if (isset($attributes['class'])) {
            $attributes['class'] .= ' ' . $class;
        } else {
            $attributes['class'] = $class;
        }
    }

    public static function csvToArray ($subject) {
        if ($subject == '') {
            return array();
        }
        if (substr_count($subject, ',') < 1) {
            return array($subject);
        }
        $array = preg_split('/,(?=(?:[^\"]*\"[^\"]*\")*(?![^\"]*\"))/', $subject, -1, PREG_SPLIT_DELIM_CAPTURE);
        foreach ($array as &$value) {
            $value = trim($value, "\" \t\n");
        }
        return $array;
    }
}