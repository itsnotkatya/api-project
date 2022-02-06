<?php

namespace App\Controller;

use App\Model\FileUploadModel;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

#[Route('/file', name: 'file')]
class FileController extends AbstractAPIController
{
    private SerializerInterface $serializer;

    private ValidatorInterface $validator;

    public function __construct(SerializerInterface $serializer, ValidatorInterface $validator)
    {
        $this->serializer = $serializer;
        $this->validator = $validator;
    }

    public function upload(Request $request)
    {
        $fileModel = $this->serializer->deserialize(
            $request->getContent(),
            FileUploadModel::class,
            'json'
        );

        $errors = $this->validator->validate($fileModel);

    }
}

