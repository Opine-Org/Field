<?php
namespace Field;

class FieldRoute {
	public function build ($root) {
		$build = require $root . '/../vendor/virtuecenter/field/js/build.php';
		print_r($build);
	}
}