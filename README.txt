Search Configuration module: https://www.drupal.org/project/search_config
=======================================================

DESCRIPTION
===========
The basic functionality of this module is to configure the search forms so as to give an easy user interface for the
users during the search mechanism.
The key features are listed below:

* Remove the basic search form if advanced search form is present.
* Move the basic keyword search to advanced search form.
* Options to override advanced forms fieldset.
* Options to specify the relative positions of the field labels. i.e, either the default or below the field or in the
  hidden state.
* Options to alter the field labels of the basic and the advanced search forms.
* Options to select the users who can access various fields.
* Replace the type options with custom type groups.
* Options to alter the pager limit.

This module has been ported to Drupal 8 as part of the Google Summer of Code 2016 project.

PREREQUISITES
=============
Search module (Drupal core).

INSTALLATION
============
Standard module installation.

See https://www.drupal.org/docs/8/extending-drupal/installing-contributed-modules for further information.

CONFIGURATION
=============

After installing the module, navigate to the module page located in admin/config/search/pages/. You can basically see
two configuration options: Labels and string overrides and Additional node search configuration. The labels and string
overrides is to configure the field labels. You have options to alter the field titles of the basic and advanced search
forms. You also have the liberty to specify the relative position of the field title. It can be above the field (the
default format), below the field or in the hidden state.
Additional node search configuration configures the additional features and make this module more lively. Once you
select the additional node search configuration option, you have the options to deal with form control, removing
... keyword options, selecting the roles who can see the fields, replacing type option with custom type groups and
setting the pager limit.
Form control allows you to configure the search form in such a way that only one form is displayed at a given instant.
This is achieved by selecting the checkbox 'Only show one form at a time.' in the form control. Another option is to
move the basic keyword search to advanced search form. You can also select the roles who can view particular fieldset.
There are basically three types of roles; Anonymous user, Authenticated user and the Administrator. You could set the
privileges from this search configuration tool.
You can also replace the type option available with custom type groups. i.e, you can search based on content types of
your choice. This is done by adding the required content type in the field 'Replace type options with custom type
groups'.
You could also configure the maximum number of results to be displayed in a page. You can select the value of your
choice from the drop down menu of 'Pager limit' in the results section.

Functions used in the module:

* function search_config_form_search_admin_settings_alter(&$form, $form_state)
    This is the main function for the creation of the forms in search configuration string overrides and the additional
    node search configuration. This gives the characteristic features of the forms required, the default value to be
    added and also makes use of the permissions assigned to various roles. The 'search_config_string_overrides'
    generates the string overrides form and 'content_node_search_config' deals with the additional node search
    configuration.

* function search_config_search_admin_settings_submit(&$form, &$form_state)
   This function comes into action on submission of the configuration forms. It saves the contents of the form using the
   Drupal configuration service. It also access the search_config.node_content_settings configuration file to edit the
   details as per the user entry.

* function element_node_search_config_groupings_validate($element, FormStateInterface &$form_state)
   This function validates the data entered by the user in the search configuration forms. It makes use of the regular
   expressions. Furthermore, it sets errors if any unusual behaviour is reported.

* function search_config_node_settings() and function search_config_string_overrides($key = NULL)
   This function is for loading the default settings. Ensuring the default values are added to the form attributes. It
   makes use of the Drupal configuration API.

* function search_config_content_types()
   This function returns an array of the names of the node types available.
   return array_map('Drupal\Component\Utility\Html::escape', node_type_get_names());

* function search_config_get_roles_by_permission($permission)
   This helper function returns roles with the given permission.
     foreach (array(
                  AccountInterface::ANONYMOUS_ROLE,
                  AccountInterface::AUTHENTICATED_ROLE
                ) as $rid) {
         return $rid->hasPermission($permission);
       }

* function search_config_get_access($remove, $grant)
   This helper function is to test the users whether they have been configured the right access to the fields.
     $user = \Drupal::currentUser();
       if ($remove) {
         return (bool) array_intersect_key($user->getRoles(), array_filter($grant));
       }
       return TRUE;

MAINTAINERS
===========
Karthik Kumar D K  <https://www.drupal.org/u/heykarthikwithu>
Naveen Valecha     <https://www.drupal.org/u/naveenvalecha>
Neetu Morwani      <https://www.drupal.org/u/neetu-morwani>
Joyce George       <https://www.drupal.org/u/joyceg>
