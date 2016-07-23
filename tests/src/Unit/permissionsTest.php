<?php

/**
 * Testing the permission()
 */
use \Drupal\search_config\searchConfigPermissions;

class permissionsTest extends PHPUnit_Framework_TestCase {
  public $permissionSet;

  public function setUp() {
    $this->permissionSet = new searchConfigPermissions();
  }

  public function testPermissions() {
    $this->assertEquals('Article: Search content of this type', $this->permissionSet["search Article content"]->searchConfigPermissions->permissions());
    $this->assertEquals('Article: Search content of this type', $this->permissionSet["search Page content"]->searchConfigPermissions->permissions());
  }
}

?>
