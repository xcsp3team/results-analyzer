<div class="relative">
    <span class="absolute right-3 -top-8 dark:text-gray-400 " wire:click="export">@svg('hugeicons-xls-02')</span>
    <div class="overflow-x-auto shadow-md relative">
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
                        <td
                            {{$cell->attributes}}
                            class="td  {{$header[$i]->align ?? ""}} {{$cell->class}} {{ is_numeric($cell->value) ? "tabular-nums" : "" }}">
                            {!!  is_float($cell->value) ? number_format($cell->value, 2, ".", "&nbsp;") : (is_numeric($cell->value) ? number_format((int)$cell->value, 0, ".", "&nbsp;") : $cell->value) !!}
                        </td>
                    @endforeach
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
</div>
