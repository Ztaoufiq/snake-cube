<?php
namespace Domain\Shape\Tests\Entity;
use PHPUnit\Framework\TestCase;
use Domain\Shape\Entity\Cube;
class CubeTest extends TestCase
{
    /** @test */
    public function test_cube_class_attributes(){
        $this->assertClassHasAttribute('dimension', Cube::class);
        $this->assertClassHasAttribute('segment', Cube::class);
    }
    /** @test */
    public function test_cube_object_encapsulation(){
        $cubeData = [
            'dimension' => [3, 3, 3],
            'segment' => [2, 1, 1, 2, 1, 2, 1, 1, 2, 2, 1, 1, 1, 2, 2, 2, 2]
        ];
        $cube = new Cube($cubeData['dimension'],$cubeData['segment']);
        $this->assertEquals($cube->getDimension(), $cubeData['dimension']);
        $this->assertEquals($cube->getSegment(), $cubeData['segment']);
    }
}

