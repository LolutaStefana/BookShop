<?php

namespace App\Story;

use App\Entity\Organisation;
use Zenstruck\Foundry\Story;
use App\Factory\OrganisationFactory;

final class DefaultOrganisationsStory extends Story
{
    public function build(): void
    {
        OrganisationFactory::createMany(100);
    }
}
