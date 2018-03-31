<?php

/**
 * @file
 * Contains \Drupal\nnels_api\Plugin\resource\entity\user\user\Users__1_1.
 */

namespace Drupal\nnels_api\Plugin\resource\entity\user\user;

use Drupal\restful\Plugin\resource\ResourceInterface;
use Drupal\restful\Plugin\resource\Users__1_0;

/**
 * Class Users
 * @package Drupal\nnels_api\Plugin\resource\entity\user\user
 *
 * @Resource(
 *   name = "users:1.1",
 *   resource = "users",
 *   label = "Users",
 *   description = "Export the user entity.",
 *   authenticationTypes = TRUE,
 *   authenticationOptional = TRUE,
 *   dataProvider = {
 *     "entityType": "user",
 *     "bundles": {
 *       "user"
 *     },
 *   },
 *   majorVersion = 1,
 *   minorVersion = 1
 * )
 */
class Users__1_1 extends Users__1_0 implements ResourceInterface {
  /**
   * {@inheritdoc}
   */
  public function process() {
    $this->convertMeInPath();

    return parent::process();
  }

  //@todo Ensure this cannot be listed by normal API user

  /**
   * Replace any instances of 'me' in the $path with the authenticated user's
   * UID.
   *
   * See Drupal\restful\Plugin\resource\Resource::view()
   */
  public function convertMeInPath() {
    $path = $this->getPath();
    $ids = explode(static::IDS_SEPARATOR, $path);
    if (in_array('me', $ids)) {
      $account = $this->getAccount();

      foreach ($ids as &$id) {
        if ($id === 'me') {
          $id = $account->uid;
        }
      }

      $this->setPath(implode(static::IDS_SEPARATOR, $ids));
    }
  }
}