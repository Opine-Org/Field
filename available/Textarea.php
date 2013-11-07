<?php
namespace Field;

class Textarea {
	public function render ($field) {
		$field['attributes']['name'] = $field['marker'] . '[' . $field['name'] . ']';
		$data = '';
		if (isset($field['data'])) {
			$data = $field['data'];
		}
		if (isset($field['placeholder'])) {
			$field['attributes']['placeholder'] = $field['placeholder'];
		}
		$this->fieldService->tag($field, 'textarea', $field['attributes'], false, $data);
	}
}