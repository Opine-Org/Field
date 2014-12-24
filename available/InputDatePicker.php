<?php
namespace Field;

use MongoDate;

class InputDatePicker
{
    private $fieldService;

    public function __construct($fieldService)
    {
        $this->fieldService = $fieldService;
    }

    public function render($field, $document, $formObject)
    {
        $field['attributes']['class'] = 'datepicker';
        if (isset($field['datetimepicker'])) {
            $this->fieldService->addClass($field['attributes'], 'datetimepicker');
        }
        if (isset($field['timepicker'])) {
            $this->fieldService->addClass($field['attributes'], 'timepicker');
        }
        $field['attributes']['type'] = 'text';
        $field['attributes']['name'] = $field['marker'].'['.$field['name'].']';
        $field['attributes']['autocomplete'] = 'off';
        $field['attributes']['spellcheck'] = 'false';
        $field['attributes']['value'] = $this->fieldService->defaultValue($field);

        return $this->fieldService->tag($field, 'input', $field['attributes']);
    }

    public function stringToObject($data)
    {
        if (empty($data)) {
            return;
        }

        return new MongoDate(strtotime($data));
    }

    public function objectToString($data)
    {
        if (is_object($data) && get_class($data) === 'MongoDate') {
            return date('m/d/Y', $data->sec);
        }
        if (is_string($data)) {
            return $data;
        }

        return;
    }

    public function nowString()
    {
        return date('m/d/Y');
    }
}
