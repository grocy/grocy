@extends('layout.default')

@section('title', $__t('Stock journal summary'))
@section('activeNav', '')
@section('viewJsName', 'stockjournalsummary')

@section('content')
<div class="row">
	<div class="col">
		<h2 class="title">@yield('title')</h2>
	</div>
</div>

<hr class="my-2 py-1">

<div class="row">
	<div class="col">
		<table id="journal-summary-table"
			class="table table-sm table-striped dt-responsive">
			<thead>
				<tr>
					<th>{{ $__t('Product') }}</th>
					<th>{{ $__t('Booking type') }}</th>
					<th>{{ $__t('User') }}</th>
					<th>{{ $__t('Amount') }}</th>
				</tr>
			</thead>
			<tbody class="d-none">
				@foreach($entries as $journalEntry)
				<tr>
					<td>
						{{ $journalEntry->product_name }}
					</td>
					<td>
						{{ $__t($journalEntry->transaction_type) }}
					</td>
					<td>
						{{ $journalEntry->user_display_name }}
					</td>
					<td>
						<span class="locale-number locale-number-quantity-amount">{{ $journalEntry->amount }}</span> {{ $__n($journalEntry->amount, $journalEntry->qu_name, $journalEntry->qu_name_plural) }}
					</td>
				</tr>
				@endforeach
			</tbody>
		</table>
	</div>
</div>
@stop
