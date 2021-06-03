<?php
namespace Domain\Shape\Tests\UseCase;
use PHPUnit\Framework\TestCase;
use Domain\Shape\UseCase\CreateCube;
use Domain\Shape\Entity\Cube;
use Domain\Shape\Tests\Adapters\InMemoryCubeBuilder;
use Domain\Shape\Exception\InvalidCubeDataException;
use App\Port\Builder\OwnCubeBuilder;
class CreateCubeTest extends TestCase
{
    /** @test */
    public function validate_cube(): void
    {
        $dimension = [3, 3, 3];
        $segment = [2, 1, 1, 2, 1, 2, 1, 1, 2, 2, 1, 1, 1, 2, 2, 2, 2];
        $builder = new InMemoryCubeBuilder($dimension);
        $useCase = new CreateCube($builder);
        $cube = new Cube($dimension, $segment);
        $validate = $useCase->validate($cube);
        $this->assertNull($validate);
    }
    /**
     * @dataProvider myCubeData
     */
    public function invalidate_data_cube($dimension, $segment): void
    {
        $builder = new InMemoryCubeBuilder;
        $useCase = new CreateCube($builder);
        $this->expectException(InvalidCubeDataException::class);
        $cube = $useCase->execute([
            'volumeDimension' => $dimension,
            'snakeSegment' => $segment
        ]);
    }
    public function myCubeData(): array
    {
        return [
            [[], []],
            [[], [2, 1, 1, 2, 1, 2, 1, 1, 2, 2, 1, 1, 1, 2, 2, 2, 2]],
            [[3, 3, 3], []],
            [[3, 3], [2, 1, 1, 2, 1, 2, 1, 1, 2, 2, 1, 1, 1, 2, 2, 2, 2]],
            [[3, 3, 3], [2, 1, 1, 2, 1, 2, 1, 1, 2, 2, 1, 1, 1, 2]],
        ];
    }
    /** @test */
    public function create_cube(): void
    {
        $dimension = [3, 3, 3];
        $segment = [2, 1, 1, 2, 1, 2, 1, 1, 2, 2, 1, 1, 1, 2, 2, 2, 2];
        $builder = new OwnCubeBuilder($dimension, $segment);
        $useCase = new CreateCube($builder);
        $cubesSolutions = $useCase->execute();
        $this->assertContains([0, 0, 0], $cubesSolutions[0]);
        $this->assertContains(['2x', 'y', '-x', '2z', 'y', '-2z', 'x', 'z', '-2y', '-2x', 'y', '-z', 'y', '2z', '-2y', '2x', '2y'], $cubesSolutions[0]);

    }
}

