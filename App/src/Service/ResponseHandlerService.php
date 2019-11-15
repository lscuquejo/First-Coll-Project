<?php

namespace App\Service;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;

class ResponseHandlerService extends AbstractController
{
    public function successfullyCreated(string $entityName, string $uuid)
    {
        $response = [
            'type' => 'Created',
            'title' => "New $entityName Created",
            'detail' => "Saved new $entityName with this uuid = " . $uuid,
            'instance' => "/api/$entityName",
            'uuid' => $uuid,
        ];

        return $this->json($response, Response::HTTP_CREATED);
    }

    public function successfullyUpdated(string $entityName, string $uuid)
    {
        $response = [
            'type' => 'Updated',
            'title' => "New $entityName Updated",
            'detail' => "Updated $entityName with uuid" . $uuid,
            'instance' => "/api/$entityName/{uuid}",
        ];

        return $this->json($response, Response::HTTP_OK);
    }

    public function successfullyDeleted(string $entityName, string $uuid)
    {
        $response = [
            'type' => 'Deleted',
            'title' => "$entityName Deleted",
            'detail' => "Deleted $entityName with this uuid = " . $uuid,
            'instance' => "/api/$entityName/{uuid}",
        ];

        return $this->json($response, Response::HTTP_OK);
    }
}
