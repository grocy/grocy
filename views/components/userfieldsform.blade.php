@push('componentScripts')
	<script src="{{ $U('/viewjs/components/userfieldsform.js', true) }}?v={{ $version }}"></script>
@endpush

@if(count($userfields) > 0)

<div id="userfields-form" data-entity="{{ $entity }}" class="border border-info p-2 mb-2" novalidate>
	<h2 class="small">{{ $__t('Userfields') }}</h2>

	@foreach($userfields as $userfield)

	@if($userfield->type == \Grocy\Services\UserfieldsService::USERFIELD_TYPE_SINGLE_LINE_TEXT)
	<div class="form-group">
		<label for="name">{{ $userfield->caption }}</label>
		<input type="text" class="form-control userfield-input" data-userfield-name="{{ $userfield->name }}">
	</div>
	@elseif($userfield->type == \Grocy\Services\UserfieldsService::USERFIELD_TYPE_SINGLE_MULTILINE_TEXT)
	<div class="form-group">
		<label for="description">{{ $userfield->caption }}</label>
		<textarea class="form-control userfield-input" rows="4" data-userfield-name="{{ $userfield->name }}"></textarea>
	</div>
	@elseif($userfield->type == \Grocy\Services\UserfieldsService::USERFIELD_TYPE_INTEGRAL_NUMBER)
	@include('components.numberpicker', array(
		'id' => $userfield->name,
		'label' => $userfield->caption,
		'noNameAttribute' => true,
		'min' => 0,
		'isRequired' => false,
		'additionalCssClasses' => 'userfield-input',
		'additionalAttributes' => 'data-userfield-name="' . $userfield->name . '"'
	))
	@elseif($userfield->type == \Grocy\Services\UserfieldsService::USERFIELD_TYPE_DECIMAL_NUMBER)
	@include('components.numberpicker', array(
		'id' => '',
		'label' => $userfield->caption,
		'noNameAttribute' => true,
		'min' => 0,
		'step' => 0.01,
		'isRequired' => false,
		'additionalCssClasses' => 'userfield-input',
		'additionalAttributes' => 'data-userfield-name="' . $userfield->name . '"'
	))
	@elseif($userfield->type == \Grocy\Services\UserfieldsService::USERFIELD_TYPE_DATE)
	@include('components.datetimepicker', array(
		'id' => $userfield->name,
		'label' => $userfield->caption,
		'noNameAttribute' => true,
		'format' => 'YYYY-MM-DD',
		'initWithNow' => false,
		'limitEndToNow' => false,
		'limitStartToNow' => false,
		'additionalGroupCssClasses' => 'date-only-datetimepicker',
		'isRequired' => false,
		'additionalCssClasses' => 'userfield-input',
		'additionalAttributes' => 'data-userfield-name="' . $userfield->name . '"'
	))
	@elseif($userfield->type == \Grocy\Services\UserfieldsService::USERFIELD_TYPE_DATETIME)
	@include('components.datetimepicker', array(
		'id' => $userfield->name,
		'label' => $userfield->caption,
		'noNameAttribute' => true,
		'format' => 'YYYY-MM-DD HH:mm:ss',
		'initWithNow' => false,
		'limitEndToNow' => false,
		'limitStartToNow' => false,
		'isRequired' => false,
		'additionalCssClasses' => 'userfield-input',
		'additionalAttributes' => 'data-userfield-name="' . $userfield->name . '"'
	))
	@elseif($userfield->type == \Grocy\Services\UserfieldsService::USERFIELD_TYPE_CHECKBOX)
	<div class="form-group">
		<div class="form-check">
			<input class="form-check-input userfield-input" type="checkbox" data-userfield-name="{{ $userfield->name }}" value="1">
			<label class="form-check-label" for="{{ $userfield->name }}">{{ $userfield->caption }}</label>
		</div>
	</div>
	@elseif($userfield->type == \Grocy\Services\UserfieldsService::USERFIELD_TYPE_PRESET_LIST)
	<div class="form-group">
		<label for="{{ $userfield->name }}">{{ $userfield->caption }}</label>
		<select class="form-control userfield-input" data-userfield-name="{{ $userfield->name }}">
			<option></option>
			@foreach(preg_split('/\r\n|\r|\n/', $userfield->config) as $option)
				<option value="{{ $option }}">{{ $option }}</option>
			@endforeach
		</select>
	</div>
	@endif

	@endforeach

</div>

@endif
