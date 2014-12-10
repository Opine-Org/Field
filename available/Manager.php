<?php
namespace Field;

class Manager {
    private $fieldService;
    private $managerController;

    public function __construct ($fieldService, $managerController) {
        $this->fieldService = $fieldService;
        $this->managerController = $managerController;
    }

    public function render ($field, $document, $formObject) {
        if (!isset($document['dbURI'])) {
            return '';
        }
        ob_start();
        $this->managerController->indexEmbedded($field['manager'], $field['name'], $document['dbURI']);
        return ob_get_clean();
    }
}