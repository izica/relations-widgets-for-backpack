## Laravel Backpack Relations Views


### Installation
```
composer require izica/laravel-backpack-relations-views
```

### Screenshots

![Alt text](screenshots/relation_panel.png?raw=true "relation_panel")
![Alt text](screenshots/relation_table.png?raw=true "relation_table")

### Features
* use relations as views at show operation
* show or hide panels or fields by conditions
* build field value by closure
* use dot orm notation for accessing relation fields

### Docs for widgets
* relation_panel
    * `name` - name of relation
    * `label` - panel label
    * `backpack_crud` - backpack crud url,
    * `buttons` (optional) - set false to hide all action buttons
    * `button_show` (optional) - set false to hide
    * `button_edit` (optional) - set false to hide
    * `visible` (optional) - closure for hiding or showing panel
    * `fields` - fields array,
        * `name` - name
        * `label` - for field
        * `closure` - use closure instead of name field,
        * `visible`(optional) - closure for hiding or showing panel
        
* relation_table
    * `name` - name of relation
    * `label` - panel label
    * `backpack_crud` - backpack crud url,
    * `buttons` (optional) - set false to hide all action buttons
    * `button_add` (optional) - set false to hide
    * `button_show` (optional) - set false to hide
    * `button_edit` (optional) - set false to hide
    * `button_delete` (optional) - set false to hide
    * `visible` (optional) - closure for hiding or showing panel
    * `columns` - fields array,
        * `name` - name
        * `label` - for field
        * `closure` - use closure instead of name field,
        
### Usage

#### Relation panel
belongsTo, hasOne
```php
protected function setupShowOperation()
{
    $this->data['widgets']['after_content'][] = [
        'type'           => 'relation_panel',
        'name'           => 'account_contact',
        'label'          => 'Account contact info',
        'backpack_crud'  => 'accountcontact',
        'visible' => function($entry){
            return $entry->is_public_person;
        },
        'buttons' => false,
        'fields'        => [
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
    ];
}

```

#### Relation table
hasMany
```php
protected function setupShowOperation()
{
    $this->data['widgets']['after_content'][] = [
        'type'           => 'relation_table',
        'name'           => 'order_cargos',
        'label'          => 'Order cargo list',
        'backpack_crud'  => 'accountcontact',
        'visible' => function($entry){
            return $entry->order_cargos->count() > 0;
        },
        'buttons' => false,
        'fields'        => [
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
    ];
}

```