<?php
namespace Field;

class InputHidden {
    public function render ($field) {
        $field['attributes']['type'] = 'hidden';
        $field['attributes']['name'] = $field['marker'] . '[' . $field['name'] . ']';
        return $this->fieldService->tag($field, 'input', $field['attributes']);
    }
}