<?php
namespace Field;

class InputHidden {
    private $fieldService;

    public function __construct ($fieldService) {
        $this->fieldService = $fieldService;
    }

    public function render ($field) {
        $field['attributes']['type'] = 'hidden';
        $field['attributes']['name'] = $field['marker'] . '[' . $field['name'] . ']';
        if (isset($field['data'])) {
	        $field['attributes']['value'] = $field['data'];
	    }
        return $this->fieldService->tag($field, 'input', $field['attributes']);
    }
}