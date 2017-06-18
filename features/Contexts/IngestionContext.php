<?php

declare(strict_types=1);

namespace NiR\GhDashboard\Behat\Contexts;

use Behat\Behat\Context\Context;
use Behat\Symfony2Extension\Context\KernelAwareContext;
use NiR\GhDashboard\Behat\ApiExtension\Context\GatherApiContext;
use Symfony\Component\HttpKernel\KernelInterface;

class IngestionContext implements Context, KernelAwareContext
{
    use GatherApiContext;

    /** @var KernelInterface */
    private $kernel;

    /** @var string */
    private $signature;

    public function setKernel(KernelInterface $kernel)
    {
        $this->kernel = $kernel;
    }

    /**
     * @Given I have a json payload representing a raw event
     */
    public function iHaveAJsonPayloadRepresentingARawEvent()
    {
        $body = file_get_contents(__DIR__ . "/payloads/fork.json");
        $secret = $this->kernel->getContainer()->getParameter('github_secret');

        $this->signature = hash_hmac('sha1', $body, $secret);

        $this
            ->getOrCreateRequestBuilder()
            ->setBody($body)
            ->addHeader('Content-Type', 'application/x-www-form-urlencoded')
            ->addHeader('X-Github-Delivery', '4fd6e5b0-5406-11e7-9e96-b516ef50da20')
            ->addHeader('X-Github-Event', 'fork')
        ;
    }

    /**
     * @Given I have a valid signature
     */
    public function iHaveAValidSignature()
    {
        $this
            ->getOrCreateRequestBuilder()
            ->addHeader('X-Hub-Signature', "sha1={$this->signature}")
        ;
    }

    /**
     * @Given I have an invalid signature
     */
    public function iHaveAnInvalidSignature()
    {
        $this
            ->getOrCreateRequestBuilder()
            ->addHeader('X-Hub-Signature', 'sha1=bad_signature')
        ;
    }

    /**
     * @When I send it to the github hook
     */
    public function iSendItToTheGithubHook()
    {
        $builder = $this
            ->getOrCreateRequestBuilder()
            ->setUrl('/hook')
            ->setMethod('POST')
        ;

        $this->apiContext->sendRequest($builder->build());
        print_r((string) $this->apiContext->getResponse()->getBody());
    }
}
