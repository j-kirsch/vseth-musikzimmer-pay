<?php

/*
 * This file is part of the vseth-semesterly-reports project.
 *
 * (c) Florian Moser <git@famoser.ch>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\DataFixtures\Base;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

abstract class BaseFixture extends Fixture implements OrderedFixtureInterface, ContainerAwareInterface
{
    /* @var ContainerInterface $container */
    private $container;

    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }

    protected function getTranslator()
    {
        return $this->container->get('translator');
    }

    /**
     * @return \Faker\Generator
     */
    protected function getFaker()
    {
        return Factory::create('de_CH');
    }

    /**
     * create random instances.
     *
     * @param $count
     *
     * @throws \Exception
     */
    protected function loadSomeRandoms(ObjectManager $manager, $count = 5)
    {
        $instances = [];
        for ($i = 0; $i < $count; ++$i) {
            $instance = $this->getRandomInstance();
            if ($instance === null) {
                throw new \Exception('you need to override getRandomInstance to return an instance before you can use this function');
            }

            $instances[] = $instance;
            $manager->persist($instance);
        }

        return $instances;
    }

    /**
     * create an instance with all random values.
     *
     * @return mixed
     */
    protected function getRandomInstance()
    {
        return null;
    }
}
