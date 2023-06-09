<?php

namespace App\DataFixtures;

use App\Entity\Category;
use App\Entity\Product;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use PHPUnit\Runner\Filter\Factory;
use Symfony\Component\String\Slugger\SluggerInterface;

class AppFixtures extends Fixture
{
    protected  $slugger;

    public function __construct(SluggerInterface $slugger)
    {
        $this->slugger = $slugger;
    }

    public function load(ObjectManager $manager): void
    {
        $faker = \Faker\Factory::create('fr_FR');
     /*   $faker->addProvider(new \Liior\Faker\Prices($faker));
        $faker->addProvider(new \Bezhanov\Faker\Provider\Commerce($faker));
        $faker->addProvider(new \Bluemmb\Faker\PicsumPhotosProvider($faker));*/

        for($c = 0; $c < 3; $c++)
        {
            $category = new Category();
            $category->setName($faker->department())
                ->setSlug(strtolower($this->slugger->slug($category->getName())));

            $manager->persist($category);

            for($p = 0; $p < mt_rand(15,20); $p++){
                $product = new product();
                $product->setName($faker->productName())
                    ->setPrice($faker->price(4000,20000))
                    ->setSlug(strtolower($this->slugger->slug($product->getName())))
                    ->setDescription($faker->sentence(nbWords: 5))
                    ->setCategory($category)
                    ->setMainPicture($faker->imageUrl(200,200,true));

                $manager->persist($product);
            }
        }



        $manager->flush();
    }
}