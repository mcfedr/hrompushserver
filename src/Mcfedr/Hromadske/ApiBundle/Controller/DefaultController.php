<?php

namespace Mcfedr\Hromadske\ApiBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Cache;
use Symfony\Component\HttpFoundation\JsonResponse;

class DefaultController extends Controller
{
    /**
     * @Route("/streams")
     * @Method({"GET"})
     * @Cache(expires="1 hour", public=true)
     */
    public function streamsAction()
    {
        return new JsonResponse(
            [
                'streams' => $this->get('mcfedr_you_tube_live_streams.loader')->getStreams(),
                'radio' => array_values($this->container->getParameter('mcfedr_hromadske_api.radio')),
                'news' => $this->get('mcfedr_hromadske_news.crawler.news')->fetchNews()
            ]
        );
    }
}