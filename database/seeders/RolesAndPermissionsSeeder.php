<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolesAndPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // All the permissions
        $permissions = [
            'manage_roles_permissions',
            'add_users',
            'edit_users',
            'remove_users',
            'purchase_domain',
            'create_neighborhoods_maps',
            'create_rel_zipcode_list',
            'get_phone_number_link_to_ringba',
            'create_services_pages',
            'add_to_lead_gen_portal',
            'create_logo', 'request_gbp',
            'create_blog_pages',
            'create_geotag_images',
            'send_messages_to_affiliates',
            'swap_phone_numbers',
            'add_neighborhoods_maps_directions',
            'order_reviews',
            'order_backlinks',
            'add_service_pages',
            'update_content',
            'respond_to_reviews',
            'login_to_gbp',
            'login_to_site_email',
            'view_all_reports',
            'view_crm_data',
            'view_owner_data_trends',
            'self_service_reports',
            'view_revenue_reports',
            'view_payout_reports',
            'view_other_reports',
            'create_rental_sale',
            'view_rental_sale',
            'update_rental_sale',
            'view_rental_report',
            'add_announcement',
            'remove_announcement',
            'site_generator',
            'news_generator',
            'access_leads_portal',
            'generate_bulk_content',
            'edit_wp_homepages',
            'generate_ai_content',
            'generate_reviews',
            'create_edit_form',
            'update_dns',
            'create_dns',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name'=> $permission]);
        }

        // Roles and their respective permissions
        $rolesPermissions = [
            'super_admin' => Permission::all()->pluck('name')->toArray(),
            'administrator' => [
                'add_users', 'edit_users', 'remove_users', 'purchase_domain', 'create_neighborhoods_maps',
                'create_rel_zipcode_list', 'get_phone_number_link_to_ringba', 'create_services_pages',
                'add_to_lead_gen_portal', 'create_logo', 'request_gbp', 'create_blog_pages',
                'create_geotag_images', 'send_messages_to_affiliates', 'swap_phone_numbers',
                'add_neighborhoods_maps_directions', 'order_reviews', 'order_backlinks', 'add_service_pages',
                'update_content', 'respond_to_reviews', 'login_to_gbp', 'login_to_site_email',
                'view_all_reports', 'view_crm_data', 'view_owner_data_trends', 'self_service_reports',
                'view_revenue_reports', 'view_payout_reports', 'view_other_reports', 'create_rental_sale',
                'view_rental_sale', 'update_rental_sale', 'view_rental_report', 'access_leads_portal', 'site_generator',
                'news_generator', 'add_announcement', 'remove_announcement',
                'generate_bulk_content', 'generate_ai_content', 'generate_reviews','update_dns','create_edit_form','create_dns'
            ],
            'creation' => [
                'purchase_domain', 'create_neighborhoods_maps', 'create_rel_zipcode_list',
                'get_phone_number_link_to_ringba', 'create_services_pages', 'add_to_lead_gen_portal',
                'create_logo', 'request_gbp', 'create_blog_pages', 'create_geotag_images',
                'send_messages_to_affiliates', 'generate_bulk_content', 'site_generator',
                'edit_wp_homepages', 'generate_ai_content'
            ],
            'maintenance' => [
                'swap_phone_numbers', 'add_neighborhoods_maps_directions', 'order_reviews',
                'order_backlinks', 'add_service_pages', 'update_content', 'respond_to_reviews',
                'login_to_gbp', 'login_to_site_email',
            ],
            'sales_manager' => [
                'create_rental_sale', 'view_rental_sale', 'update_rental_sale', 'view_rental_report',
            ],
            'sales_person' => [
                'view_rental_report',
            ],
            'leads_manager' => [
                'access_leads_portal',
            ],
            'partner' => [
                'access_leads_portal',
            ],
            'user' => [],
        ];

        foreach ($rolesPermissions as $roleName => $permissions) {
            $role = Role::firstOrCreate(['name' => $roleName]);
            $role->syncPermissions($permissions);
        }

        // $roles = [
        //     'super_admin',
        //     'administrator',
        //     'creation',
        //     'maintenance',
        //     'sales_manager',
        //     'sales_person',
        //     'leads_manager',
        //     'user'
        // ];

        // foreach ($roles as $roleName) {
        //     $role = Role::firstOrCreate(['name' => $roleName]);

        //     if ($roleName == 'super_admin') {
        //         $role->givePermissionTo(Permission::all());
        //     } elseif ($roleName == 'administrator') {
        //         $adminPermissions = [ 'add_users',  'edit_users', 'remove_users',
        //         'purchase_domain', 'create_neighborhoods_maps', 'create_rel_zipcode_list', 'get_phone_number_link_to_ringba', 'create_services_pages', 'add_to_lead_gen_portal', 'create_logo', 'request_gbp', 'create_blog_pages', 'create_geotag_images', 'send_messages_to_affiliates',
        //         'swap_phone_numbers', 'add_neighborhoods_maps_directions', 'order_reviews', 'order_backlinks', 'add_service_pages', 'update_content', 'respond_to_reviews', 'login_to_gbp', 'login_to_site_email',
        //         'view_all_reports', 'view_crm_data', 'view_owner_data_trends', 'self_service_reports', 'view_revenue_reports', 'view_payout_reports', 'view_other_reports', 'create_rental_sale', 'view_rental_sale', 'update_rental_sale', 'view_rental_report',
        //         'access_leads_portal'
        //         ];
        //         $role->syncPermissions($adminPermissions);
        //     } elseif ($roleName == 'creation') {
        //         $creationPermissions = ['purchase_domain', 'create_neighborhoods_maps', 'create_rel_zipcode_list', 'get_phone_number_link_to_ringba', 'create_services_pages', 'add_to_lead_gen_portal', 'create_logo', 'request_gbp', 'create_blog_pages', 'create_geotag_images', 'send_messages_to_affiliates'];
        //         $role->syncPermissions($creationPermissions);
        //     } elseif ($roleName == 'maintenance') {
        //         $maintenancePermissions = ['swap_phone_numbers', 'add_neighborhoods_maps_directions', 'order_reviews', 'order_backlinks', 'add_service_pages', 'update_content', 'respond_to_reviews', 'login_to_gbp', 'login_to_site_email'];
        //         $role->syncPermissions($maintenancePermissions);
        //     } elseif ($roleName == 'sales_manager') {
        //         $salesManagerPermissions = ['create_rental_sale', 'view_rental_sale', 'update_rental_sale', 'view_rental_report'];
        //         $role->syncPermissions($salesManagerPermissions);
        //     } elseif ($roleName == 'sales_person') {
        //         $salesPersonPermissions = ['view_rental_report'];
        //         $role->syncPermissions($salesPersonPermissions);
        //     } elseif ($roleName == 'leads_manager') {
        //         $leadsManagerPermissions = ['access_leads_portal'];
        //         $role->syncPermissions($leadsManagerPermissions);
        //     }

        // }

    }
}
