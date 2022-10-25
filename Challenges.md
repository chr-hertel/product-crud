# Symfony Forms in Detail

Trainer: https://twitter.com/el_stoffel
Repository: https://github.com/chr-hertel/product-crud

## Challenge 1 - Contact Form

* merge branch `contact/01-base`
    ```bash
    git merge origin/contact/01-base
    ```
* implement `App\Contact\Dto`
    * see `App\Contact\Mailer` for help
* implement `App\Form\ContactType`
* implement `App\Contact\ContactController::contact(...)`
    * URI `/contact`
    * route name `contact`
* implement template `templates/contact.html.twig`

See documentation for help:
* https://symfony.com/doc/current/forms.html#usage
* https://symfony.com/doc/current/best_practices.html#forms

### Additional challenges

* Form Validation
    ```bash
    git merge origin/contact/02-validation
    ```
* Form Layout
    ```bash
    git merge origin/contact/03-layout
    ```

## Challenge 2 - Category Form

* merge branch `category/01-base`
    ```bash
    git merge origin/category/01-base
    ```
* update Doctrine schema
    ```bash
    bin/console doctrine:schema:update --force
    ```
* implement `App\Form\CategoryType` using `DataMapper` and `empty_data`
* catch CategoryException and add `FormError` instances

See documentation for help:
* https://symfony.com/doc/current/form/data_mappers.html

### Additional challenges

* optional parent relation
    ```bash
    git merge origin/category/02-parent
    ```
    https://symfony.com/doc/current/reference/forms/types/entity.html

* filter parent categories to filter out itself
* use category path as choice label, e.g. Food » Vegetable

## Challenge 3 - Product Form

* merge branch `product/01-base`
    ```bash
    git merge origin/product/01-base
    ```
* implement `App\Form\SkuType` using `DataTransformer` and `TextType` as parent
* implement `App\Form\ProductType` for `App\Entity\Product` using `DataMapper`

See documentation for help:
* https://symfony.com/doc/current/form/data_transformers.html
* https://symfony.com/doc/current/reference/forms/types/entity.html

### Additional challenges

* don't show field `sku` for existing products 
    ```bash
    git merge origin/product/02-view
    ```
