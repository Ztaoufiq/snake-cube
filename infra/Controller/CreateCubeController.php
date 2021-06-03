<?php

namespace App\Controller;

use Domain\Shape\UseCase\CreateCube;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class CreateCubeController
{
    protected CreateCube $useCase;
    public function __construct(CreateCube $useCase)
    {
        $this->useCase = $useCase;
    }

    public function handleRequest(Request $request)
    {
        if($request->isMethod('GET')){
            ob_start();
            include __DIR__ . '/../templates/form.html.php';
            return new Response(ob_get_clean());
        }
        try {
            $dimension = $request->request->get('dimension') ?? [];
            $segment = array_map('intval', str_split($request->request->get('segment'))) ?? [];
            $cube = $this->useCase->execute([
                'dimension' => $dimension,
                'segment' => $segment
            ]);
            return new JsonResponse($cube);
        } catch (\Exception $e) {
            return new Response($e->getMessage());
        }


    }
}