<?php
namespace Field;

class Select {
    private $fieldService;

    public function __construct ($fieldService) {
        $this->fieldService = $fieldService;
    }

    public function render ($field, $document, $formObject) {
        $buffer = '';
        $field['attributes']['name'] = $field['marker'] . '[' . $field['name'] . ']';
        $field['options'] = $this->fieldService->options($field, $document, $formObject);
        if (isset($field['readonly']) && $field['readonly'] == true) {
            $field['attributes']['class'] .= ' input-xlarge uneditable-input ';
            if (isset($field['data']) && !empty($field['data'])) {
                if (isset($field['options'][(string)$field['data']])) {
                    $this->fieldService->tag($field, 'span', $field['attributes'], false, $field['options'][(string)$field['data']]);
                    $function = self::inputHidden();
                    $function($field);
                    return;
                }
            }
        }
        $buffer .= $this->fieldService->tag($field, 'select', $field['attributes'], 'open');
        if (isset($field['nullable']) && ($field['nullable'] === true || is_string($field['nullable']) == true)) {
            if ($field['nullable'] === true) {
                $field['nullable'] = '';
            }
            $buffer .= '<option value="">' . $field['nullable'] . '</option>';
        }
        if (is_array($field['options'])) {
            foreach ($field['options'] as $key => $value) {
                $selected = '';
                if (isset($field['data']) && $key == $field['data']) {
                    $selected = ' selected="selected" ';
                }
                $buffer .= '<option value="' . $key . '" ' . $selected . '>' . $value . '</option>';
            }
        }
        $buffer .= '</select>';
        return $buffer;
    }
}