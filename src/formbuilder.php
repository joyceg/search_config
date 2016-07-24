<?php

namespace Drupal\search_config\buildForm;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Session\AccountInterface;
use Drupal\Core\Url;
use Drupal\Core\StringTranslation;
use Drupal\Component\Utility\Html;
class searchForm extends FormBase{
    public function getFormId()
    {
        return 'search_form';
    }
    public function buildForm(array $form, FormStateInterface $form_state)
    {
        $string_overrides = search_config_string_overrides();   
        $form['search_config_string_overrides'] = array(
            '#type' => 'fieldset',
            '#theme' => 'search_config_admin_label_form',
            '#title' => $this->t('Labels and string overrides'),
            '#collapsible' => TRUE,
            '#collapsed' => TRUE,
            'labels' => array(),

            'title_display' => array(),
            '#tree' => TRUE,
        );
        $title_display_options = array(
            'default' => $this->t('Default'),
            'invisible' => $this->t('Hidden'),
            'description' => $this->t('Below'),
        );
        $slabels = array(
            'basic' => array($this->t('Keywords'), t('Enter your keywords')),
            'basic_with_keys' => array($this->t('Keywords (with search keys)'), $this->t('Enter your keywords')),
            'basic_submit' => array($this->t('Submit button'), $this->t('Search')),
            'advanced_fieldset' => array($this->t('Wrapping fieldset'), $this->t('Advanced search')),
            'advanced_fieldset_with_keys' => array($this->t('Wrapping fieldset (with search keys)'), $this->t('Advanced search')),
            'advanced_any' => array($this->t('Containing any ...'), $this->t('Containing any of the words')),
            'advanced_phrase' => array($this->t('Containing the phrase'), $this->t('Containing the phrase')),
            'advanced_none' => array($this->t('Containing none ...'), $this->t('Containing none of the words')),
            'advanced_type' => array($this->t('Types'), $this->t('Only of the type(s)')),
            'advanced_language' => array($this->t('Language selector'), $this->t('Languages')),
            'advanced_submit' => array($this->t('Submit button'), $this->t('Advanced search')),
        );
        $form['search_config_string_overrides']['#field-labels'] = $slabels;
        foreach ($slabels as $skey => $slabel) {
            $form['search_config_string_overrides']['labels'] += array(
                $skey => array(
                    '#type' => 'textfield',
                    '#title' => $slabel[0],
                    '#title_display' => 'invisible',
                    '#default_value' => $string_overrides['labels'][$skey],
                    '#description' => $this->t('t() string: !translation' ,
                        array('%label' => $slabel[1], '!translation' => '!search_config:' . $skey)),
                    '#size' => 40,
                ),
            );
            if (isset($string_overrides['title_display'][$skey])) {
                $form['search_config_string_overrides']['title_display'] += array(
                    $skey => array(
                        '#type' => 'radios',
                        '#title' => $slabel[0],
                        '#title_display' => 'invisible',
                        '#default_value' => $string_overrides['title_display'][$skey],
                        '#options' => $title_display_options,
                    ),
                );
            }
        }
        $settings = search_config_node_settings();
        $role_options = array_map('Html::escape', user_roles());
        $form['content_node_search_config'] = array(
            '#type' => 'fieldset',
            '#tree' => TRUE,
            '#title' => $this->t('Additional Node Search Configuration'),
            '#collapsed' => \Drupal::config('search_config.node')->get('search_config'),
            '#collapsible' => TRUE,
            'info' => array(
                '#markup' => $this->t('<p>The basic search form is the keyword and search button that is shown to all users. The additional fields are all part of the advanced search form that is only shown to users that have the "%advsearch" permission. <em>This module does not override these permissions!</em></p>',
                    ['%usesearch' => $this->t('Use search'), '%advsearch' => t('Use advanced search')]),
            ),
            'forms' => array(
                '#type' => 'item',
                '#title' => $this->t('Form control'),
                'toggle_forms' => array(
                    '#type' => 'checkbox',
                    '#title' => $this->t('Only show one form at a time'),
                    '#description' => $this->t('This will hide the basic search form if the advanced search form is present.'),
                    '#default_value' => $settings['forms']['toggle_forms'],
                ),
                'move_keyword_search' => array(
                    '#type' => 'checkbox',
                    '#title' => $this->t('Move basic keyword search'),
                    '#description' => $this->t('This will move the keyword search field, (%label), into the advanced form and hide the rest of the basic form.'),
                    '#default_value' => $settings['forms']['move_keyword_search'],
                ),
                'advanced_populate' => array(
                    '#type' => 'checkbox',
                    '#title' => $this->t('Populate the advanced form with default values'),
                    '#description' => $this->t('By default the advanced form is always empty after a submission. This option will pull the values from the basic search form and use these to populate the advanced search form fields. <span style="color: #ff0000; font-style: italic;">Experimental</span>'),
                    '#default_value' => $settings['forms']['advanced_populate'],
                ),
                'remove_containing_wrapper' => array(
                    '#type' => 'radios',
                    '#title' => $this->t('Remove all <em>Containing ...</em> keyword options'),
                    '#default_value' => $settings['forms']['remove_containing_wrapper'],
                    '#options' => array(
                        'default' => $this->t('No change'),
                        'remove' => $this->t('Remove unconditionally'),
                        'empty' => $this->t('Remove wrapper if no containing fields'),
                    ),
                ),
                'advanced_expand' => array(
                    '#type' => 'radios',
                    '#title' => $this->t('Control how the advanced form initially renders'),
                    '#default_value' => $settings['forms']['advanced_expand'],
                    '#options' => array(
                        'default' => $this->t('Always collapsed (no modification)'),
                        'remove' => $this->t('Remove the collapsible wrapper and title'),
                        'expand_always' => $this->t('Force it to stay open, but keep the open look and feel'),
                        'expand_on_first' => $this->t('Expand initially, then collapsed'),
                        'expand_if_empty' => $this->t('Expand initially or when there are zero results'),
                    )
                ),
            ),
            'fields' => [
                '#type' => 'item',
            ],
        );
        $fields = array(
            'containing_any' => $this->t('Containing any of the words'),
            'containing_phrase' => $this->t('Containing the phrase'),
            'containing_none' => $this->t('Containing none of the words'),
            'types' => $this->t('Only of the type(s)'),
            'category' => $this->t('By category'), // @todo: Find correct field naming
            'language' => $this->t('Languages'),
        );
        foreach ($fields as $key => $label) {
            $form['content_node_search_config']['fields'][$key] = array(
                '#type' => 'fieldset',
                '#title' => $this->t('%field settings', array('%field' => $label)),
                '#collapsible' => TRUE,
                '#collapsed' => TRUE,
                'remove' => array(
                    '#type' => 'checkbox',
                    '#title' => $this->t('Hide this field', array('%field' => $label)),
                    '#default_value' => $settings['fields'][$key]['remove'],
                ),
                'roles' => array(
                    '#type' => 'checkboxes',
                    '#title' => $this->t('Override the above option by selecting one or more roles that should see this field:'),
                    '#options' => $role_options,
                    '#default_value' => $settings['fields'][$key]['roles'],
                ),
            );
            if ($key == 'category') {
                $form['content_node_search_config']['fields'][$key]['#access'] = FALSE;
            }
            if ($key == 'types') {
                $groupings = array();
                foreach ($settings['fields'][$key]['groupings'] as $gtypes => $glabels) {
                    $groupings [] = $gtypes . '|' . $glabels;
                }
                $groupings_type_help_items = array();
                foreach (search_config_content_types() as $gtype => $glabel) {
                    $groupings_type_help_items[] = $this->t('!type|%label', array('!type' => $gtype, '%label' => $glabel));
                }
                $groupings_type_help_items[] = $this->t('&lt;other-types&gt;|Search for all other content types not used already in a grouping that is allowed by the type filter.');
                $groupings_type_help_items[] = $this->t('&lt;all-types&gt;|Search for every content types allowed by the type filter.');

                $groupings_type_help = $this->t('<p>Enter one grouping per line, in the format "%format". The key, %key, is a comma separated list of node content types. The %label is used in the search form display. There are two special key values, &lt;other-types&gt; and &lt;all-types&gt;, described below. A full list of value options are:</p>',
                    array('%format' => $this->t('machine name(s)') . '|' . t('label'), "%key" => t('machine name(s)'), '%label' => t('label')));
                $item_list = array(
                    '#type' => 'item_list',
                    '#items' => $groupings_type_help_items,
                );
                $groupings_type_help .= \Drupal::service('renderer')->render($item_list);
                $groupings_type_help .= $this->t('<p>Leave empty to use the standard type options.</p>');
                $form['content_node_search_config']['fields'][$key] += array(
                    'filter' => array(
                        '#type' => 'checkboxes',
                        '#title' => $this->t('Hide the following content types from the %field filter:', array('%field' => $label)),
                        '#description' => $this->t(array('!url' => Url::fromRoute('user.admin_permissions')), '<strong>This only limits the filter options</strong>. You must set the <a href="!url">permissions here</a> to enforce search restrictions per content types. These premissions are also used to filter the options shown to the user.'
                        ),
                        '#options' => search_config_content_types(),
                        '#default_value' => $settings['fields'][$key]['filter'],
                    ),
                    'groupings' => array(
                        '#title' => $this->t('Replace type options with custom type groups'),
                        '#type' => 'textarea',
                        '#rows' => 5,
                        '#default_value' => implode("\n", $groupings),
                        '#element_validate' => array('element_node_search_config_groupings_validate'),
                        '#description' => $groupings_type_help,
                    ),
                );
            }
        }

        // Provide some additional help for groupings setting.

        // Restrictions and a summary of search config permissions
        $form['content_node_search_config']['results'] = array(
            '#type' => 'fieldset',
            '#title' => $this->t('Results'),
            '#collapsible' => TRUE,
            '#collapsed' => TRUE,
            'limit' => array(
                '#type' => 'select',
                '#title' => $this->t('Pager limit'),
                '#description' => $this->t('This option alters the number of results per page on the search results page.'),
                '#default_value' => $settings['results']['limit'],
                '#options' => array(0 => $this->t('Do not alter')) + array_combine(array_merge(range(1, 25, 1), range(30, 100, 5), range(150, 1000, 25)), array_merge(range(1, 25, 1), range(30, 100, 5), range(150, 1000, 25))),
            ),
        );
        $permissions = array();
        $roles = array_map('check_plain', user_roles());
        $search_config_permission = array_filter(\Drupal::service('search_config.permissions')->getPermissions(), function($v) {
            return $v['provider'] == 'search_config';
        }
        );
        foreach ($search_config_permission as $permission => $info) {
            $permissions[$permission] = array();
            foreach (search_config_get_roles_by_permission($permission) as $rid) {
                $permissions[$permission][$rid] = $roles[$rid];
            }
        }
        $items = array();
        $all_permissions = array(AccountInterface::ANONYMOUS_ROLE => $roles[\Drupal\Core\Session\AccountInterface::ANONYMOUS_ROLE], AccountInterface::AUTHENTICATED_ROLE => $roles[AccountInterface::AUTHENTICATED_ROLE],
        );
        $search_all = array();
        foreach ($permissions['search all content'] as $rid => $role) {
            $search_all[$rid] = $role . '<sup>*</sup>';
            if (isset($all_permissions)) {
                $all_permissions[$rid] = $search_all[$rid];
            }
        }
        foreach ($permissions as $perm_key => $permission) {
            $t_args = array(
                '!permission' => $search_config_permission[$perm_key]['title']
            );
            $permission = $search_all + $permission;
            if (count($permission)) {
                // We can reduce this down to just the two roles.
                if (isset($permission[AccountInterface::AUTHENTICATED_ROLE])) {
                    $permission = array_intersect_key($all_permissions, $permission);
                }

                if (isset($permission[AccountInterface::AUTHENTICATED_ROLE]) && isset($permission[AccountInterface::ANONYMOUS_ROLE])) {
                    $items[] = $this->t('!permission<br /><strong><em>Everyone</em></strong>', $t_args);
                }
                else {
                    // Remove the note flag on these.
                    if ($perm_key == 'search all content') {
                        $permission = array_intersect_key($roles, $permission);
                    }
                    $t_args['!roles'] = implode(', ', $permission);
                    $items[] = $this->t('!permission<br />!roles', $t_args);
                }
            }
            else {
                $items[] = $this->t('!permission<br /><strong><em>No one</em></strong>', $t_args);
            }
        }

        // Restrictions and a summary of search config permissions
        $form['content_node_search_config']['restrictions'] = array(
            '#type' => 'fieldset',
            '#title' => $this->t('Restrictions'),
            '#collapsible' => TRUE,
            '#collapsed' => TRUE,
            'remove_advanced' => array(
                '#type' => 'checkbox',
                '#title' => $this->t('Remove advanced search unconditionally'),
                '#description' => $this->t('This option removes the advanced search form irrespective of any other setting or user permission.'),
                '#default_value' => $settings['restrictions']['remove_advanced'],
            ),
            'admin_bypass' => array(
                '#type' => 'checkbox',
                '#title' => $this->t('Admin bypass (primary user with id 1)'),
                '#description' => $this->t('This option bypasses all form configuration settings; i.e. the entire advanced form unaltered.'),
                '#default_value' => $settings['restrictions']['admin_bypass'],
            ),
            'type_restrictions' => array(
                '#type' => 'fieldset',
                '#title' => $this->t('Content type permission overview', array('%field' => $label)),
                '#collapsible' => TRUE,
                '#collapsed' => TRUE,
                'permissions' => array(
                        '#markup' => $this->t('This is an overview of what roles can search each content type.') .
                            \Drupal::service('renderer')->render( array( '#type' => 'item_list', '#items' => $items ) ) )
                    . $this->t('<p><strong><sup>*</sup></strong>Note: These users are allowed to search all content items.</p><p>To update these, edit the %module module <a href="!url">permissions</a>.</p>',
                        array('%module' => $this->t('Search configuration'), '!url' => Url::fromRoute(('admin/people/permissions'), array('fragment' => 'module-search_config')))),
            ),
        );
        return $form;
    }
    public function validateForm(array &$form, FormStateInterface $form_state)
    {
        $list = explode("\n", $form['search_config_exclude']['#value']);
        // Pre-tidy the options list.
        $list = array_filter(array_map('trim', $list), 'strlen');

        $values = array();
        $content_types = search_config_content_types();
        $found_types = array();
        $errors = array();
        $empty_pairs = array();
        foreach ($list as $text) {  
            if (preg_match('/([a-z0-9_,\-<>]{1,})\|(.{1,})/', $text, $matches)) {
                $key = trim($matches[1]);
                $value = trim($matches[2]);
                if (empty($key) || empty($value)) {
                    $empty_pairs []= Html::escape($matches[0]);
                }
                else {
                    $found_types = array();
                    $unknown_types = array();
                    $types = array_filter(array_map('trim', explode(',', $key)), 'strlen');

                    foreach ($types as $name) {
                        if (isset($content_types[$name]) || $name == '<other-types>' || $name == '<all-types>') {
                            $found_types[] = $name;
                        }
                        else {
                            $unknown_types[] = $name;
                        }
                    }
                    if (count($unknown_types)) {
                        $errors[] = $this->t('The key contains one or more invalid content types: %types [line: %line]',
                            array('%types' => implode(', ', $unknown_types), '%line' => $matches[0]));
                    }
                    elseif (count($found_types)) {
                        $values[implode(',', $found_types)] = $value;
                    }
                    else {
                        $errors[] = $this->t('No types could be found. [line: %line]',
                            array('%line' => $matches[0]));
                    }
                }
            }
            else {
                $empty_pairs []= Html::escape($text);
            }
        }

        if (!empty($empty_pairs)) {
            $item_list = array(
                '#type' => 'item_list',
                '#items' => $empty_pairs,
            );
            $errors [] = $this->t('Each line must contain a "type|value" pair. Types must contain one or more content type machine codes separated by commas and values must be non-empty strings. This error was seen on the following lines: !list',
                array('!list' => \Drupal::service('renderer')->render($item_list)));
        }
        if (!empty($errors)) {
            $item_list = array(
                '#type' => 'item_list',
                '#items' => $errors,
            );
            $form_state->setErrorByName($form['search_config_exclude'], $this->t('The following errors were detected in the group options: !list',
                array('!list' => \Drupal::service('renderer')->render($item_list))));
        }
        else {
            $form_state->setErrorByName($form['search_config_exclude'], $values);
        }
    }
    public function submitForm(array &$form, FormStateInterface $form_state)
    {
        $form['#submit'][] = 'search_config_search_admin_settings_alter';
        if (isset($form['basic']['submit']) && !empty($labels['basic_submit'])) {
            $form['basic']['submit']['#value'] = t('!search_config:basic_submit', array('!search_config:basic_submit' => $labels['basic_submit']));
        }
        if (isset($form['advanced']['submit']) && !empty($labels['advanced_submit'])) {
            $form['advanced']['submit']['#value'] = t('!search_config:advanced_submit', array('!search_config:advanced_submit' => $labels['advanced_submit']));
        }
    }
}

