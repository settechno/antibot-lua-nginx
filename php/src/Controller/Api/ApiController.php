<?php


namespace App\Controller\Api;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;

class ApiController extends AbstractController {

    protected function transformJsonBody(Request $request) {
        $data = json_decode($request->getContent(), true);

        if ($data === null) {
            return $request;
        }

        $request->request->replace($data);

        return $request;
    }
}