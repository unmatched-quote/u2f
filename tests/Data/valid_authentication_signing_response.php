<?php

/**
 * This is what's provided by Yubikey once it signs the challenge, using the info given via auth challenge
 */
return [
    'type'          => 'u2f_sign_response',
    'requestId'     => 1,
    'responseData'  => [
        'clientData'    => 'eyJ0eXAiOiJuYXZpZ2F0b3IuaWQuZ2V0QXNzZXJ0aW9uIiwiY2hhbGxlbmdlIjoiWW5KSGJ6UmlOWGRRWVc1WU5uQmhiVW96T0hCSmFWcGtaMEpyYjNwelNWVSIsIm9yaWdpbiI6Imh0dHBzOi8vc2VydmVyLm1mYS53ZWIiLCJjaWRfcHVia2V5IjoiIn0',
        'keyHandle'     => 'TBl7BtPWkiiB7GEdphPferDvTdnB1wSdjSYtHYg3BqNh19RKSNgv6-hPROB-pqeoq3rlel5NsnFqeLUAhF5Nbg',
        'signatureData' => 'AQAAABswRAIgZnfBV7O_O1j3keClYYtobXWFRbropjiYATy_RoVwp8oCIHFvZjRFP5tGDB1qY6vJZ250yAnw8J_ZLVMZ8RN3kOr5'
    ]
];