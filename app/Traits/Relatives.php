<?php

namespace App\Traits;

trait Relatives
{
    private $relationships;

    /**
     * Relatives constructor.
     */
    public function __construct()
    {
        $this -> relationships = includeResources();
    }

    /**
     * @return bool
     */
    public function loadRelationships() : bool
    {
        return ( bool ) count( $this -> relationships );
    }
}