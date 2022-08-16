<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Customer;
use App\Repository\CustomerRepository;
use JMS\Serializer\ContextFactory\SerializationContextFactoryInterface;
use Nelmio\ApiDocBundle\Annotation\Model;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use OpenApi\Attributes as OA;
use Symfony\Component\Validator\Validator\ValidatorInterface;

#[Route('/api/customer', name: 'customer_')]
class CustomerController extends AbstractApiController
{
    public function __construct(
        private readonly CustomerRepository $customerRepository,
        private readonly ValidatorInterface $validator,
        private readonly SerializationContextFactoryInterface $serializationContextFactory,
    ) {
    }

    #[Route('/{customer}', name: 'get_one', requirements: ['customer' => '\d+'], methods: [Request::METHOD_GET])]
    #[OA\Response(
        response: 200,
        description: 'Returns the Customer by ID',
        content: new Model(
            type: Customer::class,
        ),
    )]
    #[ParamConverter('customer', converter: 'doctrine.orm')]
    public function getOne(Customer $customer): JsonResponse
    {
        $serializerContext = $this->serializationContextFactory
            ->createSerializationContext()
            ->setGroups(['Default', 'getters']);

        return $this->okResponse($customer, $serializerContext);
    }

    #[Route('/', name: 'get_list', methods: [Request::METHOD_GET])]
    public function getList(): JsonResponse
    {
        $customers = $this->customerRepository->findAll();
        $serializerContext = $this->serializationContextFactory
            ->createSerializationContext()
            ->setGroups(['Default', 'getters']);

        return $this->okResponse($customers, $serializerContext);
    }

    #[Route('/', name: 'create', methods: [Request::METHOD_POST])]
    #[OA\Response(
        response: 200,
        description: 'Returns created Customer',
        content: new Model(
            type: Customer::class,
        ),
    )]
    #[OA\RequestBody(
        request: "customer",
        required: true,
        content: new OA\JsonContent(
            ref: new Model(type: Customer::class, groups: ['Default']),
        )
    )]
    #[ParamConverter('customer', converter: 'serializer_converter')]
    public function create(Customer $customer): JsonResponse
    {
        $errors = $this->validator->validate($customer);
        if ($errors->count() > 0) {
            return $this->json($errors, Response::HTTP_BAD_REQUEST);
        }
        $customer = $this->customerRepository->create($customer);

        return $this->json($customer);
    }

    #[Route('/{customer}', name: 'update', requirements: ['customer' => '\d+'], methods: [Request::METHOD_PATCH])]
    #[OA\Response(
        response: 200,
        description: 'Returns updated Customer',
        content: new Model(
            type: Customer::class,
        ),
    )]
    #[OA\RequestBody(
        request: "newCustomer",
        required: true,
        content: new OA\JsonContent(
            ref: new Model(type: Customer::class, groups: ['Default']),
            schema: 'change Customer'
        )
    )]
    #[ParamConverter('newCustomer', converter: 'serializer_converter')]
    #[ParamConverter('customer', converter: 'doctrine.orm')]
    public function update(Customer $customer, Customer $newCustomer): JsonResponse
    {
        $customer->setNickName($newCustomer->getNickName());
        $customer->setEmail($newCustomer->getEmail());
        $errors = $this->validator->validate($customer);
        if ($errors->count() > 0) {
            return $this->json($errors, Response::HTTP_BAD_REQUEST);
        }
        $customer = $this->customerRepository->update($customer);

        $serializerContext = $this->serializationContextFactory
            ->createSerializationContext()
            ->setGroups(['Default', 'getters']);

        return $this->okResponse($customer, $serializerContext);
    }
}
