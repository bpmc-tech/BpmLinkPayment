<?php

namespace Plugin\BpmLinkPayment\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Config
 *
 * @ORM\Table(name="plg_bpm_link_payment_config")
 * @ORM\Entity(repositoryClass="Plugin\BpmLinkPayment\Repository\ConfigRepository")
 */
class Config
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer", options={"unsigned":true})
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="api_token", type="string", length=255)
     */
    private $api_token;

    /**
     * @var string
     *
     * @ORM\Column(name="api_domain", type="string", length=255)
     */
    private $api_domain;

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getApiToken()
    {
        return $this->api_token;
    }

    /**
     * @param string $name
     *
     * @return $this;
     */
    public function setApiToken($api_token)
    {
        $this->api_token = $api_token;

        return $this;
    }
    
    /**
     * @return string
     */
    public function getApiDomain()
    {
        return $this->api_domain;
    }

    /**
     * @param string $name
     *
     * @return $this;
     */
    public function setApiDomain($api_domain)
    {
        $this->api_domain = $api_domain;

        return $this;
    }
}
