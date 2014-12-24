<?php
namespace Field;

class InputCodename
{
    private $fieldService;

    public function __construct($fieldService)
    {
        $this->fieldService = $fieldService;
    }

    public function render($field, $document, $formObject)
    {
        $field['attributes']['type'] = 'hidden';
        $field['attributes']['name'] = $field['marker'].'['.$field['name'].']';
        if (isset($field['path'])) {
            $field['attributes']['data-path'] = $field['path'];
            if (!isset($field['attributes']['class'])) {
                $field['attributes']['class'] = '';
            }
            $field['attributes']['class'] .= ' vcms-permalink';
            $field['attributes']['class'] = trim($field['attributes']['class']);
        }
        if (isset($field['selector'])) {
            $field['attributes']['data-selector'] = $field['selector'];
        }
        if (isset($field['mode'])) {
            $field['attributes']['data-mode'] = $field['mode'];
        }

        return $this->fieldService->tag($field, 'input', $field['attributes']);
    }
}
