<?php

/**
 * Information in key storage server, which we have to use to interpret what signing response carries.
 * The goal is to verify that the response is coming from the device identified by key_handle, and that
 * the packed counter in signatureData is larger than the one stored in KSM.
 * If it is, then we're not subject to replays and the data is valid (among other checks).
 */
return [
    'challenge'     => 'YnJHbzRiNXdQYW5YNnBhbUozOHBJaVpkZ0Jrb3pzSVU',
    'publicKey'     => 'BEfBekIF7AhKwgi4QpQg0mQUQmIIEq6fDCOEv8auCDNNfeNpdNLrRFfAB9KjlBxp/pxjl45v2CnSOuSH+nmHMuk=',
    'keyHandle'     => 'TBl7BtPWkiiB7GEdphPferDvTdnB1wSdjSYtHYg3BqNh19RKSNgv6-hPROB-pqeoq3rlel5NsnFqeLUAhF5Nbg',
    'appId'         => 'https://server.mfa.web',
    'version'       => 'U2F_V2',
    'counter'       => 0
];