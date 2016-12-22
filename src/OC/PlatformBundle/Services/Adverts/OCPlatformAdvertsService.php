<?php

namespace OC\PlatformBundle\Services\Adverts;

use DateTime;
use Exception;

class OCPlatformAdvertsService
{

    private $advertCollection;

    public function displayCollection()
    {
        return $this->advertCollection;
    }

    public function getAdvertCollection()
    {
        $this->advertCollection = $this->getListAdverts();

//         $em = $this->getDoctrine()->getManager();

        return $this;
    }

//     public function getDefaultAdvert($id)
//     {
//         $advert = array(
//             'title'   => 'Recherche développpeur Symfony2',
//             'id'      => $id,
//             'author'  => 'Alexandre',
//             'content' => 'Nous recherchons un développeur Symfony2 débutant sur Lyon. Blabla…',
//             'date'    => new DateTime()
//         );

//         return $advert;
//     }

    public function sortCollection($order = 'asc')
    {
        $method = 'ksort';
        if ('desc' == strtolower($order)) {
            $method = 'krsort';
        }

        $method($this->advertCollection);

        return $this;
    }

    public function getCollectionSlice($offset, $limit)
    {
        if (!isset($offset) && empty($limit)) {
            throw new Exception("Parameters offset and limit not defined. If you don't define them, no need of this method");
        }

        $this->advertCollection = array_slice(
            $this->advertCollection,
            $offset,
            $limit,
            true
        );

        return $this;
    }

    protected function getListAdverts()
    {
        // Notre liste d'annonce en dur
        $listAdverts = array(
            array(
                'title'   => 'Recherche développpeur Symfony',
                'id'      => 1,
                'author'  => 'Alexandre',
                'content' => 'Nous recherchons un développeur Symfony débutant sur Lyon. Blabla…',
                'date'    => new DateTime()
            ),
            array(
                'title'   => 'Mission de webmaster',
                'id'      => 2,
                'author'  => 'Hugo',
                'content' => 'Nous recherchons un webmaster capable de maintenir notre site internet. Blabla…',
                'date'    => new DateTime()
            ),
            array(
                'title'   => 'Offre de stage webdesigner',
                'id'      => 3,
                'author'  => 'Mathieu',
                'content' => 'Nous proposons un poste pour webdesigner. Blabla…',
                'date'    => new DateTime()
            ),
            array(
                'title'   => 'Dev lead PHP',
                'id'      => 4,
                'author'  => 'Xavier',
                'content' => 'Nous proposons un poste pour Dev Lead. Blabla…',
                'date'    => new DateTime()
            )
        );

        return $listAdverts;
    }
}