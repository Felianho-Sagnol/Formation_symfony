<?php

namespace App\DataFixtures;

use App\Entity\Ad;
use Faker\Factory;
use App\Entity\Role;
use App\Entity\User;
use App\Entity\Image;
use App\Entity\Booking;
use App\Entity\Comment;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class AppFixtures extends Fixture
{
    private $encoder;
    public function __construct(UserPasswordEncoderInterface $encoder){
        $this->encoder = $encoder;
    }


    public function load(ObjectManager $manager)
    {
        
        $faker = Factory::create('FR-fr');

        $adminRole = new Role();

        $adminRole->setTitle('ROLE_ADMIN');
        $manager->persist($adminRole);

        $adminUser = new User();
        $adminUser->setFirstName('Félix')
                  ->setLastName('Sagno')
                  ->setEmail('felix@sagnol.com')
                  ->setHash($this->encoder->encodePassword($adminUser,'password'))
                  ->setPicture('http://avatar.io/twitter/LiiorC/')
                  ->setIntroduction('je suis sagnol vertong (une introduction)')
                  ->setDescription("<p>".join('</p><p>',$faker->paragraphs(3))."</p>")
                  ->addUserRole($adminRole);
        $manager->persist($adminUser);


        
        $users = [];
        //nous gerons les utilisateurs ici
        for($i = 1;$i <= 10; $i++){
            $user = new User();

            $hash = $this->encoder->encodePassword( $user,'password');

            $user->setFirstName($faker->firstname)
                 ->setLastName($faker->lastname)
                 ->setEmail($faker->email)
                 ->setIntroduction($faker->sentence())
                 ->setDescription("<p>".join('</p><p>',$faker->paragraphs(3))."</p>")
                 ->setHash($hash);

            $manager->persist($user);

            $users[] = $user;
        }



        //nous gerons les  annonces ici
        for($i = 1;$i <= 30; $i++){

            $ad = new Ad();

            $user = $users[mt_rand(0,count($users) - 1)];

            $tille = $faker->sentence();
            $coverImage = $faker->imageUrl(1000,300);
            $introduction = $faker->paragraph(2);
            $content = "<p>".join('</p><p>',$faker->paragraphs(5))."</p>";
            $ad->setTitle($tille)
            ->setCoverImage($coverImage)
            ->setIntroduction($introduction)
            ->setContent($content)
            ->setPrice(mt_rand(40,100))
            ->setRooms(mt_rand(1,5))
            ->setAuthor($user);

            

            for($j = 1 ;$j <= mt_rand(2,5); $j++){

                $image = new Image();
                $image->setUrl($faker->imageUrl(1000,300))
                      ->setCaption($faker->sentence())
                      ->setAd($ad);
                      
                $manager->persist($image);
            }

            //Gestion des reservations
            for($j = 1 ;$j <= mt_rand(0,10); $j++){
                $booking = new Booking();

                $createdAt = $faker->dateTimeBetween('-6 months');
                $startDate = $faker->dateTimeBetween('-3 months');

                $duration = mt_rand(3,10);

                $endDate = (clone $startDate)->modify("+$duration days"); //clone pour obtenir une nouvelle instance

                $amount = $ad->getPrice()*$duration; //le prix total de la reservaton

                $comment = $faker->paragraph();

                $booker = $users[mt_rand(0,count($users)-1)];
                $booking->setBooker($booker)
                        ->setAd($ad)
                        ->setStartDate($startDate)
                        ->setEndDate($endDate)
                        ->setCreatedAt($createdAt)
                        ->setAmount($amount)
                        ->setComment($comment);

                $manager->persist($booking);

                //Gestion des commentaires
                if(mt_rand(0,1)){
                    $comment = new Comment();
                    $comment->setContent($faker->paragraph())
                            ->setRating(mt_rand(1,5))
                            ->setAuthor($booker)
                            ->setAd($ad);
                    $manager->persist($comment);
                }
            }


            $manager->persist($ad);
        }
        // $product = new Product();
        // $manager->persist($product);

        $manager->flush();
    }
}
