<?php

class search_configPermissions {
    public function permissions() {
        $permissions = [];
        foreach (search_config_content_types() as $type => $label) {
            $permissions["search $type content"] = array(
                'title' => t('%type_name: Search content of this type', array('%type_name' => $label)),
            );
        }
        return $permissions;
    }
    public function search_config_content_types()  {
        return array_map('check_plain', node_type_get_names());
    }
}
