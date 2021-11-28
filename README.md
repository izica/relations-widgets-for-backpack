## Laravel Backpack Relations Widgets
1. [Installation](#installation)
2. [Screenshots](#screenshots)
3. [Features](#features)
4. [Documentation](#documentation)
5. [Usage](#usage)
5. [How to enable creating related model](#how-to-enable-creating-related-model)

### 3.0 Whats new:
* relation_table search input
* relation_table create button with relation attribute reference
* relation_table pagination

### Installation
```
composer require izica/relations-widgets-for-backpack
```

### Screenshots

![Alt text](screenshots/relation_panel.png?raw=true "relation_panel")
![Alt text](screenshots/relation_table.png?raw=true "relation_table")

### Features
* use widgets for showing relations in show operation
* show or hide panels or fields by conditions
* build field value by closure
* use dot orm notation for accessing relation fields

### Documentation
* relation_panel
    * `name` - name of relation
    * `label` - panel label
    * `backpack_crud` - backpack crud url,
    * `buttons` (optional) - set false to hide all action buttons
    * `button_show` (optional) - set false to hide
    * `button_edit` (optional) - set false to hide
    * `visible` (optional) - closure for hiding or showing panel
    * `fields` (optional) - fields array, by default get columns from `fillable` in model
        * `name` - name
        * `label` - for field
        * `closure` - use closure instead of name field,
        * `visible`(optional) - closure for hiding or showing panel
        
* relation_table
    * `name` - (required) name of relation
    * `label` - panel label
    * `relation_attribute` - (optional) used for passing url parameter for button_create
    * `search` - (optional) `closure`, enables search input
    * `per_page` - (optional) enables pagination, `null` by default
    * `backpack_crud` - backpack crud url,
    * `buttons` (optional) - set false to hide all action buttons
    * `button_create` (optional) - set false to hide
    * `button_show` (optional) - set false to hide
    * `button_edit` (optional) - set false to hide
    * `button_delete` (optional) - set false to hide
    * `visible` (optional) - closure for hiding or showing panel
    * `fields` (optional) - columns array, by default get columns from `fillable` in model
        * `name` - name
        * `label` - for field
        * `closure` - use closure instead of name field,
        
### Usage

#### Relation panel
`belongsTo`, `hasOne`

```php
use Backpack\CRUD\app\Library\Widget;

protected function setupShowOperation()
{
    Widget::add([
        'type'           => 'relation_panel',
        'name'           => 'account_contact',
        'label'          => 'Account contact info',
        'backpack_crud'  => 'accountcontact',
        'visible' => function($entry){
            return $entry->is_public_person;
        },
        'buttons' => false,
        'fields'         => [
            [
                'label' => 'Birthdate',
                'closure' => function($entry){
                    return date('d.M.Y', $entry->birthdate);
                }
            ],
            [
                'label' => 'Contact phone',
                'name'  => 'contact_phone',
            ],
            [
                'label' => 'Contact email',
                'name'  => 'contact_email',
            ],
            [
                'label' => 'Address',
                'name'  => 'address.name',
                'visible' => function($entry){
                    return !!$entry->address;
                }       
            ],
        ],
    ])->to('after_content');
}

```

#### Relation table
`hasMany`

```php
protected function setupShowOperation()
{
    Widget::add([
        'type'           => 'relation_table',
        'name'           => 'order_cargos',
        'label'          => 'Order cargo list',
        'backpack_crud'  => 'ordercargo',
        'visible' => function($entry){
            return $entry->order_cargos->count() > 0;
        },
        'search' => function ($query, $search) {
            return $query->where('name', 'like', "%{$search}%");
        },
        'relation_attribute' => 'order_id',
        'button_create' => false,
        'button_delete' => false,
        'columns' => [
            [
                'label' => 'Type',
                'name'  => 'order_cargo_type.name',
            ],
            [
                'label' => 'Weight',
                'name'  => 'weight',
            ],
            [
                'label' => 'Value, $',
                'closure' => function($entry){
                    return "{$entry->value}$";
                }
            ],
        ],
    ])->to('after_content');
}

```

### How to enable creating related model
You need to set:
* `button_create` => `true`
* `relation_attribute` => `attribute_name`

Next you need to add to relation/select field `default` value:
```php
    CRUD::addField([
        'type' => "relationship",
        'name' => 'order',
        'default' => $_GET['order_id'] ?? null
    ]);
```