<?php
namespace Field;

class InputHidden {
    public function render ($field) {
        $field['attributes']['type'] = 'hidden';
        $field['attributes']['name'] = $field['marker'] . '[' . $field['name'] . ']';
        if (isset($field['data'])) {
	        $field['attributes']['value'] = $field['data'];
	    }
        return $this->fieldService->tag($field, 'input', $field['attributes']);
    }
}