<?php

namespace spec\NiR\GhDashboard\Contexts\Ingestion\Http;

use NiR\GhDashboard\Contexts\Ingestion\Http\SignatureChecker;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Symfony\Component\HttpFoundation\ParameterBag;
use Symfony\Component\HttpFoundation\Request;

class SignatureCheckerSpec extends ObjectBehavior
{
    function let()
    {
        $this->beConstructedWith('my_secret_key');
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(SignatureChecker::class);
    }

    function it_validates_when_signature_matches_payload()
    {
        $this->validate('f683382517207d47df2859674a2eaac76955449b', '{"foo":"bar"}')->shouldReturn(true);
    }

    function it_invalidates_when_signature_does_not_match_payload()
    {
        $this->validate('aze', '{"foo":"bar"}');
    }
}
