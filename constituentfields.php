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
    'constituentfields_leadership_level' => 'constituentfields_leadership_level_values',
    'constituentfields_known_languages' => 'constituentfields_known_languages_values',
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
    if ($field_params['serialize'] == 0) {
      // Fix bug unlikely to be fixed (we should move this code to api4)
      $field_params['serialize'] = '';
    }
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

  // For backward compatility, rename the old version of this profile.
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

  // We add some special dynamic code to the managed hook call. So, we
  // have to trigger a fresh reconciliation at the end of installation
  // to ensure everything is properly created.
  CRM_Core_ManagedEntities::singleton(TRUE)->reconcile();
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
  // We dynamically add our profile because api3 doesn't support adding
  // custom fields using the name, we can only add them using their id
  // and the id will change on every installation.
  $profile  = array(
    'name' => 'constituentfields_update_constituentfields_profile',
    'entity' => 'UFGroup',
    'update' => 'never',
    'module' => 'net.ourpowerbase.constituentfields',
    'params' => array(
      'version' => 3,
      'title' => 'Update Constuent Information',
      'description' => 'Powerbase profile for updating multiple contacts constituent information',
      'is_active' => 1,
      'name' => 'constituentfields_update_constituentfields_profile',
    ),
  );

  $fields = array(
      'constituentfields_individual_constituent_type',
      'constituentfields_staff_responsible',
      'constituentfields_date_started',
      'constituentfields_how_started',
  );

  $profile_fields = array();
  $weight = 0;
  foreach ($fields as $field_name) {
    // Get the custom id of the field we want.
    $result = civicrm_api3('CustomField', 'get', array('name' => $field_name));
    if ($result['count'] > 0) {
      $id = $result['id'];
      $values = array_pop($result['values']);
      $label = $values['label'];
      $profile_fields[] = array(
        'uf_group_id' => '$value.id',
        'field_name' => 'custom_' . $id,
        'is_active' => 1,
        'label' => $label,
        'field_type' => 'Individual',
        "weight" => 10 + $weight,
        "in_selector" => "1",
        "visibility" => "Public Pages and Listings",
      );
    }
  }
  // Depending on timing, the custom fields may not yet be created.
  // If that's the case, don't add this at all - we want to wait
  // until we have all the pieces before we add it because we have
  // update set to never.
  if (count($profile_fields) > 0) {
    $profile['params']['api.uf_field.create'] = $profile_fields;
    $entities[] = $profile;
  }

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


function constituentfields_civicrm_buildForm($formName, &$form) {
  // We want the custom data field Date Started to default to todays date
  $start_date_forms = array('CRM_Contact_Form_Contact', 'CRM_Profile_Form_Edit');
  if (in_array($formName, $start_date_forms)) {
    $id = \Civi\Api4\CustomField::get()
      // This might be called on a public profile.
      ->setCheckPermissions(FALSE)
      ->addWhere('name', '=', 'constituentfields_date_started')
      ->addSelect('id')
      ->execute()->first()['id'];
    if ($id) {
      $date = date('Y-m-d');
      $field = 'custom_' . $id;
      if($formName == 'CRM_Contact_Form_Contact' ) {
        // the new contact form appends -1
        $field .= '_-1';
      }
      $defaults[$field] = $date;
      $field_display = $field . '_display';
      $defaults[$field_display] = date('m/d/Y');
      $form->setDefaults( $defaults );
    }
  }
}
