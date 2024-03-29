<?php

namespace App\Form;

use App\Entity\Category;
use App\Entity\Product;
use App\Form\DataTransformer\CentimesTransformer;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\CallbackTransformer;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ProductType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, [
                'label' => 'Nom du produit',
                'attr' => [
                    'placeholder'=> 'Tapez le nom du produit'
                ]

            ])
            ->add('description', TextareaType::class, [
                'label' => 'Description courte',
                'attr' => [
                    'placeholder' => 'Tapez une description assez courte mais parlante pour le visiteur'
                ]
            ])
            ->add('price', MoneyType::class, [
                'label' => 'Prix du produit',
                'attr' => [
                    'placeholder' => 'Renseignez le prix de l\'article en euros',
                ],
                'divisor' => 100
            ])
            ->add('mainPicture', UrlType::class, [
                'label' => 'Image du produit',
                'attr' => ['placeholder' => 'Tapez une URL d\'image !']
            ])
            ->add('category', EntityType::class, [
                'label' => 'Catégorie',
                'attr' => [],
                'placeholder' => '--Choisir la catégorie--',
                'class' => Category::class,
                'choice_label' => //'name'
                    function (Category $category) {
                        return strtoupper($category->getName());
                    }

            ]);

        //$builder->get('price')->addModelTransformer(new CentimesTransformer());

  /*      $builder->addEventListener(FormEvents::POST_SUBMIT, function (FormEvent $event) {

            $product = $event->getData();

            if($product->getPrice() !== null)
            {
                $product->setPrice($product->getPrice() * 100);
            }
        });*/

/*        $builder->addEventListener(FormEvents::PRE_SET_DATA, function(FormEvent $event) {
            $form = $event->getForm();
            $product = $event->getData();

            if($product->getPrice() !== null)
            {
                $product->setPrice($product->getPrice/100);
            }

     /*       if($product->getId() === null){
                $form->add('category', EntityType::class, [
                    'label' => 'Catégorie',
                    'attr' => [],
                    'placeholder' => '--Choisir la catégorie--',
                    'class' => Category::class,
                    'choice_label' => //'name'
                        function (Category $category) {
                    return strtoupper($category->getName());
                }

                        ]);
            }
        });*/
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Product::class,
        ]);
    }
}
