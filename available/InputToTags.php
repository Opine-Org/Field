<?php
namespace Field;

class InputToTags {
	public function render ($field) {
		$field['attributes']['type'] = 'text';
		$field['attributes']['name'] = $field['marker'] . '[' . $field['name'] . ']';
		$field['attributes']['id'] = 'tags_' . uniqid();
		$field['attributes']['class'] = 'input-to-tags';
		if (isset($field['data']) && is_array($field['data'])) {
			$field['attributes']['value'] = htmlentities(implode(',', $field['data']));
		}
		$this->fieldService->tag($field, 'input', $field['attributes']);
		
		$autocomplete = '';
		if (isset($field['autocomplete'])) {
			$autocomplete = ',autocomplete_url: "/admin/autocomplete/?marker=' . str_replace('\\', '_', $field['__CLASS__']) . '-' . $field['name'] . '"';
		}

		echo '<script>
			$("#', $field['attributes']['id'], '").tagsInput({
				width: "auto", height: "auto"',
				$autocomplete, '
			});
		</script>';
	}
}