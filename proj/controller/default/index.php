<?php

Class indexController Extends base {

public function index() {
	/*** set a template variable ***/
        $this->registry->template->welcome = 'Welcome to Student Information & Management System';
	/*** load the index template ***/
        $this->registry->template->show('index');
}

}

?>
