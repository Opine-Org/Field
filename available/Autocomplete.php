<?php
namespace Field;

class Autocomplete {
    public function render ($field) {
        $field['attributes']['name'] = $field['marker'] . '[' . $field['name'] . ']';
        $field['attributes']['class'] = 'custom-autocomplete';
        $field['attributes']['data-single'] = 0;
        if (is_callable($field['options'])) {
            $function = $field['options'];
            $field['options'] = $function();
        }
        if (!$this->fieldService->isAssociative($field['options'])) {
            $field['options'] = $this->fieldService->forceAssociative($field['options']);
        }
        $field['attributes']['id'] = 'id__' . uniqid();
        if (isset($field['data']) && is_array($field['data'])) {
            $field['attributes']['value'] = [];                
            foreach ($field['data'] as $key) {
                if (isset($field['options'][$key])) {
                    $field['attributes']['value'][] = $field['options'][$key];
                }
            }

            $field['attributes']['value'] = $this->fieldService->arrayToCsv($field['attributes']['value']);
        }
        $this->fieldService->tag($field, 'input', $field['attributes']);        
        if (is_array($field['options'])) {
            $tmpJson = [];
            foreach ($field['options'] as $optionKey => $optionValue) {
                $tmpJson[] = array("label" => $optionValue, "value" => $optionKey);
            }
        }
        $stringName = '${' . $field['attributes']['id'] . '}';
        return '<div style="display: none" id="', $field['attributes']['id'], '-autocomplete-data">', $stringName, '</div>';    
    }
}