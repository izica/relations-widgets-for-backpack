@php
    if(!isset($entry) || $entry->{$widget['name']} === null){
        return;
    }
    if(isset($widget['visible']) && is_callable($widget['visible'])){
        if(!$widget['visible']($entry->{$widget['name']})){
            return;
        }
    }
    if(!isset($widget['fields'])){
        foreach ($entry->{$widget['name']}->getFillable() as $propertyName){
            $widget['fields'][$propertyName] = $crud->makeLabel($propertyName);
        }
    }

    if (!isset($widget['button_show']) || $widget['button_show'] !== false) {
        $widget['button_show'] = true;
    }

    if (!isset($widget['button_edit']) || $widget['button_edit'] !== false) {
        $widget['button_edit'] = true;
    }

    if (!isset($widget['buttons']) || $widget['buttons'] !== false) {
        $widget['buttons'] = true;
    }
@endphp

<div class="row">
    <div class="col-md-8">
        <h5>{{$widget['label']}}</h5>
        <div class="card no-padding no-border">
            <table class="table table-striped mb-0">
                <tbody>
                    @foreach($widget['fields'] as $field)
                        @php
                            if(isset($field['visible']) && is_callable($field['visible'])){
                                if(!$field['visible']($entry->{$widget['name']}->{$widget['name']})){
                                    continue;
                                }
                            }
                            $value = '';
                            if(isset($field['closure'])){
                                $value = $field['closure']($entry->{$widget['name']});
                            }
                            if(isset($field['name'])){
                                 $value = data_get($entry->{$widget['name']}, $field['name']);
                            }
                        @endphp
                        <tr>
                            <td>
                                <strong>{{$field['label'] ?? ''}}:</strong>
                            </td>
                            <td>
                                <span>{{$value ?? ''}}</span>
                            </td>
                        </tr>
                    @endforeach
                    @if($widget['buttons'])
                        <tr>
                            <td>
                                <strong>{{ trans('backpack::crud.actions') }}</strong>
                            </td>
                            <td>
                                @if ($widget['button_show'] === true)
                                    <a href="/admin/{{$widget['backpack_crud']}}/{{$entry->{$widget['name']}->id}}/show"
                                       class="btn btn-sm btn-link">
                                        <i class="la la-eye"></i> {{ trans('backpack::crud.preview') }}
                                    </a>
                                @endif
                                @if ($widget['button_edit'] === true)
                                    <a href="/admin/{{$widget['backpack_crud']}}/{{$entry->{$widget['name']}->id}}/edit"
                                       class="btn btn-sm btn-link">
                                        <i class="la la-edit"></i> {{ trans('backpack::crud.edit') }}
                                    </a>
                                @endif
                            </td>
                        </tr>
                    @endif
                </tbody>
            </table>
        </div>
    </div>
</div>