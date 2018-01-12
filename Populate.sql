INSERT INTO con_contact_point_types (
  CPT_NAME,
  CPT_DESCRIPTION,
  CPT_UPDATED)
VALUES (
  'Telephone',
  'The contact point is a telephone number',
  '2016-11-21');

INSERT INTO con_contact_point_types (
  CPT_NAME,
  CPT_DESCRIPTION,
  CPT_UPDATED)
VALUES (
  'Mobile',
  'The contact is a mobile telephone',
  '2016-11-21');

INSERT INTO con_contact_point_types (
  CPT_NAME,
  CPT_DESCRIPTION,
  CPT_UPDATED)
VALUES (
  'Email',
  'The contact is an email address',
  '2016-11-21');

INSERT INTO con_contact_types (
  CTT_NAME,
  CTT_DESCRIPTION,
  CTT_UPDATED)
VALUES (
  'Personal',
  'The contact relates to an individual',
  current_timestamp());

INSERT INTO con_contact_types (
  CTT_NAME,
  CTT_DESCRIPTION,
  CTT_UPDATED)
VALUES (
  'Business',
  'The contact relates to a business or an individuals business details',
  current_timestamp());

INSERT INTO con_contact_types (
  CTT_NAME,
  CTT_DESCRIPTION,
  CTT_UPDATED)
VALUES (
  'Emergency',
  'The contact should be used to notify someone of a serious incident',
  current_timestamp());

insert into con_titles (
    TIT_TITLE,
    TIT_UPDATED)
  VALUES (
    'Mr',
    '2016-11-21');

insert into con_titles (
  TIT_TITLE,
  TIT_UPDATED)
VALUES (
  'Miss',
  '2016-11-21');

insert into con_titles (
  TIT_TITLE,
  TIT_UPDATED)

VALUES (
  'Mrs',
  '2016-11-21');

insert into con_titles (
  TIT_TITLE,
  TIT_UPDATED)

VALUES (
  'Ms',
  '2016-11-21');

insert into con_titles (
  TIT_TITLE,
  TIT_UPDATED)
VALUES (
  'Doctor',
  '2016-11-21');

insert into con_titles (
  TIT_TITLE,
  TIT_UPDATED)

VALUES (
  'Sir',
  '2016-11-21');

insert into con_titles (
  TIT_TITLE,
  TIT_UPDATED)
VALUES (
  'Dame',
  '2016-11-21');

insert into con_titles (
  TIT_TITLE,
  TIT_UPDATED)
VALUES (
  'Lord',
  '2016-11-21');

insert into con_titles (
  TIT_TITLE,
  TIT_UPDATED)
VALUES (
  'Lady',
  '2016-11-21');

INSERT INTO lnk_link_types (
  LTP_ID,
  LTP_NAME,
  LTP_TABLE,
  LTP_ID_FIELD,
  LTP_UPDATED)
VALUES (
  1,
  'People',
  'con_people',
  'PER_ID',
  '2016-11-21');

INSERT INTO lnk_link_types (
  LTP_ID,
  LTP_NAME,
  LTP_TABLE,
  LTP_ID_FIELD,
  LTP_UPDATED)
VALUES (
  2,
  'Addresses',
  'con_addresses',
  'ADR_ID',
  '2016-11-21');

INSERT INTO lnk_link_types (
  LTP_ID,
  LTP_NAME,
  LTP_TABLE,
  LTP_ID_FIELD,
  LTP_UPDATED)
VALUES (
  3,
  'Contact Points',
  'con_contact_points',
  'CNP_ID',
  '2016-11-21');

INSERT INTO lnk_link_types (
  LTP_ID,
  LTP_NAME,
  LTP_TABLE,
  LTP_ID_FIELD,
  LTP_UPDATED)
VALUES (
  4,
  'Groups',
  'grp_groups',
  'GRP_ID',
  '2016-11-21');

INSERT INTO lnk_link_types (
  LTP_ID,
  LTP_NAME,
  LTP_TABLE,
  LTP_ID_FIELD,
  LTP_UPDATED)
VALUES (
  5,
  'Projects/Tasks',
  'pro_tasks',
  'TSK_ID',
  '2016-11-21');

INSERT INTO lnk_link_types (
  LTP_ID,
  LTP_NAME,
  LTP_TABLE,
  LTP_ID_FIELD,
  LTP_UPDATED)
VALUES (
  6,
  'Web Accounts',
  'web_accounts',
  'WAC_ID',
  '2016-12-06');

INSERT INTO lnk_link_types (
  LTP_ID,
  LTP_NAME,
  LTP_TABLE,
  LTP_ID_FIELD,
  LTP_UPDATED)
VALUES (
  7,
  'Systems',
  'sas_sytems',
  'SYS_ID',
  '2016-12-06');

INSERT INTO lnk_link_types (
  LTP_ID,
  LTP_NAME,
  LTP_TABLE,
  LTP_ID_FIELD,
  LTP_UPDATED)
VALUES (
  8,
  'Schedules',
  'sch_schedule_items',
  'SCI_ID',
  '2016-12-29');

INSERT INTO lnk_link_types (
  LTP_ID,
  LTP_NAME,
  LTP_TABLE,
  LTP_ID_FIELD,
  LTP_UPDATED)
VALUES (
  9,
  'Users',
  'usr_users',
  'USR_ID',
  '2017-01-03');

INSERT INTO lnk_link_type_avail (
  LTA_ONR_LTP_ID,
  LTA_CHD_LTP_ID,
  LTA_DESCRIPTION,
  LTA_UPDATED)
VALUES (
  4,
  1,
  'Allows groups to link to people',
  '2016-11-25');

INSERT INTO lnk_link_type_avail (
  LTA_ONR_LTP_ID,
  LTA_CHD_LTP_ID,
  LTA_DESCRIPTION,
  LTA_UPDATED)
VALUES (
  5,
  1,
  'Allows projects and tasks to link to people',
  '2016-11-25');

INSERT INTO lnk_link_type_avail (
  LTA_ONR_LTP_ID,
  LTA_CHD_LTP_ID,
  LTA_DESCRIPTION,
  LTA_UPDATED)
VALUES (
  1,
  2,
  'Allows people to link to addresses',
  '2016-11-25');

INSERT INTO lnk_link_type_avail (
  LTA_ONR_LTP_ID,
  LTA_CHD_LTP_ID,
  LTA_DESCRIPTION,
  LTA_UPDATED)
VALUES (
  4,
  2,
  'Allows groups to link to addresses',
  '2016-11-25');

INSERT INTO lnk_link_type_avail (
  LTA_ONR_LTP_ID,
  LTA_CHD_LTP_ID,
  LTA_DESCRIPTION,
  LTA_UPDATED)
VALUES (
  1,
  3,
  'Allows people to link to contact points',
  '2016-11-25');

INSERT INTO lnk_link_type_avail (
  LTA_ONR_LTP_ID,
  LTA_CHD_LTP_ID,
  LTA_DESCRIPTION,
  LTA_UPDATED)
VALUES (
  4,
  3,
  'Allows groups to link to contact points',
  '2016-11-25');

INSERT INTO lnk_link_type_avail (
  LTA_ONR_LTP_ID,
  LTA_CHD_LTP_ID,
  LTA_DESCRIPTION,
  LTA_UPDATED)
VALUES (
  4,
  4,
  'Allows a group to encompass other groups',
  '2016-11-25');

INSERT INTO lnk_link_type_avail (
  LTA_ONR_LTP_ID,
  LTA_CHD_LTP_ID,
  LTA_DESCRIPTION,
  LTA_UPDATED)
VALUES (
  1,
  5,
  'Allows people to link to projects/tasks',
  '2016-11-25');

INSERT INTO lnk_link_type_avail (
  LTA_ONR_LTP_ID,
  LTA_CHD_LTP_ID,
  LTA_DESCRIPTION,
  LTA_UPDATED)
VALUES (
  4,
  5,
  'Allows groups to link to projects/tasks',
  '2016-11-25');

INSERT INTO lnk_link_type_avail (
  LTA_ONR_LTP_ID,
  LTA_CHD_LTP_ID,
  LTA_DESCRIPTION,
  LTA_UPDATED)
VALUES (
  6,
  1,
  'Allows Web Accounts to link to People',
  '2016-12-06');

INSERT INTO pro_task_types (
  TST_NAME,
  TST_DESCRIPTION,
  TST_SUPPORT_SUB,
  TST_UPDATED)
VALUES (
  'Project',
  'A significant project which supports multiple sub-tasks',
  1,
  '2016-12-04');

INSERT INTO pro_task_states (
  TSS_NAME,
  TSS_DESCRIPTION,
  TSS_COMPLETE,
  TSS_UPDATED)
VALUES (
  'Created',
  'The task has been freshly created with no action yet taken',
  0,
  '2016-12-04');

INSERT INTO pro_task_states (
  TSS_NAME,
  TSS_DESCRIPTION,
  TSS_COMPLETE,
  TSS_UPDATED)
VALUES (
  'Assigned',
  'The task has been assigned to someone for attention',
  0,
  '2016-12-04');

INSERT INTO pro_task_states (
  TSS_NAME,
  TSS_DESCRIPTION,
  TSS_COMPLETE,
  TSS_UPDATED)
VALUES (
  'In Progress',
  'The task is being worked on',
  0,
  '2016-12-04');

INSERT INTO pro_task_states (
  TSS_NAME,
  TSS_DESCRIPTION,
  TSS_COMPLETE,
  TSS_UPDATED)
VALUES (
  'Complete',
  'The task is finished',
  1,
  '2016-12-04');

INSERT INTO pro_task_states (
  TSS_NAME,
  TSS_DESCRIPTION,
  TSS_COMPLETE,
  TSS_UPDATED)
VALUES (
  'Rejected',
  'The task is not going to be done',
  1,
  '2016-12-04');

INSERT INTO usr_access_areas (
  ACA_ID,
  ACA_NAME,
  ACA_DESCRIPTION,
  ACA_UPDATED)
VALUES (
  0,
  'Default',
  'Set default access levels for role if access area is not specified',
  '2017-01-04');

INSERT INTO usr_access_areas (
  ACA_ID,
  ACA_NAME,
  ACA_DESCRIPTION,
  ACA_UPDATED)
VALUES (
  1,
  'Contacts',
  'Control access level for contact information',
  '2017-01-04');

INSERT INTO usr_access_areas (
  ACA_ID,
  ACA_NAME,
  ACA_DESCRIPTION,
  ACA_UPDATED)
VALUES (
  2,
  'Groups',
  'Control access level for groups',
  '2017-01-04');

INSERT INTO usr_access_areas (
  ACA_ID,
  ACA_NAME,
  ACA_DESCRIPTION,
  ACA_UPDATED)
VALUES (
  3,
  'Projects/Tasks',
  'Control access level for projects/tasks',
  '2017-01-04');

INSERT INTO usr_access_areas (
  ACA_ID,
  ACA_NAME,
  ACA_DESCRIPTION,
  ACA_UPDATED)
VALUES (
  4,
  'Requirements/Fulfillments',
  'Control access level for requirements and fulfillments',
  '2017-01-04');

INSERT INTO usr_access_areas (
  ACA_ID,
  ACA_NAME,
  ACA_DESCRIPTION,
  ACA_UPDATED)
VALUES (
  5,
  'Scheduling',
  'Control access level for scheduling',
  '2017-01-04');

INSERT INTO usr_access_areas (
  ACA_ID,
  ACA_NAME,
  ACA_DESCRIPTION,
  ACA_UPDATED)
VALUES (
  6,
  'Users',
  'Control access level for users',
  '2017-01-04');

INSERT INTO usr_access_areas (
  ACA_ID,
  ACA_NAME,
  ACA_DESCRIPTION,
  ACA_UPDATED)
VALUES (
  7,
  'Linking',
  'Control access linking system config',
  '2017-01-04');

INSERT INTO usr_access_areas (
  ACA_ID,
  ACA_NAME,
  ACA_DESCRIPTION,
  ACA_UPDATED)
VALUES (
  8,
  'Systems',
  'Control access system designer',
  '2017-01-24');

/*XCal - Now user role and admin setup*/
INSERT INTO usr_roles (
    ROL_ID,
    ROL_NAME,
    ROL_DESCRIPTION,
    ROL_UPDATED)
VALUES (
    1,
    'Administrator',
    'Defaults full access to all system areas',
    current_timestamp());

INSERT INTO usr_role_access (
    RAC_ROL_ID,
    RAC_ACA_ID,
    RAC_SEARCHVIEW,
    RAC_NEWMOD,
    RAC_REMOVE,
    RAC_CONFIG,
    RAC_UPDATED)
VALUES (
    1,
    0,
    2,
    2,
    2,
    1,
    current_timestamp());

INSERT INTO usr_users (
  USR_ID,
  USR_USERNAME,
  USR_PASSHASH,
  USR_UPDATED)
VALUES (
  1,
  'Admin',
  '9af336941f2003f637eb9f5eac928ff0e4efe2a4846a9d375d4434ae39df68a3',
  current_timestamp());

INSERT INTO usr_user_roles (
  URL_USR_ID,
  URL_ROL_ID,
  URL_UPDATED)
VALUES (
  1,
  1,
  current_timestamp());

INSERT INTO web_accounts (
  WAC_USERNAME,
  WAC_PASSHASH,
  WAC_ACTIVE,
  WAC_USR_ID,
  WAC_UPDATED)
VALUES (
  'admin',
  '9af336941f2003f637eb9f5eac928ff0e4efe2a4846a9d375d4434ae39df68a3',
  1,
  1,
  current_timestamp());
