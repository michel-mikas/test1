<?php
namespace App\Http\libs;

use Session;

class Route_Structure {
    
    // routes allowed

    public function get_front_pages_routes() {
        $routes = array(
            array(
                'route' => '/',
                'request_method' => 'get',
                'controller' => 'Front_Pages\PagesController',
                'method' => 'home',
            ),
            array(
                'route' => 'admin/login',
                'request_method' => 'get',
                'controller' => 'Front_Pages\PagesController',
                'method' => 'login_form',
            ),
            array(
                'route' => 'admin/login/auth',
                'request_method' => 'post',
                'controller' => 'AuthController',
                'method' => 'login_auth',
            ),
            array(
                'route' => 'make-order/{id}',
                'request_method' => 'get',
                'controller' => 'Front_Pages\PagesController',
                'method' => 'make_order',
            ),
            array(
                'route' => 'post/order',
                'request_method' => 'post',
                'controller' => 'Front_Pages\PagesController',
                'method' => 'post_order',
            ),
        );
        return $routes;
    }



    // USER ROUTES



    public function get_user_routes() {
        $routes = array(
            array(
                'route' => '/test_page',
                'request_method' => 'get',
                'controller' => 'Front_Pages\UserController',
                'method' => 'test_page',
            ),
        );
        return $routes;
    }

    public function get_admin_routes() {


        // PREFIXED AS 'admin'


        $routes = array(

/**************************************************************************

                                //GENERAL

**************************************************************************/

            array(
                'route' => '/dashboard',
                'request_method' => 'get',
                'controller' => 'Admin_Panel\LandingController',
                'method' => 'home',
            ),
            array(
                'route' => '/logout',
                'request_method' => 'get',
                'controller' => 'AuthController',
                'method' => 'logout',
            ),

/**************************************************************************

                                //ADMINS

**************************************************************************/


			array(
                'route' => '/profile',
                'request_method' => 'get',
                'controller' => 'Admin_Panel\AdminsController',
                'method' => 'profile_edit',
            ),
            array(
                'route' => '/admins/all',
                'request_method' => 'get',
                'controller' => 'Admin_Panel\AdminsController',
                'method' => 'get_all',
            ),
            array(
                'route' => '/admins/add',
                'request_method' => 'get',
                'controller' => 'Admin_Panel\AdminsController',
                'method' => 'add',
            ),
            array(
                'route' => '/admins/edit/{}',
                'request_method' => 'get',
                'controller' => 'Admin_Panel\AdminsController',
                'method' => 'edit',
            ),
			array(
                'route' => '/admins/view/{}',
                'request_method' => 'get',
                'controller' => 'Admin_Panel\AdminsController',
                'method' => 'edit',
            ),
			array(
                'route' => '/admins/post_edit',
                'request_method' => 'post',
                'controller' => 'Admin_Panel\AdminsController',
                'method' => 'post_edit',
            ),
            array(
                'route' => '/admins/post_add',
                'request_method' => 'post',
                'controller' => 'Admin_Panel\AdminsController',
                'method' => 'post_add',
            ),
            array(
                'route' => '/admins/delete/{id}',
                'request_method' => 'get',
                'controller' => 'Admin_Panel\AdminsController',
                'method' => 'delete',
            ),
            array(
                'route' => '/admins/restore/{id}',
                'request_method' => 'get',
                'controller' => 'Admin_Panel\AdminsController',
                'method' => 'restore',
            ),


/**************************************************************************

                                //USERS

**************************************************************************/

			array(
                'route' => '/customers/all',
                'request_method' => 'get',
                'controller' => 'Admin_Panel\CustomersController',
                'method' => 'get_all'
            ),
            array(
                'route' => '/customers/view/{id}',
                'request_method' => 'get',
                'controller' => 'Admin_Panel\CustomersController',
                'method' => 'view_item',
            ),






/**************************************************************************

                                //ORDERS

**************************************************************************/

            array(
                'route' => '/orders/all',
                'request_method' => 'get',
                'controller' => 'Admin_Panel\OrdersController',
                'method' => 'get_all'
            ),
            array(
                'route' => '/orders/view/{id}',
                'request_method' => 'get',
                'controller' => 'Admin_Panel\OrdersController',
                'method' => 'view_item',
            ),



/**************************************************************************

                                //PRODUCTS

**************************************************************************/

            array(
                'route' => '/products/all',
                'request_method' => 'get',
                'controller' => 'Admin_Panel\ProductsController',
                'method' => 'get_all'
            ),
            array(
                'route' => '/products/view/{id}',
                'request_method' => 'get',
                'controller' => 'Admin_Panel\ProductsController',
                'method' => 'view_item',
            ),
            array(
                'route' => '/products/add',
                'request_method' => 'get',
                'controller' => 'Admin_Panel\ProductsController',
                'method' => 'add',
            ),
            array(
                'route' => 'products/post_add',
                'request_method' => 'post',
                'controller' => 'Admin_Panel\ProductsController',
                'method' => 'post_add',
            ),
            array(
                'route' => '/products/delete/{id}',
                'request_method' => 'get',
                'controller' => 'Admin_Panel\ProductsController',
                'method' => 'delete',
            ),
            array(
                'route' => '/products/restore/{id}',
                'request_method' => 'get',
                'controller' => 'Admin_Panel\ProductsController',
                'method' => 'restore',
            ),


        );
        return $routes;
    }
}