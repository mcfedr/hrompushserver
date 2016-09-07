<?php

namespace Mcfedr\Hromadske\ApiBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Cache;

class DefaultController extends Controller
{
    /**
     * @Route("/streams")
     * @Method({"GET"})
     * @Cache(public=true, maxage=3600, smaxage=3600)
     */
    public function streamsAction()
    {
        return $this->json(
            [
                'streams' => $this->get('mcfedr_you_tube_live_streams.loader')->getStreams(),
                'radio' => array_values($this->container->getParameter('mcfedr_hromadske_api.radio')),
                'news' => $this->get('mcfedr_hromadske_news.crawler.news')->fetchNews()
            ]
        );
    }
}
