<?php

declare(strict_types=1);

namespace App\Request\ParamConverter;

use App\Entity\Interfaces\IdentifiableInterface;
// use App\Model\ValueObject\UpdateContext;
use App\Request\JsonRequestDeserializer;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Request\ParamConverter\ParamConverterInterface;
use Symfony\Component\HttpFoundation\Request;

final class SerializerParamConverter implements ParamConverterInterface
{
    public function __construct(
        private JsonRequestDeserializer $jsonRequestDeserializer
    ) {
    }

    public function apply(Request $request, ParamConverter $configuration): bool
    {
        /** @psalm-var class-string $class */
        $class = $configuration->getClass();
        $request->attributes->set($configuration->getName(), $this->getValue($request, $class));

        return true;
    }

    public function supports(ParamConverter $configuration): bool
    {
        return (bool) $configuration->getClass();
    }

    /**
     * @psalm-template T
     *
     * @psalm-param class-string<T> $class
     *
     * @psalm-return T
     */
    private function getValue(Request $request, string $class): object
    {
        $entity = $this->jsonRequestDeserializer->deserialize($request, $class);
//        if (is_string($request->getContent()) && $entity instanceof IdentifiableInterface) {
//            $updateContext = (new UpdateContext())->setFields(
//                array_keys(
//                    json_decode($request->getContent(), true)
//                )
//            );
//            $entity->setUpdateContext($updateContext);
//        }

        return $entity;
    }
}
