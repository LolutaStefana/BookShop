<?php


namespace App\DataFixtures;
use App\Story\DefaultOrganisationsStory;
use App\Story\DefaultUsersStory;
use App\Story\DefaultBooksStory;
use App\Story\DefaultReviewsStory;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        DefaultBooksStory::load();
        DefaultReviewsStory::load();
	DefaultUsersStory::load();
        DefaultOrganisationsStory::load();
    }
}

