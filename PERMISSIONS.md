# Permissions and Roles Documentation

## Introduction
### Purpose
This documentation provides an overview of the permissions and roles within this application. It includes detailed descriptions of each permission and role, along with guidelines for developers on how to create new roles or permissions.

## Key Concepts
### Permissions
Permissions are the smallest units of authorization, granting users the ability to perform specific actions or operations. These permissions are then assigned to roles. 

### Roles
Roles are collections of permissions that define a set of actions or operations a user is authorized to perform. Roles group multiple permissions together to streamline user management and access control. Role are assigned to the user.

## Permissions List
| Permission Name       | Description                  | Module/Feature  |
|-----------------------|------------------------------|-----------------|
| `create_rental_sale` | Allows creating rental sales | User Management |
| `view_rental_sale`   | Allows viewing rental sales  | Reporting       |
| `update_rental_sale` | Allows editing rental sales  | User Management |
| `view_rental_report`  | Allows viewing rental report | User Management |

## Standard Roles
| Role Name       | Description                                  | Permissions                                |
|-----------------|----------------------------------------------|--------------------------------------------|
| `sales_manager` | Full access to all sales relation operations | `view_rental_sale`, `create_rental_sale`, `update_rental_sale`, `view_rental_report` |
| `sales_person`  | Only can see the rental reporting            | `view_rental_report`                  |

## Guidelines for Creating New Permissions and Roles
### Naming Conventions
Describe naming conventions.

### Development process
1. Go to RolesAndPermissionsSeeder inside /database/seeders
2. Add permission (if new permissions) in permissions array
3. Add new role in roles array (if new role)
4. There is for loop to insert all the roles in database. Also inside the loop the proper permissions are added to the roles with if else conditions. Add the proper permissions to proper role inside that loop.
5. Go to CustomPermission class into app/Models, add the friendly name (which would be used as navigation menu name), and add routing path for that ( navigation menu) name. When adding the routing path, also set the 'ready' value to 'true' when it is ready.
6. This is so far for core development of the roles and permissions.
7. Now, in controller, get the permission lists base on the user's role. For example, go to ReportingUserController and see it.
8. Finally, reseed db with php artisan db:seed --class=RolesAndPermissionsSeeder

### Approval Process
Describe the approval process.

### Documentation Requirements
Describe what needs to be documented for new permissions and roles.

## Use Cases and Examples
Provide practical examples and use cases.

## Review and Update Process
### Review Schedule
Describe the review schedule.

### Responsible Parties
List who is responsible for reviews and updates.

## Version Control
Explain how version control is managed.
