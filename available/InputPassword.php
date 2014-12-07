<?php
namespace Field;

class InputPassword {
    private $fieldService;

    public function __construct ($fieldService) {
        $this->fieldService = $fieldService;
    }

    public function render ($field, $document, $formObject) {
        $field['attributes']['type'] = 'password';
        $field['attributes']['name'] = $field['marker'] . '[' . $field['name'] . ']';
        if (isset($field['placeholder'])) {
            $field['attributes']['placeholder'] = $field['placeholder'];
        }
        return $this->fieldService->tag($field, 'input', $field['attributes']);
    }
}