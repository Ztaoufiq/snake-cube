<?php

namespace App\Adapter\Builder;
use Domain\Shape\Port\CubeBuilderInterface;
use Domain\Shape\Entity\Cube;
use App\Adapter\Path\MaximumPath;
use App\Adapter\Path\IdenticalPath;

class OwnCubeBuilder implements CubeBuilderInterface
{
    //private Vector $vector;
    private $dimension;
    private $segment;
    private $identicalPath;
    private $maximumPath;
    public function __construct(array $dimension, array $segment)
    {
        $this->dimension = $dimension;
        $this->segment = $segment;
        $this->identicalPath = new IdenticalPath($dimension);
        $this->maximumPath = new MaximumPath($dimension);
    }

    /**
     * @return array
     */
    public function getDimension(): array
    {
        return $this->dimension;
    }

    /**
     * @return array
     */
    public function getSegment(): array
    {
        return $this->segment;
    }

    public function build(): \Generator
    {
        $segmentLength = array_sum($this->segment);
        $neededLength = array_product($this->dimension) - 1;
        if ($segmentLength !== $neededLength) {
            throw new Exception("Segment has not the right length $segmentLength istead of $neededLength");
        } else {
            foreach ($this->identicalPath->getUsefulPoints() as $initPoint) {
                $this->maximumPath->setCursor($initPoint);
                $this->identicalPath->setCursor($initPoint);
                foreach ($this->buildRec($initPoint, 0) as $solution) {
                    yield $solution;
                }
            }
        }
    }

    public function buildRec($initCursor, $step): \Generator
    {
        if ($step === count($this->segment)) {
            yield [$initCursor, $this->maximumPath->getPath()];
        } else {
            if (count($this->maximumPath->getPath()) === 0) {
                $previousPosition = -1;
            } else {
                $path = $this->maximumPath->getPath();
                $previousPosition = end($path)->getPosition();
            }
            $norm = $this->segment[$step];
            foreach ([$norm, -$norm] as $v) {
                for ($i = 0, $iMax = count($this->dimension); $i < $iMax; $i++) {
                    if ($i !== $previousPosition && $this->identicalPath->mustExplore($i)) {
                        $possibleVector = new Vector($i, $v);
                        if ($this->maximumPath->canMove($possibleVector)) {
                            $this->maximumPath->move($possibleVector);
                            $this->identicalPath->move($possibleVector);
                            foreach ($this->buildRec($initCursor, $step + 1) as $solution){
                                yield $solution;
                            }
                            $this->maximumPath->back();
                            $this->identicalPath->back();
                        }
                    }
                }
            }
        }

    }
}
