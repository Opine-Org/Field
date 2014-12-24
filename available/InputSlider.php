<?php
namespace Field;

class InputSlider
{
    private $fieldService;

    public function __construct($fieldService)
    {
        $this->fieldService = $fieldService;
    }

    public function render($field, $document, $formObject)
    {
        $name = $field['marker'].'['.$field['name'].']';
        $label = '';
        if (!empty($field['label'])) {
            $label = '<label>'.$field['label'].'</label>';
        }
        $default = '';
        if (isset($field['data']) && !empty($field['data'])) {
            $default = $field['data'];
        } else {
            if (!empty($field['default'])) {
                $default = $field['default'];
            }
        }

        return '
            <div class="ui slider checkbox"><input type="checkbox" name="'.$name.'" value="'.$default.'" />'.$label.'</div>';
    }
}
