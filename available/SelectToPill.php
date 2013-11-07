<?php
namespace Field;

class SelectToPill {
	public function render ($field) {
		$field['attributes']['value'] = [];
		$field['attributes']['key'] = [];
		$field['attributes']['name'] = $field['marker'] . '[' . $field['name'] . ']';				
		if (is_callable($field['options'])) {
			$function = $field['options'];
			$field['options'] = $function();
		};
		if (!$this->fieldService->isAssociative($field['options'])) {
			$field['options'] = $this->fieldService->forceAssociative($field['options']);
		}
		
		if (isset($field['data']) && is_array($field['data'])) {
			$field['attributes']['value'] = [];
			$field['attributes']['key'] = [];
			foreach ($field['data'] as $key) {
				if (isset($field['options'][$key])) {
					$field['attributes']['value'][] = $field['options'][$key];
					$field['attributes']['key'][] = $key;
				}
			}
		}
			
		ob_start();
		$availableCount = 0;
		if (is_array($field['options'])) {
			foreach ($field['options'] as $key => $value) {
				$style = '';
				$used = false;
				if (!in_array($key, $field['attributes']['key'])) {
					$availableCount++;
					$style = ' style="display: block" ';
				} else {
					$style = ' style="display: none" ';
				}
				echo '<li ', $style, ' data-id="', $key, '"><a onclick="selectToPill(this)">', $value, '</a></li>';
			}
		}
		$available = ob_get_clean();
		
		ob_start();
		$chosenCount = count($field['attributes']['value']);
		for ($i=0; $i < $chosenCount; $i++) {
			echo '
				<li style="position: relative" data-id="' . $field['attributes']['key'][$i] . '">
					<a style="cursor: pointer">', $field['attributes']['value'][$i], '
						<span class="ui-icon ui-icon-closethick ui-icon-triangle-1-s"></span>
					</a>
					<input type="hidden" name="', $field['attributes']['name'], '[]" value="', $field['attributes']['key'][$i], '" />
				</li>';
		}
		$chosen = ob_get_clean();
		
		$title = '';
		if (isset($field['tooltip'])) {
			$title = $field['tooltip'];
		}
		echo '
			<div title="', $title, '" class="select-to-pill" data-name="', $field['attributes']['name'], '">
				<div class="dropdown">
					<a class="btn">', $field['label'], '<span class="caret"></span></a>
					<ul class="dropdown-menu" data-count="', $availableCount, '" style="max-height: 200px; overflow: auto">', $available, '</ul>
				</div>
				<ul class="nav nav-pills nav-stacked">', $chosen, '</ul>
			</div>';
	}
}	