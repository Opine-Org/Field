<?php
namespace Field;

class InputFile {
	public function render ($field) {
		$field['attributes']['type'] = 'file';
		$field['attributes']['name'] = $field['marker'] . '[' . $field['name'] . ']';
		
		$uploaded = false;
		$image = '';
		$message = 'Upload';
		if (isset($field['data']) && is_array($field['data']) && count($field['data']) > 0) {
			$message = 'Replace: ' . $field['data']['name'];
			$uploaded == true;
			$type = strtolower($field['data']['type']);
			if (substr_count($type, 'jpeg') || substr_count($type, 'jpg') || substr_count($type, 'png') || substr_count($type, 'gif')) {
				$image = '<a href="' . $field['data']['url'] . '" target="_blank"><img style="z-index: 2" class="ui tiny image" src="' . $field['data']['url'] . '" /></a>';
			} else {
				$image = '<a href="' . $field['data']['url'] . '" target="_blank"><i style="z-index: 2" class="file icon"></i></a>';
			}
		}

		echo '
			<div class="ui segment fileinput-button">',
				$image, '<span>', $message, '</span>
				<div class="manager trash ui icon button"><i class="trash icon"></i></div>
  				<input class="fileupload" type="file" placeholder="Choose..." name="', $field['name'], '">
			</div>
		    <div class="progress">
		        <div class="progress-bar progress-bar-success"></div>
		    </div>';

		if ($uploaded) {
			foreach ($field['data'] as $key => $value) {
				echo '<input type="hidden" name="', $field['attributes']['name'], '[', $key, ']" value="', $value, '" />';
			}
			//echo '<p><a class="img-preview" data-type="', $field['data']['type'], '" data-height="', $field['data']['height'], '" data-width="', $field['data']['width'], '" data-href="', $field['data']['url'], '">', $field['data']['name'], '</a></p>';
		}
	}
}