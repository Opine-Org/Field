<?php
/**
 * Opine\Field\Service
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
namespace Opine\Field;


class Service
{
    private $db;
    private $fieldContainer = [];
    private $root;
    private $route;

    public function __construct($root, $db, $route)
    {
        $this->root = $root;
        $this->db = $db;
        $this->route = $route;
    }

    public function defaultValue(&$field)
    {
        $default = '';
        if (isset($field['data']) && !empty($field['data'])) {
            $default = $field['data'];
        } else {
            if (!empty($field['default'])) {
                if (substr_count($field['default'], '@') == 1 && substr_count($field['default'], '\@') == 0) {
                    $default = $this->route->serviceMethod($field['default'], $field);
                } else {
                    $default = $field['default'];
                }
            }
        }

        return $default;
    }

    public function options($field, $document, $formObject)
    {
        if (!isset($field['options'])) {
            return [];
        }
        if (is_string($field['options']) && substr_count($field['options'], '@') == 1) {
            return $this->route->serviceMethod($field['options'], $field, $document, $formObject);
        }
        if (!is_array($field['options'])) {
            return [];
        }
        $criteria = [];
        if (isset($field['options']['criteria'])) {
            $criteria = $field['options']['criteria'];
        }
        $sort = [];
        if (isset($field['options']['sort'])) {
            $sort = $field['options']['sort'];
        }
        $key = '_id';
        if (isset($field['options']['key'])) {
            $key = $field['options']['key'];
        }
        $label = 'title';
        if (isset($field['options']['label'])) {
            $label = $field['options']['label'];
        }
        switch ($field['options']['type']) {
            case 'array':
                $options = $field['options']['value'];
                break;

            case 'url':
                $options = file_get_contents($field['options']['url']);
                break;

            case 'query':
                $options = $this->db->fetchAllGrouped(
                    $this->db->collection($field['options']['collection'])->
                        find($criteria)->
                        sort($sort),
                    $key,
                    $label);
                break;

            case 'queryDistinct':
                $options = $this->db->distinct($field['options']['collection'], $field['options']['field']);
                if (empty($options)) {
                    $options = [];
                }
                if (isset($field['options']['value'])) {
                    $options = array_unique(array_merge($field['options']['value'], $options));
                }
                sort($options);
                break;

            default:
                return [];
        }
        if (!$this->isAssociative($options)) {
            return $this->forceAssociative($options);
        }

        return $options;
    }

    public function isAssociative(&$array)
    {
        if (!is_array($array)) {
            return false;
        }
        if (array_values($array) === $array) {
            return false;
        }

        return true;
    }

    public function arrayToCsv(array $array)
    {
        foreach ($array as &$value) {
            if (substr_count($value, ',') > 0) {
                $value = '"'.str_replace('"', '\"', $value).'"';
            }
        }

        return htmlentities(implode(', ', $array));
    }

    public function forceAssociative(&$array)
    {
        if (!is_array($array)) {
            return [];
        }
        $newArray = [];
        foreach ($array as $value) {
            $newArray[(string) $value] = $value;
        }

        return $newArray;
    }

    public function tag(&$field, $tag, $attributes = [], $closed = true, $data = '')
    {
        if (isset($attributes['name']) && substr_count($attributes['name'], '-') > 0) {
            $tmp = explode('[', substr($attributes['name'], 0, -1), 2);
            $marker = $tmp[0];
            $name = $tmp[1];
            $name = explode('-', $name);
            $attributes['name'] = $marker;
            foreach ($name as $namePart) {
                $attributes['name'] .= '['.$namePart.']';
            }
        }

        $buffer = '';
        $buffer .= '<'.$tag;
        foreach ($attributes as $attribute => $value) {
            if (is_array($attribute) || is_array($value)) {
                continue;
            }
            $buffer .= ' '.$attribute.'="'.$value.'"';
        }
        if ($closed === true) {
            $buffer .= ' />';
        } elseif ($closed === false) {
            $buffer .= '>'.$data.'</'.$tag.'>';
        } else {
            $buffer .= '>';
        }

        return $buffer;
    }

    public function addClass(Array &$attributes, $class)
    {
        if (isset($attributes['class'])) {
            $attributes['class'] .= ' '.$class;
        } else {
            $attributes['class'] = $class;
        }
    }

    public static function csvToArray($subject)
    {
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
