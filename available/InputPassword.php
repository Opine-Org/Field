<?php
namespace Field;

class InputPassword {
    public function render ($field) {
        $field['attributes']['type'] = 'password';
        $field['attributes']['name'] = $field['marker'] . '[' . $field['name'] . ']';
        return $this->fieldService->tag($field, 'input', $field['attributes']);
    }
}