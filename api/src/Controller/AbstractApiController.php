<?php

declare(strict_types=1);

namespace App\Controller;

use JMS\Serializer\SerializationContext;
use JMS\Serializer\SerializerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;

abstract class AbstractApiController extends AbstractController
{
    /**
     * Override to get jms serializer.
     */
    public static function getSubscribedServices(): array
    {
        return array_merge(
            parent::getSubscribedServices(),
            [SerializerInterface::class => '?' . SerializerInterface::class]
        );
    }

    /**
     * Get one item (existing or updated) or list of items.
     *
     * @param array|object $data
     */
    protected function okResponse($data, ?SerializationContext $context = null): JsonResponse
    {
        return $this->getResponse($data, JsonResponse::HTTP_OK, $context);
    }

    /**
     * Get newly created entity.
     *
     * @param array|object $data
     */
    protected function createdResponse($data, ?SerializationContext $context = null): JsonResponse
    {
        return $this->getResponse($data, JsonResponse::HTTP_CREATED, $context);
    }

    /**
     * Get no content after deletion.
     */
    protected function deletedResponse(): JsonResponse
    {
        return new JsonResponse(null, JsonResponse::HTTP_NO_CONTENT);
    }

    /**
     * Get no content after action.
     */
    protected function processedResponse(): JsonResponse
    {
        return new JsonResponse(null, JsonResponse::HTTP_NO_CONTENT);
    }

    /**
     * Get multi status response.
     *
     * @param array|object $data
     */
    protected function multiStatusResponse($data, ?SerializationContext $context = null): JsonResponse
    {
        return $this->getResponse($data, JsonResponse::HTTP_MULTI_STATUS, $context);
    }

    /**
     * Redirect Response.
     *
     * @param array|object $data
     */
    protected function redirectResponse($data): JsonResponse
    {
        return $this->getResponse($data, JsonResponse::HTTP_TEMPORARY_REDIRECT);
    }

    /**
     * @param array|object $data
     */
    protected function getResponse(
        $data,
        int $statusCode = JsonResponse::HTTP_OK,
        ?SerializationContext $context = null
    ): JsonResponse {
        /* It's because jms_serializer.default_context.serialization.serialize_null setting is ignored */
        if ($context) {
            $context->setSerializeNull(true);
        }

        return new JsonResponse(
            $this->getSerializer()->serialize($data, 'json', $context),
            $statusCode,
            [],
            true
        );
    }

    /**
     * Get JMS Serializer.
     */
    protected function getSerializer(): SerializerInterface
    {
        /** @var SerializerInterface $serializer */
        return $this->container->get(SerializerInterface::class);
    }
}
