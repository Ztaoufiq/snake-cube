<?php

namespace Domain\Shape\Tests\Adapters;
use Domain\Shape\Entity\Cube;
use Domain\Shape\Port\CubeBuilderInterface;
class InMemoryCubeBuilder implements CubeBuilderInterface
{
    public array $cubes = [];
    public function __construct()
    {
    }

    public function build(Cube $cube) : array
    {
        $this->cubes[]  = [[0, 0, 0], ['2x', 'y', '-x', '2z', 'y', '-2z', 'x', 'z', '-2y', '-2x', 'y', '-z', 'y', '2z', '-2y', '2x', '2y']];
        return $this->cubes;
    }
}