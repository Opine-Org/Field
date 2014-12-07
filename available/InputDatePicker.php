<?php
namespace Field;

class InputDatePicker {
    private $fieldService;

    public function __construct ($fieldService) {
        $this->fieldService = $fieldService;
    }

    public function render ($field) {
        $field['attributes']['class'] = 'datepicker';
        if (isset($field['datetimepicker'])) {
            $this->fieldService->addClass($field['attributes'], 'datetimepicker');
        }
        if (isset($field['timepicker'])) {
            $this->fieldService->addClass($field['attributes'], 'timepicker');
        }
        $field['attributes']['type'] = 'text';
        $field['attributes']['name'] = $field['marker'] . '[' . $field['name'] . ']';
        $field['attributes']['autocomplete'] = 'off';
        $field['attributes']['spellcheck'] = 'false';
        $field['attributes']['value'] = $this->fieldService->defaultValue($field);
        return $this->fieldService->tag($field, 'input', $field['attributes']);
    }
}