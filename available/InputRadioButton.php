<?php
namespace Field;

class InputRadioButton {
	public function render ($field) {
		$field['attributes']['name'] = $field['marker'] . '[' . $field['name'] . ']';


/*
		if (is_callable($field['options'])) {
			$function = $field['options'];
			$field['options'] = $function();
		}

		if (!$this->fieldService->isAssociative($field['options'])) {
			$field['options'] = $this->fieldService->forceAssociative($field['options']);
		}

		if (is_array($field['options'])) {
			echo '<div class="radioset-make ', (($field['other'] == true) ? 'radioset-other' : ''), '">';
			foreach ($field['options'] as $optionKey => $option) {
				if (is_array($option)) {
					foreach ($option as $key => $value) {
						$id = 'radio_' . uniqid();
						echo '<input type="radio" id="', $id, '" name="', $field['attributes']['name'], '" value="', $key, '" /><label for="', $id, '">', $value, '</label>';
						break;
					}
				} else {
					$id = 'radio_' . uniqid();
					echo '<input type="radio" id="', $id, '" name="', $field['attributes']['name'], '" value="', $optionKey, '" /><label for="', $id, '">', $option, '</label>';
				}
			}
	        if ($field['other'] == true) {
	            $id = 'radio_' . uniqid();
	            echo '
	                <input type="radio" id="', $id, '" name="', $field['attributes']['name'], '" value="vc-other" /><label for="', $id, '">Other</label>
				    <div style="', $field['other-style'], '" class="control-group vc-other"><label class="control-label">Other Amount: </label><div class="controls"><input data-id="', $field['name'], '-other" type="text" name="', str_replace(']', '-other]', $field['attributes']['name']), '" maxlength="', $field['other-maxlength'], '" /></div></div>';
	        }
			echo '</div>';
		}
*/
	}
}