<?php

namespace Domain\Shape\UseCase;
use Domain\Shape\Entity\Cube;
use Domain\Shape\Exception\InvalidCubeDataException;
use Assert\LazyAssertionException;
use function Assert\lazy;
use Domain\Shape\Port\CubeBuilderInterface;

class CreateCube
{
    protected CubeBuilderInterface $cubeBuilder;
    public function __construct(CubeBuilderInterface $cubeBuilder)
    {
        $this->cubeBuilder = $cubeBuilder;
    }
    public function execute(): array
    {
        $cube = new Cube(
            $this->cubeBuilder->getDimension() ?? [],
            $this->cubeBuilder->getSegment() ?? []
        );
        try {
            $this->validate($cube);
            $solutions = [];
            foreach($this->cubeBuilder->build() as $finalCube) {
                array_walk($finalCube[1], array($this, 'getCoordinatesByVector'));
                array_push($solutions, $finalCube);
            }
            return $solutions;
        } catch (LazyAssertionException $e) {
            throw new InvalidCubeDataException($e->getMessage());
        }

    }
    public function validate(Cube $cube): void
    {
        lazy()->that($cube->getDimension())->notEmpty()->count(3)
            ->that($cube->getSegment())->notEmpty()
            ->that(array_sum($cube->getSegment()))->eq(array_product($cube->getDimension())-1)
            ->verifyNow();
    }
    public function getCoordinatesByVector(&$item1): void
    {
        $item1 = str_replace(1, '', $item1->getvalue().$item1->getVariable($item1->getPosition()));
    }
}