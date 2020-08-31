@if(count($userfields) > 0)

@foreach($userfields as $userfield)

@if($userfield->show_as_column_in_tables == 1)
<th>{{ $userfield->caption }}</th>
@endif

@endforeach

@endif
