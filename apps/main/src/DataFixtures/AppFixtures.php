<?php

namespace App\DataFixtures;

use App\Entity\Question;
use App\Entity\ExpectedAnswerLanguage;
use App\Entity\User;
use App\Entity\UserLanguage;
use App\Enum\LanguageCodes;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

class AppFixtures extends Fixture
{
    const NB_USERS = 50;
    const NB_QUESTIONS = 200;

    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create();
        $languageCodes = LanguageCodes::getAll();
        shuffle($languageCodes);

        $users = [];

        for ($i = 0; $i < self::NB_USERS; $i++) {
            $user = new User();
            $user->setEmail('test@test.com'.$i);
            $user->setDisplayName($faker->name());
            $user->setPassword('1');

            $userLanguageCodes = $faker->randomElements($languageCodes, $faker->numberBetween(2, 5));

            foreach ($userLanguageCodes as $userLanguageCode) {
                $userLanguage = new UserLanguage();
                $userLanguage->setUser($user);
                $userLanguage->setCode($userLanguageCode);
                $userLanguage->setLevelNumber($faker->randomElement([
                    UserLanguage::LEVEL_NUMBER_BEGINNER,
                    UserLanguage::LEVEL_NUMBER_INTERMEDIATE,
                    UserLanguage::LEVEL_NUMBER_ADVANCED,
                    UserLanguage::LEVEL_NUMBER_FLUENT
                ]));

                $manager->persist($userLanguage);
            }

            $users[] = $user;

            $manager->persist($user);
        }

        $manager->flush();

        for ($i = 0; $i < self::NB_QUESTIONS; $i++) {
            $questionLanguageCodes = $faker->randomElements($languageCodes, $faker->numberBetween(2, 5));

            $question = new Question();
            $question->setUser($faker->randomElement($users));
            $question->setTargetLanguageCode($questionLanguageCodes[0]);

            $manager->persist($question);

            foreach ($questionLanguageCodes as $questionLanguageCode) {
                $expectedAnswerLanguage = new ExpectedAnswerLanguage();
                $expectedAnswerLanguage->setCode($questionLanguageCode);

                $question->addQuestionAcceptedLanguage($expectedAnswerLanguage);

                $manager->persist($expectedAnswerLanguage);
            }
        }

        $manager->flush();
    }
}
