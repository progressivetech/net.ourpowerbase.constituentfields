<?php

require_once 'constituentfields.civix.php';
use CRM_Constituentfields_ExtensionUtil as E;

/**
 * Implements hook_civicrm_config().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_config
 */
function constituentfields_civicrm_config(&$config) {
  _constituentfields_civix_civicrm_config($config);
}

/**
 * Implements hook_civicrm_xmlMenu().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_xmlMenu
 */
function constituentfields_civicrm_xmlMenu(&$files) {
  _constituentfields_civix_civicrm_xmlMenu($files);
}

/**
 * Implements hook_civicrm_install().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_install
 */
function constituentfields_civicrm_install() {
  _constituentfields_civix_civicrm_install();
}

/**
 * Implements hook_civicrm_postInstall().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_postInstall
 */
function constituentfields_civicrm_postInstall() {
  _constituentfields_civix_civicrm_postInstall();

  // Via managed entities, we create a group of custom fields. Some of the fields
  // are radio fields that have options, so we ask managed entities to create
  // those options. 
  //
  // However, managed entities cannot assign each custom field to the
  // appropriate option group so we do that manually here.

  $pairs = array(
    'constituentfields_individual_constituent_type' => 'constituentfields_individual_constituent_type_values',
    'constituentfields_organization_constituent_type' => 'constituentfields_organization_constituent_type_values',
    'constituentfields_how_started' => 'constituentfields_how_started_values',
  );

  foreach($pairs as $field_name => $option_group_name) {
    constituentfields_assign_option_group_to_custom_field($field_name, $option_group_name); 
  }

  // In addition, we want to restrict the contact reference field for staff
  // responsible to people in the newly created staff group.
  $params = array('return' => 'id', 'name' => 'constituentfields_staff_group');
  $staff_group_id = civicrm_api3('Group', 'getvalue', $params);

  $field_params = civicrm_api3('CustomField', 'getsingle', array('name' => 'constituentfields_staff_responsible'));
  $field_params['filter'] = 'action=lookup&group=' . intval($staff_group_id);
print_r($field_params);
  civicrm_api3('CustomField', 'create', $field_params);
}

/**
 * Assign option groups to fields
 *
 * @param string $field_name 
 *   string name of the field
 * @param string $option_group_name
 *   string name of option group
 *
 **/
function constituentfields_assign_option_group_to_custom_field($field_name, $option_group_name) {
  $params = array('name' => $option_group_name);
  $option_group = civicrm_api3('option_group', 'getsingle', $params);

  // Get the custom field.
  $params = array('name' => $field_name);
  $field = civicrm_api3('custom_field', 'getsingle', $params); 

  // Update the custom field.
  $field['option_group_id'] = $option_group['id'];
  civicrm_api3('custom_field', 'create', $field);
}

/**
 * Implements hook_civicrm_uninstall().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_uninstall
 */
function constituentfields_civicrm_uninstall() {
  _constituentfields_civix_civicrm_uninstall();
}

/**
 * Implements hook_civicrm_enable().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_enable
 */
function constituentfields_civicrm_enable() {
  _constituentfields_civix_civicrm_enable();
}

/**
 * Implements hook_civicrm_disable().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_disable
 */
function constituentfields_civicrm_disable() {
  _constituentfields_civix_civicrm_disable();
}

/**
 * Implements hook_civicrm_upgrade().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_upgrade
 */
function constituentfields_civicrm_upgrade($op, CRM_Queue_Queue $queue = NULL) {
  return _constituentfields_civix_civicrm_upgrade($op, $queue);
}

/**
 * Implements hook_civicrm_managed().
 *
 * Generate a list of entities to create/deactivate/delete when this module
 * is installed, disabled, uninstalled.
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_managed
 */
function constituentfields_civicrm_managed(&$entities) {
  _constituentfields_civix_civicrm_managed($entities);
}

/**
 * Implements hook_civicrm_caseTypes().
 *
 * Generate a list of case-types.
 *
 * Note: This hook only runs in CiviCRM 4.4+.
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_caseTypes
 */
function constituentfields_civicrm_caseTypes(&$caseTypes) {
  _constituentfields_civix_civicrm_caseTypes($caseTypes);
}

/**
 * Implements hook_civicrm_angularModules().
 *
 * Generate a list of Angular modules.
 *
 * Note: This hook only runs in CiviCRM 4.5+. It may
 * use features only available in v4.6+.
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_angularModules
 */
function constituentfields_civicrm_angularModules(&$angularModules) {
  _constituentfields_civix_civicrm_angularModules($angularModules);
}

/**
 * Implements hook_civicrm_alterSettingsFolders().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_alterSettingsFolders
 */
function constituentfields_civicrm_alterSettingsFolders(&$metaDataFolders = NULL) {
  _constituentfields_civix_civicrm_alterSettingsFolders($metaDataFolders);
}

/**
 * Implements hook_civicrm_entityTypes().
 *
 * Declare entity types provided by this module.
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_entityTypes
 */
function constituentfields_civicrm_entityTypes(&$entityTypes) {
  _constituentfields_civix_civicrm_entityTypes($entityTypes);
}

// --- Functions below this ship commented out. Uncomment as required. ---

/**
 * Implements hook_civicrm_preProcess().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_preProcess
 *
function constituentfields_civicrm_preProcess($formName, &$form) {

} // */

/**
 * Implements hook_civicrm_navigationMenu().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_navigationMenu
 *
function constituentfields_civicrm_navigationMenu(&$menu) {
  _constituentfields_civix_insert_navigation_menu($menu, 'Mailings', array(
    'label' => E::ts('New subliminal message'),
    'name' => 'mailing_subliminal_message',
    'url' => 'civicrm/mailing/subliminal',
    'permission' => 'access CiviMail',
    'operator' => 'OR',
    'separator' => 0,
  ));
  _constituentfields_civix_navigationMenu($menu);
} // */
