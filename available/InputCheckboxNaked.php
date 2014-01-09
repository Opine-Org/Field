<?php
namespace Field;

class InputCheckboxNaked {
	public function render ($field) {
	    $field['attributes']['type'] = 'checkbox';
	    $field['attributes']['name'] = $field['marker'] . '[' . $field['name'] . ']';
	    if (isset($field['options']) && is_array($field['options'])) {
	        foreach ($field['options'] as $option) {
	            if (is_array($option)) {
	                foreach ($option as $key => $value) {
	                    if ($key == 0) {
	                        $field['attributes']['value'] = $value;
	                    } else {
	                        $field['attributes']['value'] = $key;
	                    }
	                    break;
	                }
	            } else {
	                $field['attributes']['value'] = $option;
	            }
	        }
	    }
	    if (!$this->fieldService->isAssociative($field['options'])) {
	        $field['options'] = $this->fieldService->forceAssociative($field['options']);
	    }
	    return $this->fieldService->tag($field, 'input', $field['attributes']);
	}
}