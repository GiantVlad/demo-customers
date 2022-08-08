<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Contact;
use App\Entity\Customer;
use App\Entity\Image;
use App\Model\ImageUploadApiModel;
use App\Repository\ContactRepository;
use App\Repository\ImageRepository;
use Aws\S3\S3Client;
use Gedmo\Sluggable\Util\Urlizer;
use JMS\Serializer\ContextFactory\SerializationContextFactoryInterface;
use JMS\Serializer\Serializer;
use Nelmio\ApiDocBundle\Annotation\Model;
use Nelmio\ApiDocBundle\Annotation\Operation;
use OpenApi\Annotations as AO;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use OpenApi\Attributes as OA;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

#[Route('/api/upload', name: 'client_')]
class UploadController extends AbstractApiController
{
    public function __construct(
        private readonly ImageRepository $imageRepository,
        private readonly ValidatorInterface $validator,
    ) {
    }

    #[OA\RequestBody(
        content: [
            new OA\MediaType(
                mediaType: "multipart/form-data",
                schema: new OA\Schema(
                    required: ['file'],
                    properties: [
                        new OA\Property(property: 'file', type: "string", format: "binary"),
                        new OA\Property(property: 'contact', type: 'integer')
                    ],
                    type: "object",
                )
            ),
        ],
    )]
    #[Route('/', name: 'upload', methods: [Request::METHOD_POST])]
    #[ParamConverter('image', converter: 'serializer_converter')]
    public function upload(Image $image): JsonResponse
    {
        $this->imageRepository->create($image);

        return $this->okResponse($image);
    }
}
