<?php

declare(strict_types=1);

namespace App\Form;

use App\Entity\Category;
use App\Entity\Exception\CategoryException;
use App\Repository\CategoryRepository;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\DataMapperInterface;
use Symfony\Component\Form\Exception\UnexpectedTypeException;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormError;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CategoryType extends AbstractType implements DataMapperInterface
{
    private $categoryRepository;

    public function __construct(CategoryRepository $categoryRepository)
    {
        $this->categoryRepository = $categoryRepository;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->add('name', TextType::class, ['empty_data' => '']);
        $builder->add('parent', EntityType::class, [
            'class' => Category::class,
            'required' => false,
            'choice_label' => 'name',
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
                    $form->get('parent')->getData()
                );
            } catch (CategoryException $exception) {
                $form->get('name')->addError(new FormError($exception->getMessage()));

                return null;
            }
        });
    }

    public function mapDataToForms($viewData, $forms): void
    {
        if (null === $viewData) {
            return;
        }

        if (!$viewData instanceof Category) {
            throw new UnexpectedTypeException($viewData, Category::class);
        }

        /** @var FormInterface[] $forms */
        $forms = iterator_to_array($forms);

        $forms['name']->setData($viewData->getName());
        if ($viewData->hasParent()) {
            $forms['parent']->setData($viewData->getParent());
        }
    }

    public function mapFormsToData($forms, &$viewData): void
    {
        if (null === $viewData) {
            return;
        }

        if (!$viewData instanceof Category) {
            throw new UnexpectedTypeException($viewData, Category::class);
        }

        /** @var FormInterface[] $forms */
        $forms = iterator_to_array($forms);

        $name = $forms['name']->getData();
        if ($viewData->getName() !== $name) {
            try {
                $viewData->rename($name);
            } catch (CategoryException $exception) {
                $forms['name']->addError(new FormError($exception->getMessage()));
            }
        }

        $formParent = $forms['parent']->getData();
        if (null === $formParent) {
            $viewData->removeParent();
        } else {
            $viewData->moveTo($formParent);
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
