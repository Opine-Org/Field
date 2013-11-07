<?php
namespace Field;

class InputFile {
	public function render ($field) {
		$field['attributes']['type'] = 'file';
		$field['attributes']['name'] = $field['marker'] . '[' . $field['name'] . ']';
		
		echo '
			<div id="', uniqid('file__'), '" class="fileupload_bs fileupload_bs-new fileupload-container" data-provides="fileupload_bs" data-name="', $field['attributes']['name'], '">
				<div class="input-append">
					<div class="uneditable-input span2">
						<i class="icon-file fileupload_bs-exists"></i> 
						<span class="fileupload_bs-preview"></span>
					</div>
					<span class="btn btn-file">
						<span class="fileupload_bs-new">Select file</span>
						<span class="fileupload_bs-exists">Change</span>
						<input class="fileupload" type="file" name="files[]" />
					</span>
					<a href="#" class="btn fileupload_bs-exists" data-dismiss="fileupload">Remove</a>
				</div>
				<div class="progress" style="display: none">
					<div class="bar" style="width: 0%;"></div>
				</div>';
		
		if (isset($field['data']) && is_array($field['data']) && count($field['data']) > 0) {
			if (!isset($field['data']['name']) && isset($field['data']['url'])) {
				$field['data']['name'] = pathinfo($field['data']['url']);
				$field['data']['name'] = $field['data']['name']['filename'];
			}
			foreach ($field['data'] as $key => $value) {
				echo '<input type="hidden" name="', $field['attributes']['name'], '[', $key, ']" value="', $value, '" />';
			}
			echo '<p><a class="img-preview" data-type="', $field['data']['type'], '" data-height="', $field['data']['height'], '" data-width="', $field['data']['width'], '" data-href="', $field['data']['url'], '">', $field['data']['name'], '</a></p>';
		}

		echo '
			</div>';
	}
}