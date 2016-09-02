<?php
/**
 * Created by PhpStorm.
 * User: tomtom
 * Date: 01.09.16
 * Time: 17:47
 */

namespace AppBundle\EventListener;


use AppBundle\Api\ApiProblemException;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\GetResponseForExceptionEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\HttpFoundation\JsonResponse;
use AppBundle\Api\ApiProblem;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;

class ApiExceptionSubscriber implements EventSubscriberInterface
{
    /**
     * A flag to indicate if debugging mode is enabled
     *
     * @var bool
     */
    private $debug = false;

    public function __construct($debug)
    {
        $this->debug = $debug;
    }

    public function onKernelException(GetResponseForExceptionEvent $event)
    {
        $exception = $event->getException();

        $statusCode = $exception instanceof HttpExceptionInterface
            ? $exception->getStatusCode()
            : Response::HTTP_INTERNAL_SERVER_ERROR;

        // Allow HTTP 500 on ognoing exception to be handled by symfony when running in debug mode
        if ($this->debug && $statusCode >= Response::HTTP_INTERNAL_SERVER_ERROR) {
            return;
        }

        if ($exception instanceof ApiProblemException) {
            $apiProblem = $exception->getApiProblem();
        } else {
            $apiProblem = new ApiProblem(
                $statusCode
            );

            /**
             * @see https://tools.ietf.org/html/draft-ietf-appsawg-http-problem-03#section-3.1
             *
             * If it is an HttpException message (e.g. for 404, 403), we'll say as a rule that the exception message is
             * safe for the client. Otherwise, it could be some sensitive low-level exception, which should *not* be
             * exposed
             */
            if ($exception instanceof HttpExceptionInterface) {
                $apiProblem->set('detail', $exception->getMessage());
            }
        }

        $data = $apiProblem->toArray();

        /**
         * Provide an url for the error documentation
         * @see: https://tools.ietf.org/html/draft-ietf-appsawg-http-problem-03#section-3
         */
        if ($data['type'] != 'about:blank') {
            $data['type'] = 'http://localhost:8000/docs/errors#'.$data['type'];
        }

        $response = new JsonResponse(
            $data,
            $apiProblem->getStatusCode()
        );
        $response->headers->set('Content-Type', 'application/json+problem');

        $event->setResponse($response);
    }

    public static function getSubscribedEvents()
    {
        return [KernelEvents::EXCEPTION => 'onKernelException'];
    }

}