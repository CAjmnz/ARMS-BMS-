<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/*
| -------------------------------------------------------------------------
| URI ROUTING
| -------------------------------------------------------------------------
| This file lets you re-map URI requests to specific controller functions.
|
| Typically there is a one-to-one relationship between a URL string
| and its corresponding controller class/method. The segments in a
| URL normally follow this pattern:
|
|	example.com/class/method/id/
|
| In some instances, however, you may want to remap this relationship
| so that a different class/function is called than the one
| corresponding to the URL.
|
| Please see the user guide for complete details:
|
|	https://codeigniter.com/userguide3/general/routing.html
|
| -------------------------------------------------------------------------
| RESERVED ROUTES
| -------------------------------------------------------------------------
|
| There are three reserved routes:
|
|	$route['default_controller'] = 'welcome';
|
| This route indicates which controller class should be loaded if the
| URI contains no data. In the above example, the "welcome" class
| would be loaded.
|
|	$route['404_override'] = 'errors/page_missing';
|
| This route will tell the Router which controller/method to use if those
| provided in the URL cannot be matched to a valid route.
|
|	$route['translate_uri_dashes'] = FALSE;
|
| This is not exactly a route, but allows you to automatically route
| controller and method names that contain dashes. '-' isn't a valid
| class or method name character, so it requires translation.
| When you set this option to TRUE, it will replace ALL dashes in the
| controller and method URI segments.
|
| Examples:	my-controller/index	-> my_controller/index
|		my-controller/my-method	-> my_controller/my_method
*/
//auth routes
$route['default_controller'] = 'auth';
$route['auth']               = 'auth/index';
$route['auth/login']         = 'auth/login';
$route['auth/logout']        = 'auth/logout';
//dashboard routes
$route['dashboard']          = 'dashboard/index';
$route['dashboard/chart_categories'] = 'dashboard/chart_categories';
$route['dashboard/chart_trend']      = 'dashboard/chart_trend';
//items routes
$route['items']              = 'items/index';
$route['items/ajax_list']    = 'items/ajax_list';
$route['items/get/(:any)'] = 'items/get/$1';
$route['items/repair_statuses'] = 'items/repair_statuses'; 
$route['items/repair_quantities'] = 'items/repair_quantities';  
//itemized
$route['itemized']         = 'itemized/index';
$route['itemized/ajax_list'] = 'itemized/ajax_list';
$route['itemized/store'] = 'itemized/store';
$route['itemized/get/(:any)'] = 'itemized/get/$1';
$route['itemized/update/(:any)'] = 'itemized/update/$1';
$route['itemized/delete/(:any)'] = 'itemized/delete/$1';
$route['itemized/bulk_delete'] = 'itemized/bulk_delete';
//borrowing
$route['borrowing']            = 'Borrowing/index';
$route['borrowing/ajax_list']  = 'Borrowing/ajax_list';
$route['borrowing/get_available_units/(:any)'] = 'Borrowing/get_available_units/$1';
$route['borrowing/store']      = 'Borrowing/store';
$route['borrowing/get_item/(:any)']     = 'Borrowing/get_item/$1';
$route['borrowing/mark_returned/(:any)'] = 'Borrowing/mark_returned/$1';
//return
$route['returns']            = 'Return_c/index';
$route['returns/ajax_list']  = 'Return_c/ajax_list';
$route['returns/get_details/(:any)'] = 'Return_c/get_details/$1';

$route['items/sync_itemized'] = 'items/sync_itemized';

$route['404_override'] = '';
$route['translate_uri_dashes'] = FALSE;
