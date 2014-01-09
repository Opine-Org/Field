<?php
namespace Field;

class Select {
	public function render ($field) {
		$buffer = '';
		$field['attributes']['name'] = $field['marker'] . '[' . $field['name'] . ']';
		if (is_callable($field['options'])) {
			$function = $field['options'];
			$field['options'] = $function();
		};
		if (!$this->fieldService->isAssociative($field['options'])) {
			$field['options'] = $this->fieldService->forceAssociative($field['options']);
		}
		if (isset($field['readonly']) && $field['readonly'] == true) {
			$field['attributes']['class'] .= ' input-xlarge uneditable-input ';
			if (isset($field['data']) && !empty($field['data'])) {
				if (isset($field['options'][(string)$field['data']])) {
					$this->fieldService->tag($field, 'span', $field['attributes'], false, $field['options'][(string)$field['data']]);
					$function = self::inputHidden();
					$function($field);
					return;
				}
			}
		}
		$buffer .= $this->fieldService->tag($field, 'select', $field['attributes'], 'open');
		if (isset($field['nullable']) && ($field['nullable'] === true || is_string($field['nullable']) == true)) {
			if ($field['nullable'] === true) {
				$field['nullable'] = '';
			}
			$buffer .= '<option value="">' . $field['nullable'] . '</option>';
		}
		if (is_array($field['options'])) {
			foreach ($field['options'] as $key => $value) {
				$selected = '';
				if (isset($field['data']) && $key == $field['data']) {
					$selected = ' selected="selected" ';
				}
				$buffer .= '<option value="' . $key . '" ' . $selected . '>' . $value . '</option>';
			}
		}
		$buffer .= '</select>';
		return $buffer;
	}
}