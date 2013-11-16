<?php
namespace Field;

class Manager {
	public function render ($field) {
		if (!isset($this->document['dbURI'])) {
			throw new \Exception('dbURI not set');
		}
		$buffer = '';
		$url = '%dataAPI%/json-data/' . explode(':', $this->document['dbURI'])[0] . '/byEmbeddedField-' . $this->document['dbURI'] . ':' . $field['name'];
		$this->manager->table($field['manager'], 'Manager/collections/embedded', $buffer, $url);
		echo $buffer;
	}
}