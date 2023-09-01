@php
if (!isset($excludeFieldTypes))
{
$excludeFieldTypes = [];
}
@endphp

@if($userfields && count($userfields) > 0)

@foreach($userfields as $userfield)

@if(in_array($userfield->type, $excludeFieldTypes))
@continue
@endif

@if($userfield->show_as_column_in_tables == 1)
@php $userfieldObject = FindObjectInArrayByPropertyValue($userfieldValues, 'name', $userfield->name) @endphp
<td>
	@if($userfieldObject !== null)
	@if($userfield->type == \Grocy\Services\UserfieldsService::USERFIELD_TYPE_CHECKBOX)
	@if($userfieldObject->value == 1)<i class="fa-solid fa-check"></i>@endif
	@elseif($userfield->type == \Grocy\Services\UserfieldsService::USERFIELD_TYPE_PRESET_CHECKLIST)
	{!! str_replace(',', '<br>', $userfieldObject->value) !!}
	@elseif($userfield->type == \Grocy\Services\UserfieldsService::USERFIELD_TYPE_LINK)
	<a href="{{ $userfieldObject->value }}"
		target="_blank">{{ $userfieldObject->value }}</a>
	@elseif($userfield->type == \Grocy\Services\UserfieldsService::USERFIELD_TYPE_LINK_WITH_TITLE)
	@php
	$title = '';
	$link = '';
	if(!empty($userfieldObject->value))
	{
	$data = json_decode($userfieldObject->value);
	$title = $data->title;
	$link = $data->link;
	}
	@endphp
	<a href="{{ $link }}"
		target="_blank">{{ $title }}</a>
	@elseif($userfield->type == \Grocy\Services\UserfieldsService::USERFIELD_TYPE_FILE && !empty($userfieldObject->value))
	<a href="{{ $U('/files/userfiles/'. $userfieldObject->value) }}"
		target="_blank">{{ base64_decode(explode('_', $userfieldObject->value)[1]) }}</a>
	@elseif($userfield->type == \Grocy\Services\UserfieldsService::USERFIELD_TYPE_IMAGE && !empty($userfieldObject->value))
	<a class="show-as-dialog-link"
		href="{{ $U('/files/userfiles/'. $userfieldObject->value . '?force_serve_as=picture') }}">
		<img src="{{ $U('/files/userfiles/'. $userfieldObject->value . '?force_serve_as=picture&best_fit_width=32&best_fit_height=32') }}"
			title="{{ base64_decode(explode('_', $userfieldObject->value)[1]) }}"
			alt="{{ base64_decode(explode('_', $userfieldObject->value)[1]) }}"
			loading="lazy">
	</a>
	@elseif($userfield->type == \Grocy\Services\UserfieldsService::USERFIELD_TYPE_NUMBER_DECIMAL)
	<span class="locale-number locale-number-generic">{{ $userfieldObject->value }}</span>
	@elseif($userfield->type == \Grocy\Services\UserfieldsService::USERFIELD_TYPE_NUMBER_CURRENCY)
	<span class="locale-number locale-number-currency">{{ $userfieldObject->value }}</span>
	@else
	{{ $userfieldObject->value }}
	@endif
	@endif
</td>
@endif

@endforeach

@endif
