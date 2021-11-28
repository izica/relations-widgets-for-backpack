@php
    if(!isset($entry)){
        return;
    }
    if(isset($widget['visible']) && is_callable($widget['visible'])){
        if(!$widget['visible']($entry)){
            return;
        }
    }

    $searchName = "{$widget['name']}-search";
    $widgetId = "relation-table-{$widget['name']}";

    if (!isset($widget['buttons']) || $widget['buttons'] !== false) {
        $widget['buttons'] = true;
    }
    if (!isset($widget['button_create']) || $widget['button_create'] !== false) {
        $widget['button_create'] = true;
    }
     if (!isset($widget['button_show']) || $widget['button_show'] !== false) {
        $widget['button_show'] = true;
    }
    if (!isset($widget['button_edit']) || $widget['button_edit'] !== false) {
        $widget['button_edit'] = true;
    }
    if (!isset($widget['button_delete']) || $widget['button_delete'] !== false) {
        $widget['button_delete'] = true;
    }
    if(!isset($widget['columns'])){
        $widget['columns'] = [];

        try {
            $item = get_class($entry->{$widget['name']}()->getRelated());
            $item = new $item();

            $widget['columns'] = [];
            foreach ($item->getFillable() as $property){
                $widget['columns'][] = [
                    'label' => $crud->makeLabel($property),
                    'name'  => $property,
                ];
            }
        } catch (Exception $e){}
    }
    if(!isset($widget['columns']) && !isset($widget['model'])){
        $widget['columns'] = [];
    }

    $createUrl = backpack_url($widget['backpack_crud'] . "/create");
    if (isset($widget['relation_column'])) {
        $createUrl .= "?{$widget['relation_column']}={$entry->id}";
    }

    if (!isset($widget['search']) || !is_callable($widget['search'])) {
        $widget['search'] = false;
    }

    $query = $entry->{$widget['name']}();
    if(is_callable($widget['search']) && isset($_GET[$searchName])){
        $query = $widget['search']($query, $_GET[$searchName]);
    }
    $items = $query->get();
@endphp
<div id="{{$widgetId}}">
    <div class="row mb-0">
        <div class="col-sm-6">
            <div class="d-flex align-items-center mb-2">
                <h5 class="mr-2 mb-0">{{$widget['label']}}</h5>
                @if ($widget['button_create'] === true)
                    <a
                            href="{{ $createUrl }}"
                            class="btn btn-primary"
                            data-style="zoom-in"
                    >
                        <span class="ladda-label"><i class="la la-plus"></i> {{ trans('backpack::crud.add') }}</span>
                    </a>
                @endif
            </div>
        </div>
        @if ($widget['search'] !== false)
            <form
                    class="offset-3 col-sm-3"
                    onsubmit="
                            location.hash = 'hack';
                            location.hash = '';
                            location.href=this.action + '{{$widgetId}}';
                            return true;
                            "
            >
                <input
                        type="search"
                        name="{{$searchName}}"
                        class="form-control"
                        placeholder="{{ trans('backpack::crud.search') }}"
                        value="{{$_GET[$searchName] ?? ''}}"
                />
            </form>
        @endif
    </div>
    <table
            class="bg-white table table-striped table-hover nowrap rounded shadow-xs border-xs dataTable dtr-inline"
            cellspacing="0"
            aria-describedby="crudTable_info" role="grid"
    >
        <thead>
        <tr role="row">
            @foreach($widget['columns'] as $column)
                <th>{{$column['label']}}</th>
            @endforeach
            @if($widget['buttons'] === true)
                <th>{{ trans('backpack::crud.actions') }}</th>
            @endif
        </tr>
        </thead>
        <tbody>
        @foreach($items as $item)
            <tr role="row">
                @foreach($widget['columns'] as $column)
                    @php
                        $value = '';
                        if(isset($column['closure'])){
                            $value = $column['closure']($item);
                        }
                        if(isset($column['name'])){
                             $value = data_get($item, $column['name']);
                        }
                    @endphp
                    <td>
                        <span>{!!$value!!}</span>
                    </td>
                @endforeach
                @if($widget['buttons'] === true)
                    <td>
                        @if ($widget['button_show'] === true)
                            <a href="{{ backpack_url($widget['backpack_crud'] . "/" . $item->id . "/show") }}"
                               class="btn btn-sm btn-link">
                                <i class="la la-eye"></i> {{ trans('backpack::crud.preview') }}
                            </a>
                        @endif
                        @if ($widget['button_edit'] === true)
                            <a href="{{ backpack_url($widget['backpack_crud'] . "/" . $item->id . "/edit") }}"
                               class="btn btn-sm btn-link">
                                <i class="la la-edit"></i> {{ trans('backpack::crud.edit') }}
                            </a>
                        @endif
                        @if ($widget['button_delete'] === true)
                            <a href="javascript:void(0)" onclick="deleteEntryRelationHasManyWidget(this)"
                               data-route="{{ backpack_url($widget['backpack_crud'] . "/" . $item->id) }}"
                               class="btn btn-sm btn-link" data-button-type="delete">
                                <i class="la la-trash"></i> {{ trans('backpack::crud.delete') }}
                            </a>
                        @endif
                    </td>
                @endif
            </tr>
        @endforeach
        </tbody>
        <tfoot>
        <tr>
            @foreach($widget['columns'] as $column)
                <th>{{$column['label']}}</th>
            @endforeach
            @if($widget['buttons'] === true)
                <th rowspan="1" colspan="1">{{ trans('backpack::crud.actions') }}</th>
            @endif
        </tr>
        </tfoot>
    </table>
</div>

@push('after_scripts') @if (request()->ajax()) @endpush @endif
<script>
    if (typeof deleteEntryRelationHasManyWidget != 'function') {
        $('[data-button-type=delete]').unbind('click');

        function deleteEntryRelationHasManyWidget(button) {
            // ask for confirmation before deleting an item
            // e.preventDefault();
            var button = $(button);
            var route = button.attr('data-route');
            var row = button.closest('tr');

            swal({
                title: "{!! trans('backpack::base.warning') !!}",
                text: "{!! trans('backpack::crud.delete_confirm') !!}",
                icon: 'warning',
                buttons: {
                    cancel: {
                        text: "{!! trans('backpack::crud.cancel') !!}",
                        value: null,
                        visible: true,
                        className: 'bg-secondary',
                        closeModal: true
                    },
                    delete: {
                        text: "{!! trans('backpack::crud.delete') !!}",
                        value: true,
                        visible: true,
                        className: 'bg-danger'
                    }
                }
            }).then((value) => {
                if (value) {
                    $.ajax({
                        url: route,
                        type: 'DELETE',
                        success: function (result) {
                            if (result == 1) {
                                // Show a success notification bubble
                                new Noty({
                                    type: 'success',
                                    text: "{!! '<strong>'.trans('backpack::crud.delete_confirmation_title').'</strong><br>'.trans('backpack::crud.delete_confirmation_message') !!}"
                                }).show();

                                // Hide the modal, if any
                                $('.modal').modal('hide');

                                // Remove the details row, if it is open
                                if (row.hasClass('shown')) {
                                    row.next().remove();
                                }
                                // Remove the row from the datatable
                                row.remove();
                            } else {
                                // if the result is an array, it means
                                // we have notification bubbles to show
                                if (result instanceof Object) {
                                    // trigger one or more bubble notifications
                                    Object.entries(result).forEach(function (entry, index) {
                                        var type = entry[0];
                                        entry[1].forEach(function (message, i) {
                                            new Noty({
                                                type: type,
                                                text: message
                                            }).show();
                                        });
                                    });
                                } else {// Show an error alert
                                    swal({
                                        title: "{!! trans('backpack::crud.delete_confirmation_not_title') !!}",
                                        text: "{!! trans('backpack::crud.delete_confirmation_not_message') !!}",
                                        icon: 'error',
                                        timer: 4000,
                                        buttons: false
                                    });
                                }
                            }
                        },
                        error: function (result) {
                            // Show an alert with the result
                            swal({
                                title: "{!! trans('backpack::crud.delete_confirmation_not_title') !!}",
                                text: "{!! trans('backpack::crud.delete_confirmation_not_message') !!}",
                                icon: 'error',
                                timer: 4000,
                                buttons: false
                            });
                        }
                    });
                }
            });

        }
    }

    // make it so that the function above is run after each DataTable draw event
    // crud.addFunctionToDataTablesDrawEventQueue('deleteEntry');
</script>
@if (!request()->ajax()) @endpush @endif
