@if(count($userfields) > 0)

@foreach($userfields as $userfield)

@if($userfield->show_as_column_in_tables == 1)
	@php $userfieldObject = FindObjectInArrayByPropertyValue($userfieldValues, 'name', $userfield->name) @endphp
	<td>@if($userfieldObject !== null){{ $userfieldObject->value }}@endif</td>
@endif

@endforeach

@endif
