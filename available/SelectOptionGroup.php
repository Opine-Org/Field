<?php
namespace Field;

class SelectOptionGroup {
	public function render ($field) {
        $field['attributes']['name'] = $field['marker'] . '[' . $field['name'] . ']';
        if (is_callable($field['options'])) {
            $function = $field['options'];
            $field['options'] = $function();
        };

        $this->fieldService->tag($field, 'select', $field['attributes'], 'open');
        if (isset($field['nullable']) && ($field['nullable'] === true || is_string($field['nullable']) == true)) {
            if ($field['nullable'] === true) {
                $field['nullable'] = '';
            }
            echo '<option value="">', $field['nullable'], '</option>';
        }
        foreach ($field['options'] as $optionGroup) {
            echo '
                <optgroup label="', $optionGroup['label'], '">';

            if (isset($optionGroup['options']) && is_array($optionGroup['options']) && count($optionGroup['options']) > 0) {
                foreach ($optionGroup['options'] as $optionKey => $optionValue) {
                    echo '
                        <option value="', $optionKey, '">', $optionValue, '</option>';
                }
            }
            echo '
                </optgroup>';
        }
        echo '
                </select>';
    }
}