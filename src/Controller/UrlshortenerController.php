<?php


namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use App\Entity\Link;
use App\Repository\LinkRepository;


class UrlshortenerController extends AbstractController
{

    function shortener($hash = null, LinkRepository $LinkRepository)
    {
        if ($hash != null) {
            $link = $LinkRepository->findOneBy(['hash' => $hash]);

            if ($link != null) {
                $crossingCount = $link->getCrossingCount();
                if ($crossingCount < 5) {
                    $link->setCrossingCount(++$crossingCount);

                    $entityManager = $this->getDoctrine()->getManager();
                    $entityManager->persist($link);
                    $entityManager->flush();

                    return $this->redirect($link->getUrl());
                }
            }

            return $this->render('404.html.twig', []);
        } elseif (isset($_POST['url'])) {
            $url = trim($_POST['url']);

            do {
                $newHash = $this->generateHash();
            } while($LinkRepository->findOneBy(['hash' => $newHash]));

            $link = new Link();
            $link->setUrl($url);
            $link->setHash($newHash);
            $link->setCrossingCount(0);

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($link);
            $entityManager->flush();
        }

        return $this->getForm(isset($newHash) ? 'http://'.$_SERVER['HTTP_HOST'].'/'.$newHash : null);
    }

    function getForm($shortUrl = null)
    {
        return $this->render('urlshortener/form.html.twig', ['short_url' => $shortUrl]);
    }

    function generateHash()
    {
        $chars = 'abcdefghijklmnopqrstuvwxyz';
        $len = 10;

        $hash = '';
        for($i = 0; $i < $len; $i++) {
            $hash .= $chars[mt_rand(0, strlen($chars) - 1)];
        }

        return $hash;
    }
}