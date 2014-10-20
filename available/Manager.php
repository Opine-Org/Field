<?php
namespace Field;

class Manager {
	public $services = ['managerController'];

    public function render ($field) {
        if (!isset($this->document['dbURI'])) {
            return;
        }
        ob_start();
        $this->managerController->indexEmbedded($field['manager'], $field['name'], $this->document['dbURI']);
        return ob_get_clean();
    }
}