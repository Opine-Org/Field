<?php
namespace Field;

class Textarea {
    private $fieldService;

    public function __construct ($fieldService) {
        $this->fieldService = $fieldService;
    }

    public function render ($field, $document, $formObject) {
        $field['attributes']['name'] = $field['marker'] . '[' . $field['name'] . ']';
        $data = '';
        if (isset($field['data'])) {
            $data = $field['data'];
        }
        if (isset($field['placeholder'])) {
            $field['attributes']['placeholder'] = $field['placeholder'];
        }
        return $this->fieldService->tag($field, 'textarea', $field['attributes'], false, $data);
    }
}