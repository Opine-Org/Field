<?php
namespace Field;

class AutocompleteSingle {
	public function render ($field) {
		$field['attributes']['name'] = $field['marker'] . '[' . $field['name'] . ']';
		$tempName = $field['marker'] . '[' . $field['name'] . '_temp]';
		$field['attributes']['class'] .= ' custom-autocomplete';
		$field['attributes']['data-single'] = 1;
			
		if (is_callable($field['options'])) {
			$function = $field['options'];
			$field['options'] = $function();
		}
			
		if (!$this->fieldService->isAssociative($field['options'])) {
			$field['options'] = $this->fieldService->forceAssociative($field['options']);
		}
			
		$field['attributes']['id'] = uniqid('id__');
		if (isset($field['data'])) {
			if (is_array($field['data'])) {
				$field['attributes']['value'] = [];
				foreach ($field['data'] as $key) {
					if (isset($field['options'][$key])) {
						$field['attributes']['value'][] = $field['options'][$key];
					}
				}

				$field['attributes']['value'] = $this->fieldService->arrayToCsv($field['attributes']['value']);
			} else {
				if (isset($field['options'][(string)$field['data']])) {
					$field['attributes']['value'] = $field['options'][(string)$field['data']];
				}
			}
		}

		$this->fieldService->tag($field, 'input', array_merge($field['attributes'], ['name' => $tempName]));
		$this->fieldService->tag($field, 'input', array_merge($field['attributes'], ['type' => 'hidden', 'class' => $field['attributes']['id'], 'id' => uniqid('id__')]));
			
		if (is_array($field['options'])) {
			$tmpJson = [];
			foreach ($field['options'] as $optionKey => $optionValue) {
				$tmpJson[] = array("label" => $optionValue, "value" => $optionKey);
			}
		}
			
		$stringName = '${' . $field['attributes']['id'] . '}';
		echo '<div style="display: none" id="', $field['attributes']['id'], '-autocomplete-data">', $stringName, '</div>';
	}
}