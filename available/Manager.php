<?php
namespace Field;

class Manager {
    public function render ($field) {
        if (!isset($this->document['dbURI'])) {
            return;
        }
        $url = '%dataAPI%/json-data/' . explode(':', $this->document['dbURI'])[0] . '/byEmbeddedField-' . $this->document['dbURI'] . ':' . $field['name'];
        ob_start();
        $this->manager->table($field['manager'], 'Manager/collections/embedded', $url);
        return ob_get_clean();
    }
}