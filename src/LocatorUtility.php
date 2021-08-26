<?php

namespace Drupal\nnels_api;

/**
 * Class LocatorUtility
 * @package Drupal\nnels_api
 */
class LocatorUtility {
  /**
   * @param $nid
   * @return array
   */
  public static function buildLinks($nid) {
    $uuid = entity_get_uuid_by_id('node', array($nid));
    $options = array('absolute' => TRUE);
    $options['query'] = array(
      'loadByFieldName' =>
        'uuid'
    );
    $version = getHighestResourceMinorVersion('repositoryItems');
    $uuid_path = url("api/v{$version}/repositoryItems/" . $uuid[$nid], $options);

    unset($options['query']);
    $nid_path = url("api/v{$version}/repositoryItems/" . $nid, $options);

    return array(
      'nid_link' => $nid_path,
      'uuid_link' => $uuid_path,
    );
  }

  /**
   * @param $nid
   * @return string
   */
  public static function getItemPath($nid) {
    return drupal_lookup_path('alias', 'node/' . $nid);
  }
}