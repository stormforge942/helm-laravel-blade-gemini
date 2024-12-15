<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Permission\Models\Permission as SpatiePermission;

class CustomPermission extends SpatiePermission
{
    public function getFriendlyNameAttribute()
    {
        $friendlyNameMap = [
            'manage_roles_permissions' => 'Manage roles and permissions',
            'add_users' => 'Add users',
            'edit_users' => 'Edit users',
            'remove_users' => 'Remove users',
            'purchase_domain' => 'Purchase domain',
            'create_neighborhoods_maps' => 'Generate & Publish Neighborhoods',
            'create_rel_zipcode_list' => 'Create relevant zip code list',
            'get_phone_number_link_to_ringba' => 'Acquire phone number and link to Ringba',
            'create_services_pages' => 'Generate & Publish Service Pages',
            'add_to_lead_gen_portal' => 'Add to lead generation portal',
            'create_logo' => 'Create logo',
            'request_gbp' => 'Create GBP',
            'create_blog_pages' => 'Generate & Publish Blog Posts',
            'create_geotag_images' => 'Create and geotag images',
            'send_messages_to_affiliates' => 'Send messages to affiliates',
            'swap_phone_numbers' => 'Swap out phone numbers on Ringba',
            'add_neighborhoods_maps_directions' => 'Add neighborhoods, maps, or directions',
            'order_reviews' => 'Order reviews',
            'order_backlinks' => 'Order backlinks',
            'add_service_pages' => 'Add and edit service pages',
            'update_content' => 'Update content',
            'respond_to_reviews' => 'Respond to reviews',
            'login_to_gbp' => 'Log ito GBP',
            'login_to_site_email' => 'Log into site email',
            'view_all_reports' => 'View all reports',
            'view_crm_data' => 'CRM data',
            'view_owner_data_trends' => 'Owner data and trends',
            'self_service_reports' => 'Self-service reports',
            'view_revenue_reports' => 'Revenue reporting',
            'view_payout_reports' => 'Payout reports',
            'create_rental_sale' => 'Rental Contract Entry',
            'view_rental_sale' => 'Rental Pipeline',
            'view_rental_report'=> 'Sales Activity Report',
            'add_announcement' => 'Add Announcement',
            'remove_announcement'=> 'Remove Announcement',
            'news_generator' => "Generate News",
            'site_generator' => "Generate Site",
            'access_leads_portal' => 'Revenue Reporting',
            'generate_bulk_content' => 'Generate Bulk Blog Posts',
            'edit_wp_homepages' => 'Generate and Publish Homepages',
            'generate_ai_content' => 'Generate Content',
            'generate_reviews' => 'Generate Reviews',
            'create_edit_form'=>'Form Creation',
            'update_dns' => 'Namecheap DNS Configuration',
            'create_dns' => 'Create DNS'
        ];

        return $friendlyNameMap[$this->name] ?? $this->name;
    }

    public function getLinkAttribute()
    {
        $permissionsData = [
            'manage_roles_permissions' => ['url' => '/admin/permissions', 'ready' => true],
            'add_users' => ['url' => '/admin/users/register', 'ready' => true],
            'edit_users' => ['url' => '/admin/edit', 'ready' => false],
            'remove_users' => ['url' => '/admin/users/remove', 'ready' => true],
            'purchase_domain' => ['url' => '/creation/domains', 'ready' => false],
            'create_neighborhoods_maps' =>  ['url' => '/creation/neighborhoods', 'ready' => true],
            'create_rel_zipcode_list' =>  ['url' => '/creation/zipcode-list', 'ready' => false],
            'get_phone_number_link_to_ringba' =>  ['url' => '/creation/ringba', 'ready' => false],
            'create_services_pages' => ['url' => '/creation/pages/service', 'ready' => true],
            'add_to_lead_gen_portal' =>  ['url' => '/creation/lead-gen', 'ready' => false],
            'create_logo' =>  ['url' => '/creation/logo', 'ready' => false],
            'request_gbp' => ['url' => '/creation/gbp', 'ready' => false],
            'create_blog_pages' =>  ['url' => '/creation/posts/blog', 'ready' => true],
            'create_geotag_images' =>  ['url' => '/creation/geotag', 'ready' => false],
            'send_messages_to_affiliates' =>  ['url' => '/creation/affiliates', 'ready' => false],
            'swap_phone_numbers' => ['url' => '/maintenance/ringba', 'ready' => false],
            'add_neighborhoods_maps_directions' => ['url' => '/maintenance/neighborhoods', 'ready' => false],
            'order_reviews' => ['url' => '/maintenance/reviews', 'ready' => false],
            'order_backlinks' => ['url' => '/maintenance/backlinks', 'ready' => false],
            'add_service_pages' =>  ['url' => '/maintenance/service-pages', 'ready' => false],
            'update_content' =>  ['url' => '/maintenance/content', 'ready' => false],
            'respond_to_reviews' => ['url' => '/maintenance/reviews', 'ready' => false],
            'login_to_gbp' => ['url' => '/maintenance/gbp', 'ready' => false],
            'login_to_site_email' => ['url' => '/maintenance/site-email', 'ready' => false],
            'view_all_reports' => ['url' => '/reporting/all', 'ready' => false],
            'view_crm_data' => ['url' =>  '/reporting/crm', 'ready' => false],
            'view_owner_data_trends' => ['url' =>  '/reporting/owner', 'ready' => false],
            'self_service_reports' => ['url' => '/reporting/self-service', 'ready' => false],
            'view_revenue_reports' => ['url' => '/reporting/revenue', 'ready' => false],
            'view_payout_reports' => ['url' => '/reporting/payout', 'ready' => false],
            'view_kixie_reports' => ['url' => '/reporting/kixie', 'ready' => true],
            'view_other_reports' => ['url' =>  '/reporting/misc', 'ready' => false],
            'create_rental_sale'=>['url'=>'/reporting/sales-form','ready'=>true],
            'view_rental_sale'=>['url'=>'/reporting/pipeline-report','ready'=>true],
            'view_rental_report'=>['url'=>'/reporting/rental-report','ready'=>true],
            // 'add_announcement'=>['url'=>'/announcement/create','ready'=>true],
            // 'remove_announcement'=>['url'=>'/announcement/remove','ready'=>true],
            'access_leads_portal' => ['url' => '/reporting/clearvue/redirect', 'ready'=>true],
            'generate_bulk_content' => ['url' => '/creation/generate-content/bulk', 'ready'=>true],
            'edit_wp_homepages' => ['url' => '/creation/generate-content/homepage', 'ready'=>true],
            'generate_ai_content' => ['url' => '/creation/generate-content', 'ready'=>true],
            'generate_reviews' => ['url' => '/creation/generate-content/review', 'ready'=>true],
            'news_generator' => ['url' => '/creation/news-generator', 'ready'=>true],
            'create_edit_form'=>['url'=>'/maintenance/form-creation', 'ready'=>true],
            'update_dns' => ['url' => '/admin/update-dns', 'ready' => true],
            'create_dns' => ['url' => '/dns/create', 'ready' => true],
        ];

        if (isset($permissionsData[$this->name]) && $permissionsData[$this->name]['ready']) {
            return $permissionsData[$this->name]['url'];
        }

        return null;
    }
}
