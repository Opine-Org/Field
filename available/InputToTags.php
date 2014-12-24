<?php
namespace Field;

class InputToTags
{
    private $fieldService;

    public function __construct($fieldService)
    {
        $this->fieldService = $fieldService;
    }

    public function render($field, $document, $formObject)
    {
        $buffer = '';
        $field['attributes']['class'] = 'selectize-tags';
        $field['attributes']['name'] = $field['marker'].'['.$field['name'].']';
        $field['options'] = $this->fieldService->options($field, $document, $formObject);
        if (isset($field['readonly']) && $field['readonly'] == true) {
            $field['attributes']['class'] .= ' input-xlarge uneditable-input ';
            if (isset($field['data']) && !empty($field['data'])) {
                if (isset($field['options'][(string) $field['data']])) {
                    $this->fieldService->tag($field, 'span', $field['attributes'], false, $field['options'][(string) $field['data']]);
                    $function = self::inputHidden();
                    $function($field);

                    return;
                }
            }
        }
        if (isset($field['multiple']) && $field['multiple'] === true) {
            $field['attributes']['multiple'] = 'multiple';
            $field['attributes']['name'] .= '[]';
            $field['attributes']['data-multiple'] = 1;
        } else {
            $field['attributes']['data-multiple'] = 0;
        }
        if (isset($field['controlled']) && $field['controlled'] === true) {
            $field['attributes']['data-controlled'] = 1;
        } else {
            $field['attributes']['data-controlled'] = 0;
        }

        $buffer .= $this->fieldService->tag($field, 'select', $field['attributes'], 'open');
        if (isset($field['nullable']) && ($field['nullable'] === true || is_string($field['nullable']) == true)) {
            if ($field['nullable'] === true) {
                $field['nullable'] = '';
            }
            $buffer .= '<option value="">'.$field['nullable'].'</option>';
        }
        if (is_array($field['options'])) {
            foreach ($field['options'] as $key => $value) {
                $selected = '';
                if (isset($field['data'])) {
                    if (is_array($field['data'])) {
                        if (in_array((string) $key, $field['data'])) {
                            $selected = ' selected ';
                        }
                    } elseif ((string) $key == $field['data']) {
                        $selected = ' selected ';
                    }
                }
                $buffer .= '<option value="'.$key.'" '.$selected.'>'.$value.'</option>';
            }
        }
        $buffer .= '</select>';

        return $buffer;
    }

    public function csvToArray($data)
    {
        if (is_array($data)) {
            return $data;
        }

        return $this->fieldService->csvToArray($data);
    }
}
