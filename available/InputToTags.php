<?php
namespace Field;

class InputToTags {
	public function render ($field) {
		$field['attributes']['class'] = 'selectize-tags';
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

		$this->fieldService->tag($field, 'select', $field['attributes'], 'open');
		if (isset($field['nullable']) && ($field['nullable'] === true || is_string($field['nullable']) == true)) {
			if ($field['nullable'] === true) {
				$field['nullable'] = '';
			}
			echo '<option value="">', $field['nullable'], '</option>';
		}
		if (is_array($field['options'])) {
			foreach ($field['options'] as $key => $value) {
				$selected = '';
				if (isset($field['data'])) {
					if (is_array($field['data'])) {
						if (in_array((string)$key, $field['data'])) {
							$selected = ' selected ';
						}
					} elseif ((string)$key == $field['data']) {
						$selected = ' selected ';
					}
				}
				echo '<option value="', $key, '" ', $selected, '>', $value, '</option>';
			}
		}
		echo '</select>';
	}
}