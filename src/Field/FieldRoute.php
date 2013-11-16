<?php
namespace Field;

class FieldRoute {
	public function build ($root) {
		$srcDir = $root . '/../vendor/virtuecenter/field/js';
		$build = require $srcDir . '/build.php';
		$jsFolder = $root . '/js/fields';
		$buildFile = $jsFolder . '/fieldBuild.js';
		$out = '';
		if (file_exists($jsFolder)) {
			$this->unlinkFolder($jsFolder);
		}
		mkdir($jsFolder);
		foreach ($build['js'] as $file) {
			$out .= file_get_contents($root . '/../vendor/virtuecenter/field/js/' . $file) . "\n";
		}
		file_put_contents($buildFile, $out);
		$this->copyFolder($srcDir, $jsFolder);
		unlink($jsFolder . '/build.php');
	}

	private function copyFolder ($source, $dest) {
		foreach ($iterator = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($source, \RecursiveDirectoryIterator::SKIP_DOTS), \RecursiveIteratorIterator::SELF_FIRST) as $item) {
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