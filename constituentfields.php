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
  try {
    $staff_group_id = civicrm_api3('Group', 'getvalue', $params);
    $field_params = civicrm_api3('CustomField', 'getsingle', array('name' => 'constituentfields_staff_responsible'));
    $field_params['filter'] = 'action=lookup&group=' . intval($staff_group_id);
    civicrm_api3('CustomField', 'create', $field_params);
  }
  catch(CiviCRM_API3_Exception $e) {
    if ($e->getMessage() == 'Expected one Group but found 0') {
      // If we can't find the group we created it may mean the database
      // already had a staff group, in which case we don't want to
      // mess with it. This is a no-op
    }
    else {
      throw $e;
    }
  } 
  constituentfields_create_profiles();
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

  try {
    $field = civicrm_api3('custom_field', 'getsingle', $params); 
    // Update the custom field.
    $field['option_group_id'] = $option_group['id'];
    civicrm_api3('custom_field', 'create', $field);
  }
  catch(CiviCRM_API3_Exception $e) {
    if ($e->getMessage() == 'Expected one CustomField but found 0') {
      // If we can't locate the custom field, it might mean they have disabled
      // it, deleted it or it never existed in the first place. That's ok.
      return;
    }
  }
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
  constituentfields_transfer_civicrm_engage_entities();
}

/**
 * Transfer civicrm_engage profiles.
 *
 * Before enabling this module, ensure that all profiles handled by
 * civicrm_engage will now be taken over by this extension.
 **/
function constituentfields_transfer_civicrm_engage_entities() {
  

  $custom_groups = array(
    'Constituent_Info_Individuals' => 'constituentfields_individual_fields',
    'Constituent_Info_Organizations' => 'constituentfields_organization_fields',
  );
  foreach ($custom_groups as $old_name => $new_name) {
    $results = civicrm_api3('CustomGroup', 'get', array('name' => $old_name));
    if ($results['count'] > 0) {
      $id = $results['id'];
      $sql = "UPDATE civicrm_custom_group SET name = %0 WHERE id = %1";
      $params = array(0 => array($new_name, 'String'), 1 => array($id, 'Integer'));
      CRM_Core_DAO::executeQuery($sql, $params);

      $sql = "INSERT INTO civicrm_managed SET module = 'net.ourpowerbase.constituentfields',
        name = %0, entity_type = 'CustomGroup', entity_id = %1";
      CRM_Core_DAO::executeQuery($sql, $params);
    }
  }

  // Now check for staff. For some reason the "Title" is unique in the civicrm_group table
  // so we can have two.
  $results = civicrm_api3('Group', 'get', array('title' => 'staff'));
  if ($results['count'] == '1') {
    $sql = "INSERT INTO civicrm_managed SET module = 'net.ourpowerbase.constituentfields',
        name = 'constituentfields_staff', entity_type = 'Group', entity_id = %0";
    $params = array(
      0 => array($results['id'], 'Integer')
    );
    CRM_Core_DAO::executeQuery($sql, $params);
  }

}

/**
 * Create profiles using our custom fields.
 *
 * Unfortunatley, api3 can't create profiles with custom fields
 * because the api depends on the id of the custom field. We
 * can't predict what that id is, so instead using the api and
 * managed entities, we create our profile here.
 */
function constituentfields_create_profiles() {
  $old_name ='update_constituent_info';
  $new_name = 'constituentfields_update_constituentfields_profile';
  $results = civicrm_api3('UFGroup', 'get', array('name' => $old_name));
  if ($results['count'] > 0) {
    // This means the profile already exists. We are going to rename it so
    // we have consistent naming of this extensions entities. 
    $uf_group_id = $results['id'];
    $params = array_pop($results['values']);
    $params['name'] = $new_name;
    civicrm_api3('UFGroup', 'create', $params);
  }
  else {
    // This profile does not already exist. Let's create it.
    $params = array(
      'name' => $new_name,
      'title' => 'Update Constuent Information',
      'description' => 'Powerbase profile for updating multiple contacts constituent information',
      'is_active' => 1,
      'is_update_dupe' => '1',
    );
    $result = civicrm_api3('UFGroup', 'create', $params);
    $uf_group_id = $result['id'];
    $template_params = array(
      'uf_group_id' => $uf_group_id, 
      'is_active' => '1',
      'is_view' => '0',
      'is_required' => '0',
      'weight' => '10',
      'visibility' => 'User and User Admin Only',
      'label' => 'Constituent Type',
      'field_type' => 'Individual',
    );
    
    $fields = array(
      'constituentfields_individual_constituent_type' => array(),
      'constituentfields_staff_responsible' => array(),
      'constituentfields_individual_date_started' => array(),
      'constituentfields_individual_how_started' => array(),
    );
    foreach ($fields as $field_name => $props) {
      // Get the custom id of the field we want.
      $result = civicrm_api3('CustomField', 'get', array('name' => $field_name));
      if ($result['count'] > 0) {
        $id = $result['id'];
        $params = $template_params;
        $params['field_name'] = 'custom_' . $id;
        civicrm_api3('UFField', 'create', $params);
      }
    }
  }

  // Lastly insert into managed entities so we can disable and remove
  // if this extension is disabled and removed.
  $sql = "INSERT INTO civicrm_managed SET module = 'net.ourpowerbase.constituentfields',
      name = %0, entity_type = 'UFGroup', entity_id = %1";
  CRM_Core_DAO::executeQuery($sql, array(0 => array($new_name, 'String'), array($uf_group_id, 'Integer')));
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
