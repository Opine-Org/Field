<?php
namespace Field;

class InputCheckboxesList {
    private $fieldService;

    public function __construct ($fieldService) {
        $this->fieldService = $fieldService;
    }

    public function render ($field, $document, $formObject) {
        $buffer = '';
        $field['attributes']['type'] = 'checkbox';
        $field['attributes']['name'] = $field['marker'] . '[' . $field['name'] . ']';
        $field['options'] = $this->fieldService->options($field, $document, $formObject);
        if (is_array($field['options'])) {
            foreach ($field['options'] as $option) {
                if (is_array($option)) {
                    foreach ($option as $key => $value) {
                        $buffer .= '
                            <label class="checkbox">
                                <input type="checkbox" name="' . $field['attributes']['name'] . '[' . $key . ']" />' . $value .
                            '</label>';
                        break;
                    }
                } else {
                    $buffer .=
                        '<label class="checkbox">
                            <input type="checkbox" name="' . $field['attributes']['name'] . '[' . $option . ']' . '" value="on" />' . $option .
                        '</label>';
                }
            }
        }
        return $buffer;
    }
}