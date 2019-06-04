<?php
/**
 * @file
 * Contains \Drupal\nnels_api\Plugin\resource\entity\taxonomy_term\Genre__1_0
 */

namespace Drupal\nnels_api\Plugin\resource\entity\taxonomy_term;

use Drupal\restful\Plugin\resource\DataInterpreter\DataInterpreterInterface;
use Drupal\restful\Plugin\resource\Field\ResourceFieldInterface;
use Drupal\restful\Plugin\resource\ResourceEntity;
use Drupal\restful\Plugin\resource\ResourceInterface;

/**
 * Class Subject
 * @package Drupal\restful\Plugin\resource\entity\taxonomy_term
 *
 * @Resource(
 *   name = "subject:1.0",
 *   resource = "subjects",
 *   label = "Subject",
 *   description = "Export the subject taxonomy term.",
 *   authenticationTypes = { "token" },
 *   authenticationOptional = FALSE,
 *   dataProvider = {
 *     "entityType": "taxonomy_term",
 *     "bundles": {
 *       "subject"
 *     },
 *   },
 *   majorVersion = 1,
 *   minorVersion = 0
 * )
 */
class Subject__1_0 extends ResourceEntity implements ResourceInterface {
  /**
   * Overrides ResourceEntity::checkEntityAccess().
   *
   * Allow access to create "Subject" resource for privileged users, as
   * we can't use entity_access() since entity_metadata_taxonomy_access()
   * denies it for a non-admin user.
   */
  protected function checkEntityAccess($op, $entity_type, $entity) {
    $account = $this->getAccount();

    return user_access('view published content ', $account);
  }

  protected function publicFields() {
    $public_fields = parent::publicFields();
    unset($public_fields['self']);

    return $public_fields;
  }

  public static function getSubjects($tid) {
    $term = taxonomy_term_load($tid);
    $options = array('absolute' => TRUE);
    $version = str_replace('_', '.',explode("__", get_called_class())[1]);

    return array(
      array(
      'label' => $term->name,
      'name' => $term->machine_name,
      'path' => url("api/v{$version}/subject/" . $term->tid,
        $options),
      )
    );
  }
}