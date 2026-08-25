<div class="relative overflow-x-auto shadow-md ">
    <table class="table">
        @if($header != null && count($header) > 0)
            <thead class="thead">
            <tr>
                @foreach($header as $i => $th)
                    <th scope="col"
                        class="th {{$th->align}}"
                        {{isset($th->sortable) ? "wire:click=sort($i)": ""}}
                    >
                        <div class="flex {{$th->align=="text-right" ? "justify-end" : "justify-start"}}">
                            <span class="mr-2">{{$th->value}}</span>
                            <span
                                class="w-4">{{$sort_field==$i ? ($sort_direction== 'asc' ? svg('heroicon-o-arrow-down') : svg('heroicon-o-arrow-up')):svg('heroicon-o-arrows-up-down')}}</span>
                        </div>
                    </th>
                @endforeach
            </tr>
            </thead>
        @endif
        <tbody>
        @foreach($table as $row)
            <tr class="tr">
                @foreach($row as $i => $cell)
                    <td class="td  {{$header[$i]->align ?? ""}} {{$cell->class}}">
                        {!!  is_numeric($cell->value) ? number_format((int)$cell->value, 0, ",", "&nbsp;") : $cell->value !!}
                    </td>
                @endforeach
            </tr>
        @endforeach
        </tbody>
    </table>
</div>
