<?php

namespace CdiCmdb\Options;

use Zend\Stdlib\AbstractOptions;

class CdiCmdbOptions extends AbstractOptions {

    protected $some;
    
    function getSome() {
        return $this->some;
    }

    function setSome($some) {
        $this->some = $some;
    }




}
