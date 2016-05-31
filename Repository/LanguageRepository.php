<?php

namespace Mrapps\BackendBundle\Repository;

use Doctrine\ORM\EntityRepository;

/**
 * LanguageRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class LanguageRepository extends EntityRepository
{
    public function findByIso($iso) {
        return $this->findOneBy(array('isoCode' => strtolower(trim($iso))));
    }

    public function getSelect() {

        $output = array();

        $languages = $this->findAll();
        foreach($languages as $l) {
            $output[$l->getIsoCode()] = $l->getName();
        }

        return $output;
    }
}
