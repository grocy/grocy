@if(count($userfields) > 0)

<div id="userfields-form"
	data-entity="{{ $entity }}"
	class="border border-info p-2 mb-2"
	novalidate>
	<h2 class="small">{{ $__t('Userfields') }}</h2>

	@foreach($userfields as $userfield)

	@if($userfield->type == \Grocy\Services\UserfieldsService::USERFIELD_TYPE_SINGLE_LINE_TEXT)
	<div class="form-group">
		<label>{{ $userfield->caption }}</label>
		<input type="text"
			class="form-control userfield-input"
			data-userfield-name="{{ $userfield->name }}">
	</div>
	@elseif($userfield->type == \Grocy\Services\UserfieldsService::USERFIELD_TYPE_SINGLE_MULTILINE_TEXT)
	<div class="form-group">
		<label for="description">{{ $userfield->caption }}</label>
		<textarea class="form-control userfield-input"
			rows="4"
			data-userfield-name="{{ $userfield->name }}"></textarea>
	</div>
	@elseif($userfield->type == \Grocy\Services\UserfieldsService::USERFIELD_TYPE_INTEGRAL_NUMBER)
	@include('components.numberpicker', array(
	'id' => $userfield->name,
	'label' => $userfield->caption,
	'noNameAttribute' => true,
	'min' => 0,
	'isRequired' => false,
	'additionalCssClasses' => 'userfield-input',
	'additionalAttributes' => 'data-userfield-name="' . $userfield->name . '"',
	'value' => ''
	))
	@elseif($userfield->type == \Grocy\Services\UserfieldsService::USERFIELD_TYPE_DECIMAL_NUMBER)
	@include('components.numberpicker', array(
	'id' => '',
	'label' => $userfield->caption,
	'noNameAttribute' => true,
	'min' => 0,
	'decimals' => 4,
	'isRequired' => false,
	'additionalCssClasses' => 'userfield-input',
	'additionalAttributes' => 'data-userfield-name="' . $userfield->name . '"',
	'value' => ''
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
		<div class="custom-control custom-checkbox">
			<input class="form-check-input custom-control-input userfield-input"
				type="checkbox"
				id="userfield-{{ $userfield->name }}"
				data-userfield-name="{{ $userfield->name }}"
				value="1">
			<label class="form-check-label custom-control-label"
				for="userfield-{{ $userfield->name }}">{{ $userfield->caption }}</label>
		</div>
	</div>
	@elseif($userfield->type == \Grocy\Services\UserfieldsService::USERFIELD_TYPE_PRESET_LIST)
	<div class="form-group">
		<label for="{{ $userfield->name }}">{{ $userfield->caption }}</label>
		<select class="custom-control custom-select userfield-input"
			data-userfield-name="{{ $userfield->name }}">
			<option></option>
			@foreach(preg_split('/\r\n|\r|\n/', $userfield->config) as $option)
			<option value="{{ $option }}">{{ $option }}</option>
			@endforeach
		</select>
	</div>
	@elseif($userfield->type == \Grocy\Services\UserfieldsService::USERFIELD_TYPE_PRESET_CHECKLIST)
	<div class="form-group">
		<label for="{{ $userfield->name }}">{{ $userfield->caption }}</label>
		<select multiple
			class="form-control userfield-input selectpicker"
			data-userfield-name="{{ $userfield->name }}"
			data-actions-Box="true"
			data-live-search="true">
			@foreach(preg_split('/\r\n|\r|\n/', $userfield->config) as $option)
			<option value="{{ $option }}">{{ $option }}</option>
			@endforeach
		</select>
	</div>
	@elseif($userfield->type == \Grocy\Services\UserfieldsService::USERFIELD_TYPE_LINK)
	<div class="form-group">
		<label>{{ $userfield->caption }}</label>
		<input type="link"
			class="form-control userfield-input"
			data-userfield-name="{{ $userfield->name }}">
	</div>
	@elseif($userfield->type == \Grocy\Services\UserfieldsService::USERFIELD_TYPE_LINK_WITH_TITLE)
	<div class="form-group">
		<label class="d-block">{{ $userfield->caption }}</label>
		<div class="form-row">
			<div class="col-4">
				<input type="text"
					class="form-control userfield-link userfield-link-title"
					placeholder="{{ $__t('Title') }}">
			</div>
			<div class="col-8">
				<input type="link"
					class="form-control userfield-link userfield-link-link"
					placeholder="{{ $__t('Link') }}">
			</div>
			<input data-userfield-type="link"
				type="hidden"
				class="userfield-input"
				data-userfield-name="{{ $userfield->name }}">
		</div>
	</div>
	@elseif($userfield->type == \Grocy\Services\UserfieldsService::USERFIELD_TYPE_FILE)
	<div class="form-group">
		<label>{{ $userfield->caption }}</label>
		<div class="input-group">
			<div class="custom-file">
				<input type="file"
					class="custom-file-input userfield-input"
					data-userfield-name="{{ $userfield->name }}">
				<label class="custom-file-label"
					for="{{ $userfield->name }}">
					{{ $__t('No file selected') }}
				</label>
			</div>
			<div class="input-group-append userfield-file-delete">
				<span class="input-group-text"><i class="fas fa-trash"></i></span>
			</div>
			<div class="input-group-append">
				<a href="#"
					target="_blank"
					class="input-group-text userfield-file-show d-none"><i class="fas fa-eye"></i></a>
			</div>
		</div>
	</div>
	@elseif($userfield->type == \Grocy\Services\UserfieldsService::USERFIELD_TYPE_IMAGE)
	<div class="form-group">
		<label>{{ $userfield->caption }}</label>
		<div class="input-group">
			<div class="custom-file">
				<input type="file"
					class="custom-file-input userfield-input"
					data-userfield-name="{{ $userfield->name }}">
				<label class="custom-file-label"
					for="{{ $userfield->name }}">
					{{ $__t('No file selected') }}
				</label>
			</div>
			<div class="input-group-append userfield-file-delete">
				<span class="input-group-text"><i class="fas fa-trash"></i></span>
			</div>
		</div>
		<img src=""
			alt="{{ $userfield->name }}"
			class="userfield-current-file userfield-file-show d-none lazy mt-1"
			data-uf-name="{{ $userfield->name }}" />
	</div>
	@endif

	@endforeach

</div>

@endif
