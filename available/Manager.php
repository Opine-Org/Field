<?php
namespace Field;

class Manager {
    private $fieldService;
    private $managerController;

    public function __construct ($fieldService, $managerController) {
        $this->fieldService = $fieldService;
        $this->managerController = $managerController;
    }

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