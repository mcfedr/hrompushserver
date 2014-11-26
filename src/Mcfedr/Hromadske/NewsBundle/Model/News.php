<?php
/**
 * Created by mcfedr on 28/05/2014 22:55
 */

namespace Mcfedr\Hromadske\NewsBundle\Model;

class News implements \JsonSerializable
{
    /**
     * @var string
     */
    private $href;

    /**
     * @var string
     */
    private $title;

    /**
     * @var \DateTime
     */
    private $time;

    /**
     * @param string $href
     * @param \DateTime $time
     * @param string $title
     */
    function __construct($href, \DateTime $time, $title)
    {
        $this->href = $href;
        $this->time = $time;
        $this->title = $title;
    }

    /**
     * @return string
     */
    public function getHref()
    {
        return $this->href;
    }

    /**
     * @param string $href
     * @return News
     */
    public function setHref($href)
    {
        $this->href = $href;
        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getTime()
    {
        return $this->time;
    }

    /**
     * @param \DateTime $time
     * @return News
     */
    public function setTime($time)
    {
        $this->time = $time;
        return $this;
    }

    /**
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * @param string $title
     * @return News
     */
    public function setTitle($title)
    {
        $this->title = $title;
        return $this;
    }

    public function jsonSerialize()
    {
        return [
            'href' => $this->getHref(),
            'time' => $this->getTime()->format('c'),
            'title' => $this->getTitle()
        ];
    }
}
