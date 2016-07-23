<?php

namespace Drupal\search_config;

/**
 * Provides dynamic search permissions for nodes of different types.
 */
class searchConfigPermissions {

  public function permissions() {
    $permissions = [];
    foreach (search_config_content_types() as $type => $label) {
      $permissions["search $type content"] = array(
        'title' => t('%type_name: Search content of this type', array('%type_name' => $label)),
      );
    }
    return $permissions;
  }
}
