<?php
namespace Field;

class InputCheckboxesList {
	public function render ($field) {
		$field['attributes']['type'] = 'checkbox';
		$field['attributes']['name'] = $field['marker'] . '[' . $field['name'] . ']';	
		if (is_callable($field['options'])) {
			$function = $field['options'];
			$field['options'] = $function();
		}
			
		if (is_array($field['options'])) {
			foreach ($field['options'] as $option) {
				if (is_array($option)) {
					foreach ($option as $key => $value) {
						echo '
							<label class="checkbox">
								<input type="checkbox" name="', $field['attributes']['name'], '[', $key, ']', '" />', $value, 
							'</label>';							
						break;
					}
				} else {
					echo 
						'<label class="checkbox">
							<input type="checkbox" name="', $field['attributes']['name'], '[', $option, ']', '" value="on" />', $option,
						'</label>';
				}
			}
		}
	}
}