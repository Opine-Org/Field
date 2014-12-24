<?php
namespace Field;

class InputRadioList
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
            foreach ($field['options'] as $optionKey => $option) {
                if (is_array($option)) {
                    foreach ($option as $key => $value) {
                        $buffer .= '
                        <label class="radio">
                            <input type="radio" value="'.$key.'" name="'.$field['attributes']['name'].'" />'.$value.
                        '</label>';
                        break;
                    }
                } else {
                    $buffer .=
                    '<label class="radio">
                        <input type="radio" value="'.$optionKey.'" name="'.$field['attributes']['name'].'" />'.$option.
                    '</label>';
                }
            }
        }

        return $buffer;
    }
}
