<?php

declare(strict_types=1);

namespace App\Form;

use Symfony\Component\Form\AbstractTypeExtension;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;

final class NovalidateExtension extends AbstractTypeExtension
{
    public function buildView(FormView $view, FormInterface $form, array $options): void
    {
        if ($form->isRoot()) {
            $view->vars['attr']['novalidate'] = true;
        }
    }

    public static function getExtendedTypes(): iterable
    {
        return [FormType::class];
    }
}
