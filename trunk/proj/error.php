<?php

Class errorController Extends base {

public function index() 
{
        $this->registry->template->show('illegal');
}


}
?>
