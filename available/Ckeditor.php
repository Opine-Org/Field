<?php
namespace Field;

class Ckeditor {
	public function render ($field) {
		$field['attributes']['name'] = $field['marker'] . '[' . $field['name'] . ']';
		$this->fieldService->addClass($field['attributes'], 'redactor');
		if (isset($field['mini']) && $field['mini'] == true) {
			$this->fieldService->addClass($field['attributes'], 'editor-mini');
		}
		if (isset($field['css'])) {
			$field['attributes']['data-css'] = $field['css'];
		}
		if (isset($field['fullpage'])) {
			$field['attributes']['data-fullpage'] = 1;
		}
		$data = '';
		if (isset($field['data'])) {
			$data = $field['data'];
		}
		return $this->fieldService->tag($field, 'textarea', $field['attributes'], false, $data);
	}
}