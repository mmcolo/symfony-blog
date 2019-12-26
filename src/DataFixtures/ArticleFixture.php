<?php

namespace App\DataFixtures;

use Faker\Factory;
use App\Entity\Article;
use App\Entity\Comment;
use App\Entity\Category;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;

class ArticleFixture extends Fixture
{
    public function load(ObjectManager $manager)
    {
        // $product = new Product();
        // $manager->persist($product);
        $faker = \Faker\Factory::create('fr_FR');

        for($i=1;$i<=5;$i++){
            $category = new Category();
            $category->setTitle($faker->sentence());

            $manager->persist($category);

            for($j=1;$j<=mt_rand(4,6);$j++){
                $article = new Article();
                $article->setTitle($faker->sentence())
                    ->setContent($faker->text)
                    ->setimage($faker->imageURL($width = 350, $height = 350))
                    ->setCategory($category);
                
                $manager->persist($article);
                for($k=1;$k<=mt_rand(3,6);$k++){
                    $comment = new Comment();
                    $comment->setAuthor($faker->name)
                            ->setContent($faker->text)
                            ->setArticle($article);

                        $manager->persist($comment);
                }
            }
        }
        $manager->flush();
    }
}
