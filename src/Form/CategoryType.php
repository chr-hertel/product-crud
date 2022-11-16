<?php

declare(strict_types=1);

namespace App\Form;

use App\Entity\Category;
use App\Entity\Exception\CategoryException;
use App\Repository\CategoryRepository;
use Doctrine\ORM\QueryBuilder;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\DataMapperInterface;
use Symfony\Component\Form\Exception\UnexpectedTypeException;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormError;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class CategoryType extends AbstractType implements DataMapperInterface
{
    public function __construct(private readonly CategoryRepository $categoryRepository)
    {
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name')
            ->add('parent', EntityType::class, [
                'choice_label' => 'name',
                'required' => false,
                'class' => Category::class,
                'query_builder' => $this->createQueryBuilder($options['data'] ?? null),
            ]);

        $builder->setDataMapper($this);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefault('data_class', Category::class);
        $resolver->setDefault('empty_data', static function (FormInterface $form) {
            try {
                return new Category(
                    $form->get('name')->getData(),
                    $form->get('parent')->getData(),
                );
            } catch (CategoryException $exception) {
                $form->get('name')->addError(new FormError($exception->getMessage()));

                return null;
            }
        });
    }

    public function mapDataToForms(mixed $viewData, \Traversable $forms): void
    {
        if (null === $viewData) {
            return;
        }

        if (!$viewData instanceof Category) {
            throw new UnexpectedTypeException($viewData, Category::class);
        }

        $forms = iterator_to_array($forms);

        $forms['name']->setData($viewData->getName());

        if ($viewData->hasParent()) {
            $forms['parent']->setData($viewData->getParent());
        }
    }

    public function mapFormsToData(\Traversable $forms, mixed &$viewData): void
    {
        if (null === $viewData) {
            return;
        }

        if (!$viewData instanceof Category) {
            throw new UnexpectedTypeException($viewData, Category::class);
        }

        $forms = iterator_to_array($forms);

        try {
            $viewData->rename($forms['name']->getData());
        } catch (CategoryException $exception) {
            $forms['name']->addError(new FormError($exception->getMessage()));
        }

        $parent = $forms['parent']->getData();
        if (null !== $parent) {
            $viewData->moveTo($parent);
        } else {
            $viewData->removeParent();
        }
    }

    private function createQueryBuilder(?Category $category): ?QueryBuilder
    {
        if (null === $category) {
            return null;
        }

        return $this->categoryRepository->createQueryBuilder('c')
            ->where('c.id != :id')
            ->setParameter('id', $category->getId());
    }
}
