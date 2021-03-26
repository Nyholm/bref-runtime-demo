<?php


namespace App\Controller;


use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class HelloController extends AbstractController
{
    public function index()
    {
        $url = $this->generateUrl('foo', [], UrlGeneratorInterface::ABSOLUTE_URL);
        return new Response('Hello Symfony: '.$url);
    }

    public function foo()
    {
        return new Response('Foo Symfony');
    }

    public function timeout()
    {
        for ($i = 0; $i<10; $i++) {
            echo '.';
            sleep(1);
        }
        return new Response('Timeout');
    }
}