<?php
/**
 * Opine\FieldRoute
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
use RecursiveIteratorIterator;
use RecursiveDirectoryIterator;

class FieldRoute {
    private $root;

    public function __construct ($root) {
        $this->root = $root;
    }

    public function build () {
        $srcDir = $this->root . '/../vendor/opine/field/js';
        $build = require $srcDir . '/build.php';
        $jsFolder = $this->root . '/js/fields';
        $buildFile = $jsFolder . '/fieldBuild.js';
        $out = '';
        if (file_exists($jsFolder)) {
            $this->unlinkFolder($jsFolder);
        }
        mkdir($jsFolder);
        foreach ($build['js'] as $file) {
            $out .= file_get_contents($this->root . '/../vendor/opine/field/js/' . $file) . "\n";
        }
        file_put_contents($buildFile, $out);
        $this->copyFolder($srcDir, $jsFolder);
        unlink($jsFolder . '/build.php');

        $srcDir = $this->root . '/../vendor/opine/field/css';
        $build = require $srcDir . '/build.php';
        $cssFolder = $this->root . '/css/fields';
        $buildFile = $cssFolder . '/fieldBuild.css';
        $out = '';
        if (file_exists($cssFolder)) {
            $this->unlinkFolder($cssFolder);
        }
        mkdir($cssFolder);
        foreach ($build['css'] as $file) {
            $out .= file_get_contents($this->root . '/../vendor/opine/field/css/' . $file) . "\n";
        }
        file_put_contents($buildFile, $out);
        $this->copyFolder($srcDir, $cssFolder);
        unlink($cssFolder . '/build.php');
    }

    private function copyFolder ($source, $dest) {
        foreach ($iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($source, RecursiveDirectoryIterator::SKIP_DOTS), RecursiveIteratorIterator::SELF_FIRST) as $item) {
            if ($item->isDir()) {
                mkdir($dest . DIRECTORY_SEPARATOR . $iterator->getSubPathName());
            } else {
                copy($item, $dest . DIRECTORY_SEPARATOR . $iterator->getSubPathName());
            }
        }
    }

    private function unlinkFolder ($path) {
        if (is_dir($path) === true) {
            $files = array_diff(scandir($path), array('.', '..'));
            foreach ($files as $file) {
                $this->unlinkFolder(realpath($path) . '/' . $file);
            }
            return rmdir($path);
        } else if (is_file($path) === true) {
            return unlink($path);
        }
        return false;
    }
}