<?php

namespace App\Controller;

use App\Exception\API\APIException;
use App\Util\Message;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormInterface;

abstract class AbstractAPIController extends AbstractController
{
    /**
     * @param FormInterface $form
     * @param array|null $content
     * @param bool $needValidate
     *
     * @return mixed
     *
     * @throws APIException
     */
    protected function handleForm(FormInterface $form, ?array $content, bool $needValidate = true)
    {
        // Абстрактный метод для сабмита формы, в случае обнаружения ошибок выкидываем APIException
        $form->submit($content);
        if ($needValidate && (!$form->isValid() || !$form->isSubmitted())) {
            throw new APIException([Message::INCORRECT_DATA]);
        }

        return $form->getData();
    }
}
