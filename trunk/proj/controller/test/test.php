<?php

Class testController Extends base {

public function index() {
	/*** set a template variable ***/
        $this->registry->template->welcome = 'Welcome to Student Information & Management System & test';
	/*** load the index template ***/
        $this->registry->template->show('test', 'test');
}

}

?>
