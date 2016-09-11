<?php

namespace AppBundle\Annotation;


/**
 * @Annotation
 * @Target("CLASS")
 */
class Link
{
    /**
     * @Required
     *
     * @var string
     */
    public $name;

    /**
     * @var string
     */
    public $route;

    public $params = array();
    /**
     * Instead of specifying route+params, you can just
     * specify the link directly
     *
     * @var string
     */
    public $url;
}