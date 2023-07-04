<?php

namespace JustSomeCode\U2F;

use JustSomeCode\U2F\Protocol\Constants;
use JustSomeCode\U2F\DTO\RegistrationResponse;
use JustSomeCode\U2F\DTO\RegistrationChallenge;
use JustSomeCode\U2F\DTO\DecodedRegistrationResponse;
use JustSomeCode\U2F\Actions\DecodeRegistrationResponse\DecodeRegistrationResponseAction;

/**
 * 1st step of enrollment process: challenge
 * Generates the enrollment (registration) challenge. Browser passes it to the U2F hardware device which
 * signs the challenge.
 */
function u2f_enroll_challenge(string $appId, string $version = Constants::U2F_VERSION): array
{
    $challenge = new RegistrationChallenge(
        challenge: str_random(32),
        version: $version,
        appId: $appId
    );

    return (array)$challenge;
}

/**
 * 2nd step of enrollment process: parsing the response signed by hardware device
 */
function u2f_enroll_parse(
    string $appId,
    string $challenge,
    string $registrationData,
    string $clientData,
    string $version
): DecodedRegistrationResponse
{
    $dto = new RegistrationResponse(
        registrationData: $registrationData,
        challenge: $challenge,
        version: $version,
        appId: $appId,
        clientData: $clientData
    );

    $action = new DecodeRegistrationResponseAction();

    return $action->execute($dto)->getResult();
}

function u2f_auth_challenge()
{}

function u2f_auth_parse()
{}