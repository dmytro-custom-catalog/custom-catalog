<?php

namespace Dmytro\CustomCatalog\Helper;

/**
 * Class AllowedAttributes
 * @package Dmytro\CustomCatalog\Helper
 */
class AllowedAttributes
{
    /**
     * List of allowed attribute codes
     *
     * @var array
     */
    private $allowedAttributes = [];

    /**
     * AllowedAttributes constructor.
     * @param array $allowedAttributes
     */
    public function __construct(
        $allowedAttributes = []
    ) {
        $this->allowedAttributes = $allowedAttributes;
    }

    /**
     * @return array
     */
    public function getList()
    {
        return $this->allowedAttributes;
    }
}
