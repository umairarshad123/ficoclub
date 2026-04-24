<?php

return [
    'enabled' => env('DISPUTEFOX_ENABLED', false),

    'url' => env('DISPUTEFOX_URL', 'https://pulse.disputeprocess.com/CustumFieldController'),
    'method' => env('DISPUTEFOX_METHOD', 'addWebFormData'),

    'tab_info_id' => env('DISPUTEFOX_TAB_INFO_ID'),
    'redirect_url' => env('DISPUTEFOX_REDIRECT_URL', ''),
    'company_id' => env('DISPUTEFOX_COMPANY_ID'),
    'cust_type' => env('DISPUTEFOX_CUST_TYPE', 3),
    'add_affiliate_flag' => env('DISPUTEFOX_ADD_AFFILIATE_FLAG', 0),
    'assignedto_id' => env('DISPUTEFOX_ASSIGNEDTO_ID', -1),
    'sales_representative_id' => env('DISPUTEFOX_SALES_REPRESENTATIVE_ID', -1),
    'workflow_statusid' => env('DISPUTEFOX_WORKFLOW_STATUSID', -1),
    'folder_statusid' => env('DISPUTEFOX_FOLDER_STATUSID', -1),
    'customer_statusid' => env('DISPUTEFOX_CUSTOMER_STATUSID', -1),
    'portalAccess' => env('DISPUTEFOX_PORTAL_ACCESS', 0),
    'customerAgreementIDs' => env('DISPUTEFOX_CUSTOMER_AGREEMENT_IDS'),
];