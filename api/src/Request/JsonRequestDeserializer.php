<?php

declare(strict_types=1);

namespace App\Request;

use Doctrine\Persistence\ManagerRegistry;
use JMS\Serializer\ContextFactory\DeserializationContextFactoryInterface;
use JMS\Serializer\DeserializationContext;
use JMS\Serializer\Serializer;
use JMS\Serializer\SerializerInterface;
use Symfony\Component\HttpFoundation\Request;

final class JsonRequestDeserializer
{
    public function __construct(
        private SerializerInterface $serializer,
        private DeserializationContextFactoryInterface $contextFactory,
    ) {
    }

    public function deserialize(Request $request, string $class): object
    {
        $context = $this->contextFactory->createDeserializationContext();

        if ($request->isMethod(Request::METHOD_GET)) {
            return $this->deserializeRequestQuery($request, $class, $context);
        }

        return $this->deserializeRequestContent($request, $class, $context);
    }

    private function deserializeRequestQuery(Request $request, string $class, DeserializationContext $context): object
    {
        if (! $this->serializer instanceof Serializer) {
            throw new \LogicException(sprintf('%s cannot deserialize from array', $this->serializer::class));
        }

        return $this->serializer->fromArray(
            $request->query->all(),
            $class,
            $context
        );
    }

    private function deserializeRequestContent(Request $request, string $class, DeserializationContext $context): object
    {
        return $this->serializer->deserialize(
            (string) $request->getContent(),
            $class,
            'json',
            $context
        );
    }
}
