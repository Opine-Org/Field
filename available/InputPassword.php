<?php
namespace Field;

class InputPassword {
    public function render ($field) {
        $field['attributes']['type'] = 'password';
        $field['attributes']['name'] = $field['marker'] . '[' . $field['name'] . ']';
        if (isset($field['placeholder'])) {
            $field['attributes']['placeholder'] = $field['placeholder'];
        }
        return $this->fieldService->tag($field, 'input', $field['attributes']);
    }
}