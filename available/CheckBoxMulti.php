<?php
namespace Field;

class CheckBoxMulti {
    public function render ($field) {
        $buffer = '';
        $field['attributes']['name'] = $field['marker'] . '[' . $field['name'] . '][]';
        $field['attributes']['multiple'] = 'multiple';
        $field['attributes']['class'] = (empty($field['attributes']['class'])) ? 'multiSelectCheckbox' : $field['attributes']['class'] . ' multiSelectCheckbox'; 
        if (is_callable($field['options'])) {
            $function = $field['options'];
            $field['options'] = $function();
        };
        
        $this->fieldService->tag($field, 'select', $field['attributes'], 'open');
        if (isset($field['nullable']) && ($field['nullable'] === true || is_string($field['nullable']) == true)) {
            if ($field['nullable'] === true) {
                $field['nullable'] = '';
            }
            $buffer .= '<option value="">' . $field['nullable'] . '</option>';
        }
        
        if (is_array($field['options'])) {
            foreach ($field['options'] as $key => $value) {
                if (!is_array($value)) {
                    $selected = (!empty($field['data']) && in_array($key, $field['data'])) ? 'selected="selected"' : null;
                    $buffer .= '<option value="' . $key . '" ' . $selected .'>' . $value . '</option>';
                } else {
                    $buffer .= '<optgroup label="' . $value['label'] . '">';
                        foreach ($value['options'] as $k => $v) {
                            $selected = (!empty($field['data']) && in_array($k, $field['data'])) ? 'selected="selected"' : null;
                            $buffer .= '<option value="' . $k . '" ' . $selected . '>' . $v . '</option>';
                        }
                    $buffer .= '</optgroup>';
                }
            }
        }
        $buffer .= '</select>
        <script type="text/javascript">
            $(function () {
                 $(".multiSelectCheckbox").multiselect(); 
            });
        </script>';
        return $buffer;
    }
}