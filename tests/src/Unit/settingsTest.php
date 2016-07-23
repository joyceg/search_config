<?php

/**
 * Testing the settings: search_config_node_settings()
 */
class settingsTest extends PHPUnit_Framework_TestCase {
  public $defaultSettings = [];

  public function testSettings() {
    $this->defaultSettings = search_config_node_settings();
    $arr = [0, 0, 0, 'default', 'default', [], [], [], [], [], [], 1, 0, 10];
    $this->assertTrue(arraysAreSimilar($arr, $this->defaultSettings));
    function arraysAreSimilar($a, $b) {
      // if the indexes don't match, return immediately
      if (count(array_diff_assoc($a, $b))) {
        return FALSE;
      }
      // compare the values between the two arrays, the indexes match.
      foreach ($a as $k => $v) {
        if ($v !== $b[$k]) {
          return FALSE;
        }
      }
      // we have identical indexes, and no unequal values
      return TRUE;
    }

  }
}

?>
