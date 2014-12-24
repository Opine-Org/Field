<?php
namespace Field;

class InputRadio
{
    private $fieldService;

    public function __construct($fieldService)
    {
        $this->fieldService = $fieldService;
    }

    public function render($field, $document, $formObject)
    {
        $buffer = '';
        $field['attributes']['name'] = $field['marker'].'['.$field['name'].']';
        $field['options'] = $this->fieldService->options($field, $document, $formObject);
        if (!$this->fieldService->isAssociative($field['options'])) {
            $field['options'] = $this->fieldService->forceAssociative($field['options']);
        }
        if (is_array($field['options'])) {
            $buffer .= '<ul class="form-list-rdo">';
            foreach ($field['options'] as $optionKey => $option) {
                if (is_array($option)) {
                    foreach ($option as $key => $value) {
                        $buffer .= '<li><input type="radio" name="'.$field['attributes']['name'].'" value="'.$key.'" /> <label class="form-lbl">'.$value.'</label></li>';
                        break;
                    }
                } else {
                    $buffer .= '<li><input type="radio" name="'.$field['attributes']['name'].'" value="'.$optionKey.'" /> <label class="form-lbl">'.$option.'</label></li>';
                }
            }
            $buffer .= '</ul>';
        }

        return $buffer;
    }
}
