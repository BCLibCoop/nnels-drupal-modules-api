<?php

/**
 * @file
 * Contains Drupal\my_module\Plugin\resource\AccessToken__1_1.
 */

namespace Drupal\nnels_api\Plugin\resource;

use Drupal\restful\Plugin\resource\ResourceInterface;
use Drupal\restful_token_auth\Plugin\resource\AccessToken__1_0;

/**
 * Class AccessToken__1_1
 * @package Drupal\my_module\Plugin\resource
 *
 * @Resource(
 *   name = "access_token:1.1",
 *   resource = "access_token",
 *   label = "Access token authentication",
 *   description = "Export the access token authentication resource.",
 *   authenticationTypes = {
 *     "basic_auth"
 *   },
 *   authenticationOptional = FALSE,
 *   dataProvider = {
 *     "entityType": "restful_token_auth",
 *     "bundles": {
 *       "access_token"
 *     },
 *   },
 *   formatter = "single_json",
 *   menuItem = "login-token",
 *   majorVersion = 1,
 *   minorVersion = 1
 * )
 */
class AccessToken__1_1 extends AccessToken__1_0 implements ResourceInterface {}