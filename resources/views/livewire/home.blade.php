<div>
    <select  wire:model="evaluation" wire:change="load" class="block w-full px-3 py-2.5 bg-neutral-secondary-medium border border-default-medium text-heading text-sm rounded-base focus:ring-brand focus:border-brand shadow-xs placeholder:text-body">
        <option selected>Choose an evaluation</option>
        @foreach($evaluations as $v)
            <option value="{{$v->id}}">{{$v->name}} {{$v->track}}</option>
        @endforeach
    </select>

</div>
