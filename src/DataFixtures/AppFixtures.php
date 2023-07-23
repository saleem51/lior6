<?php

namespace App\DataFixtures;

use App\Entity\Category;
use App\Entity\Product;
use App\Entity\Purchase;
use App\Entity\User;
use Bezhanov\Faker\Provider\Commerce;
use Bluemmb\Faker\PicsumPhotosProvider;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Liior\Faker\Prices;
use PHPUnit\Runner\Filter\Factory;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\String\Slugger\SluggerInterface;


class AppFixtures extends Fixture
{
    protected  $slugger;
    protected $passwordHasher;

    public function __construct(SluggerInterface $slugger, UserPasswordHasherInterface $passwordHasher)
    {
        $this->slugger = $slugger;
        $this->passwordHasher = $passwordHasher;
    }

    public function load(ObjectManager $manager): void
    {
        $faker = \Faker\Factory::create('fr_FR');
        $faker->addProvider(new Prices($faker));
        $faker->addProvider(new Commerce($faker));
        $faker->addProvider(new PicsumPhotosProvider($faker));

        $admin = new User();

        $hash = $this->passwordHasher->hashPassword($admin,"password");
        $admin->setEmail("admin@gmail.com")
              ->setFullName("Admin")
              ->setPassword($hash)
              ->setRoles(['ROLE_ADMIN']);

        $manager->persist($admin);

        $users = [];

        for($u = 0; $u < 5; $u++)
        {
            $user = new User();

            $userHash = $this->passwordHasher->hashPassword($user, "password");
            $user->setEmail("user$u@gmail.com")
                 ->setFullName($faker->name())
                 ->setPassword($userHash);

            $users[] = $user;

            $manager->persist($user);
        }

        for($c = 0; $c < 3; $c++)
        {
            $category = new Category();
            $category->setName($faker->name)
                ->setSlug(strtolower($this->slugger->slug($category->getName())));

            $manager->persist($category);

            for($p = 0; $p < mt_rand(15,20); $p++){
                $product = new product();
                $product->setName($faker->name)
                    ->setPrice($faker->price(4000,20000))
                    ->setSlug(strtolower($this->slugger->slug($product->getName())))
                    ->setDescription($faker->sentence(nbWords: 5))
                    ->setCategory($category)
                    ->setMainPicture($faker->imageUrl(200,200,true));

                $manager->persist($product);
            }
        }

        for($p = 0 ; $p < mt_rand(20,40); $p++){
            $purchase = new Purchase();

            $purchase->setFullName($faker->name)
                     ->setAddress($faker->streetAddress)
                     ->setPostalCode($faker->postcode)
                     ->setCity($faker->city)
                     ->setUser($faker->randomElement($users))
                     ->setTotal(mt_rand(20000, 30000))
                     ->setPurchasedAt($faker->dateTimeBetween('- 6 month', 'now'));


                if($faker->boolean(90)){
                    $purchase->setStatus(Purchase::STATUS_PAID);
                }

                $manager->persist($purchase);

        }



        $manager->flush();
    }
}