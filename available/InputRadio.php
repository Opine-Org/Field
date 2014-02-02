<?php
namespace Field;

class InputRadio {
    public function render ($field) {
        $buffer = '';
        $field['attributes']['name'] = $field['marker'] . '[' . $field['name'] . ']';
        
        if (is_callable($field['options'])) {
            $function = $field['options'];
            $field['options'] = $function();
        }

        if (!$this->fieldService->isAssociative($field['options'])) {
            $field['options'] = $this->fieldService->forceAssociative($field['options']);
        }
        
        if (is_array($field['options'])) {
            $buffer .= '<ul class="form-list-rdo">';
            foreach ($field['options'] as $optionKey => $option) {
                if (is_array($option)) {
                    foreach ($option as $key => $value) {
                        $buffer .= '<li><input type="radio" name="' . $field['attributes']['name'] . '" value="' . $key . '" /> <label class="form-lbl">' . $value . '</label></li>';
                        break;
                    }
                } else {
                    $buffer .= '<li><input type="radio" name="' . $field['attributes']['name'] . '" value="' . $optionKey . '" /> <label class="form-lbl">' . $option . '</label></li>';
                }
            }
            $buffer .= '</ul>';
        }
        return $buffer;
    }
}