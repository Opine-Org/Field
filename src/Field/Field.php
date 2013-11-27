<?php
namespace Field;

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
				}	else {
					$default = $field['default'];
				}
			}
		}
		return $default;
	}

	public function render ($type, $metadata, $document) {
		if (!isset($this->fieldContainer[$type])) {
			$path = $this->root . '/../fields/' . $type . '.php';
			if (!file_exists($path)) {
				$path = __DIR__ . '/../../available/' . $type . '.php';
			}
			if (!file_exists($path)) {
				throw new \Exception('Unknown field type: ' . $type);
			}
			require_once $path;
			$className = 'Field\\' . $type;
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
		ob_start();
		$instance->render($metadata);
		return ob_get_clean();
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
		
		echo '<', $tag, ' ';
		foreach ($attributes as $attribute => $value) {
			echo ' ', $attribute, '="', $value, '" ';
		}
		if ($closed === true) {
			echo ' />';
		} elseif ($closed === false) {
			echo '>' . $data . '</' . $tag . '>';
		} else {
			echo '>';
		}
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