<?php
namespace Field;

class InputCheckbox {
	public function render ($field) {
		$field['attributes']['type'] = 'checkbox';
		$field['attributes']['name'] = $field['marker'] . '[' . $field['name'] . ']';
		$label = '';
		if (isset($field['options']) && is_array($field['options'])) {
			foreach ($field['options'] as $option) {
				if (is_array($option)) {
					foreach ($option as $key => $value) {
						if ($key == 0) {
							$field['attributes']['value'] = $value;
							$label = $value;
						} else {
							$field['attributes']['value'] = $key;
							$label = $value;
						}
						break;
					}
				} else {
					$field['attributes']['value'] = $option;
					$label = $option;
				}
			}
		}
		
		if (!$this->fieldService->isAssociative($field['options'])) {
			$field['options'] = $this->fieldService->forceAssociative($field['options']);
		}
		
		return '
			<ul class="form-list-chk">
				<li>' . 
		$this->fieldService->tag($field, 'input', $field['attributes']) . '
					<label class="form-lbl">', $label, '</label>
				</li>
			</ul>';
	}
}