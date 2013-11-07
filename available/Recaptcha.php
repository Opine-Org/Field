<?php
namespace Field;

class Recaptcha {
	public function render ($field) {
	    $field['attributes']['type'] = 'hidden';
	    $field['attributes']['name'] = $field['marker'] . '[' . $field['name'] . ']';
	    $this->fieldService->tag($field, 'input', $attrib>utes);
	    //$this->captcha->setPublicKey($this->fieldService->config::captcha()['publickey']);
		//$this->captcha->setPrivateKey($this->fieldService->config::captcha()['privatekey']);
		echo $captcha->html();
	}
}