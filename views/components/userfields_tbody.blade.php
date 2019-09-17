@if(count($userfields) > 0)

@foreach($userfields as $userfield)

@if($userfield->show_as_column_in_tables == 1)
	@php $userfieldObject = FindObjectInArrayByPropertyValue($userfieldValues, 'name', $userfield->name) @endphp
	<td>
	@if($userfieldObject !== null)
		@if($userfield->type == \Grocy\Services\UserfieldsService::USERFIELD_TYPE_CHECKBOX)
			@if($userfieldObject->value == 1)<i class="fas fa-check"></i>@endif
		@elseif($userfield->type == \Grocy\Services\UserfieldsService::USERFIELD_TYPE_PRESET_CHECKLIST)
			{!! str_replace(',', '<br>', $userfieldObject->value) !!}
		@elseif($userfield->type == \Grocy\Services\UserfieldsService::USERFIELD_TYPE_LINK)
			<a href="{{ $userfieldObject->value }}" target="_blank">{{ $userfieldObject->value }}</a>
		@else
			{{ $userfieldObject->value }}
		@endif
	@endif
	</td>
@endif

@endforeach

@endif
