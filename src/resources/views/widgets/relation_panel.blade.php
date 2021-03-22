@php
    $entry_relation = data_get($entry, $widget['name']);
    if(!isset($entry) || $entry_relation === null){
        return;
    }
    if(isset($widget['visible']) && is_callable($widget['visible'])){
        if(!$widget['visible']($entry_relation)){
            return;
        }
    }
    if(!isset($widget['fields'])){
        $widget['fields'] = [];
        foreach ($entry_relation->getFillable() as $propertyName){
            $widget['fields'][] = [
                'label' => $crud->makeLabel($propertyName),
                'name' => $propertyName,
            ];
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
                                if(!$field['visible']($entry_relation)){
                                    continue;
                                }
                            }
                            $value = '';
                            if(isset($field['closure'])){
                                $value = $field['closure']($entry_relation);
                            }
                            if(isset($field['name'])){
                                 $value = data_get($entry_relation, $field['name']);
                            }
                        @endphp
                        <tr>
                            <td>
                                <strong>{{$field['label'] ?? ''}}:</strong>
                            </td>
                            <td>
                                <span>{!!$value ?? ''!!}</span>
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
                                    <a href="{{ backpack_url($widget['backpack_crud'] . "/" . $entry_relation->id . "/show") }}"
                                       class="btn btn-sm btn-link">
                                        <i class="la la-eye"></i> {{ trans('backpack::crud.preview') }}
                                    </a>
                                @endif
                                @if ($widget['button_edit'] === true)
                                    <a href="{{ backpack_url($widget['backpack_crud'] . "/" . $entry_relation->id . "/edit") }}"
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
