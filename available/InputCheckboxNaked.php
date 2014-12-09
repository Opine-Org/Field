<?php
namespace Field;

class InputCheckboxNaked {
    private $fieldService;

    public function __construct ($fieldService) {
        $this->fieldService = $fieldService;
    }

    public function render ($field, $document, $formObject) {
        $field['attributes']['type'] = 'checkbox';
        $field['attributes']['name'] = $field['marker'] . '[' . $field['name'] . ']';
        $field['options'] = $this->fieldService->options($field, $document, $formObject);
        if (isset($field['options']) && is_array($field['options'])) {
            foreach ($field['options'] as $option) {
                if (is_array($option)) {
                    foreach ($option as $key => $value) {
                        if ($key == 0) {
                            $field['attributes']['value'] = $value;
                        } else {
                            $field['attributes']['value'] = $key;
                        }
                        break;
                    }
                } else {
                    $field['attributes']['value'] = $option;
                }
            }
        }
        return $this->fieldService->tag($field, 'input', $field['attributes']);
    }
}