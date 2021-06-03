<?php

namespace Domain\Shape\Entity;

class Cube
{
    private $dimension;
    private $segment;
    public function __construct(array $dimension, array $segment)
    {
        $this->dimension = $dimension;
        $this->segment = $segment;
    }

    /**
     * @return array
     */
    public function getDimension(): array
    {
        return $this->dimension;
    }

    /**
     * @param array $dimension
     */
    public function setDimension(array $dimension): void
    {
        $this->dimension = $dimension;
    }

    /**
     * @return array
     */
    public function getSegment(): array
    {
        return $this->segment;
    }

    /**
     * @param array $segment
     */
    public function setSegment(array $segment): void
    {
        $this->segment = $segment;
    }

}