<?php
namespace Field;

class InputCheckboxes
{
    private $fieldService;

    public function __construct($fieldService)
    {
        $this->fieldService = $fieldService;
    }

    public function render($field, $document, $formObject)
    {
        $buffer = '';
        $field['attributes']['type'] = 'checkbox';
        $field['attributes']['name'] = $field['marker'].'['.$field['name'].']';
        $field['options'] = $this->fieldService->options($field, $document, $formObject);

        if (is_array($field['options'])) {
            $buffer .= '<ul class="form-list-chk">';
            foreach ($field['options'] as $option) {
                if (is_array($option)) {
                    foreach ($option as $key => $value) {
                        $buffer .= '<li><input type="checkbox" name="'.$field['attributes']['name'].'['.$key.']" value="on" /> <label class="form-lbl">'.$value.'</label></li>';
                        break;
                    }
                } else {
                    $buffer .= '<li><input type="checkbox" name="'.$field['attributes']['name'].'['.$option.']" value="on" /> <label class="form-lbl">'.$option.'</label></li>';
                }
            }
            $buffer .= '</ul>';
        }

        return $buffer;
    }
}
