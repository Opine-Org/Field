<?php
namespace Field;

class InputDatePicker {
	public function render ($field) {
		$field['attributes']['class'] = 'datepicker';
		if (isset($field['datetimepicker'])) {
			$this->fieldService->addClass($field['attributes'], 'datetimepicker');
		}
		if (isset($field['timepicker'])) {
			$this->fieldService->addClass($field['attributes'], 'timepicker');
		}
		$field['attributes']['type'] = 'text';
		$field['attributes']['name'] = $field['marker'] . '[' . $field['name'] . ']';
		$field['attributes']['style'] = 'text-indent: 28px; line-height: 19px';
		$field['attributes']['autocomplete'] = 'off';
		$field['attributes']['spellcheck'] = 'false';
		$calendarLeft = 10;
		if (isset($field['calendarLeft'])) {
			$calendarLeft = $field['calendarLeft'];
		}
		
		echo '
			<div style="position: relative;">
				<i class="icon-calendar" style="position: absolute; left: ', $calendarLeft, 'px; top: 7px; opacity: .3"></i>',
				$this->fieldService->tag($field, 'input', $field['attributes']), 
			'</div>';	
	}
}