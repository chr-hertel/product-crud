<?php

declare(strict_types=1);

namespace App\Form;

use App\Entity\Category;
use App\Entity\Exception\ProductException;
use App\Entity\Price;
use App\Entity\Product;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\DataMapperInterface;
use Symfony\Component\Form\Event\PreSetDataEvent;
use Symfony\Component\Form\Exception\UnexpectedTypeException;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormError;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class ProductType extends AbstractType implements DataMapperInterface
{
    private ?DataMapperInterface $baseDataMapper;

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, ['empty_data' => ''])
            ->add('sku', SkuType::class, [
                'invalid_message' => 'Please use the correct format.',
            ])
            ->add('price', PriceType::class)
            ->add('category', EntityType::class, [
                'class' => Category::class,
                'choice_label' => 'name',
            ]);

        $this->baseDataMapper = $builder->getDataMapper();
        $builder->setDataMapper($this);

        $builder->addEventListener(FormEvents::PRE_SET_DATA, [$this, 'removeSkuOnEdit']);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefault('data_class', Product::class);
        $resolver->setDefault('empty_data', static function (FormInterface $form) {
            $sku = $form->get('sku')->getData();
            $price = $form->get('price')->getData();

            if (null === $sku || null === $price) {
                return null;
            }

            try {
                return new Product(
                    $form->get('name')->getData(),
                    $sku,
                    $form->get('category')->getData(),
                    $price,
                );
            } catch (ProductException $exception) {
                $form->get('name')->addError(new FormError($exception->getMessage()));
            }
        });
    }

    public function removeSkuOnEdit(PreSetDataEvent $event): void
    {
        $data = $event->getData();
        $form = $event->getForm();

        if ($data instanceof Product) {
            $form->remove('sku');
        }
    }

    public function mapDataToForms(mixed $viewData, \Traversable $forms): void
    {
        if (null === $this->baseDataMapper) {
            return;
        }

        $this->baseDataMapper->mapDataToForms($viewData, $forms);
    }

    public function mapFormsToData(\Traversable $forms, mixed &$viewData): void
    {
        if (null === $viewData) {
            return;
        }

        if (!$viewData instanceof Product) {
            throw new UnexpectedTypeException($viewData, Product::class);
        }

        /** @var FormInterface[] $form */
        $form = iterator_to_array($forms);

        $name = $form['name']->getData();
        if ($name !== $viewData->getName()) {
            try {
                $viewData->rename($name);
            } catch (ProductException $exception) {
                $form['name']->addError(new FormError($exception->getMessage()));
            }
        }

        $category = $form['category']->getData();
        if ($category !== $viewData->getCategory()) {
            $viewData->categorize($category);
        }

        /** @var Price $price */
        $price = $form['price']->getData();
        if (!$price->equals($viewData->getPrice())) {
            $viewData->costs($price);
        }
    }
}
