<?php
namespace Field;

class InputFile
{
    private $fieldService;

    public function __construct($fieldService)
    {
        $this->fieldService = $fieldService;
    }

    public function render($field, $document, $formObject)
    {
        $buffer = '';
        $field['attributes']['name'] = $field['marker'].'['.$field['name'].']';

        $uploaded = false;
        $image = '<a><i style="z-index: 2; opacity: .2" class="add sign box icon"></i></a>';
        $message = 'Click to Upload, or Drag and Drop';
        if (isset($field['data']) && is_array($field['data']) && count($field['data']) > 0) {
            $message = $field['data']['name'];
            $uploaded = true;
            $type = strtolower($field['data']['type']);
            if (substr_count($type, 'jpeg') || substr_count($type, 'jpg') || substr_count($type, 'png') || substr_count($type, 'gif')) {
                $image = '<a href="'.$field['data']['url'].'" target="_blank"><img style="z-index: 2" class="ui mini image" src="'.$field['data']['url'].'" /></a>';
            } else {
                $image = '<a href="'.$field['data']['url'].'" target="_blank"><i style="z-index: 2" class="file icon"></i></a>';
            }
        }

        $buffer .= '
            <div class="ui segment fileinput-button '.($uploaded === true ? 'uploaded' : '').'">'.
                $image.'<span>'.$message.'</span>
                <div class="manager trash ui icon"><i class="trash icon"></i></div>
                  <input class="fileupload" title="Click to Upload, or Drag and Drop" type="file" placeholder="Choose..." name="'.$field['name'].'">
            </div>
            <div class="progress">
                <div class="progress-bar progress-bar-success"></div>
            </div>';

        if ($uploaded) {
            foreach ($field['data'] as $key => $value) {
                $buffer .= '<input type="hidden" name="'.$field['attributes']['name'].'['.$key.']" value="'.$value.'" />';
            }
        }

        return $buffer;
    }
}
