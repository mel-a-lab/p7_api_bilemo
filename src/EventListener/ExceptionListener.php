<?php
// src/EventListener/ExceptionListener.php
namespace App\EventListener;

use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;

class ExceptionListener
{
    public function onKernelException(ExceptionEvent $event)
    {
        $exception = $event->getThrowable();

        // Récupérer le code de statut HTTP de l'exception
        $statusCode = $exception instanceof HttpExceptionInterface ? $exception->getStatusCode() : 500;

        // Transformer l'exception en message JSON
        $responseData = [
            'error' => [
                'code' => $statusCode,
                'message' => $exception->getMessage()
            ]
        ];

        // Créer une réponse JSON à partir des données
        $response = new JsonResponse($responseData, $statusCode);

        // Définir les en-têtes de la réponse
        $response->headers->set('Content-Type', 'application/json');

        // Définir la réponse dans l'événement
        $event->setResponse($response);
    }
}
