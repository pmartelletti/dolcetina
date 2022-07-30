@extends('admin::layouts.content')

@section('page_title')
    {{ __('admin::app.customers.addresses.edit-title') }}
@stop


@section('content')

    <div class="content">
        <form method="POST" action="{{ route('admin.customer.notes.update', ['customer' => $customer, 'note' => $note]) }}">
            <div class="page-header">
                <div class="page-title">
                    <h1>
                        <i class="icon angle-left-icon back-link" onclick="window.location = '{{ route('admin.customer.notes.index', ['id' => $customer->id]) }}'"></i>

                        {{ __('admin::app.customers.notes.title', ['customer_name' => $customer->first_name . ' ' . $customer->last_name]) }}
                    </h1>
                </div>

                <div class="page-action">
                    <button type="submit" class="btn btn-lg btn-primary">
                        {{ __('admin::app.customers.notes.save-note') }}
                    </button>
                </div>
            </div>

            <div class="page-content">
                <div class="form-container">
                    @csrf()

                    <input name="_method" type="hidden" value="PUT">

                    <div class="control-group" :class="[errors.has('channel_id') ? 'has-error' : '']">
                        <label for="notes">{{ __('admin::app.customers.note.enter-note') }} for {{ $customer->name }}</label>

                        <textarea class="control" style="height: 300px" name="notes" v-pre rows="10">{{ old('notes') ?: $note->note }}</textarea>

                        <span class="control-error" v-if="errors.has('notes')">@{{ errors.first('notes') }}</span>
                    </div>
                </div>
            </div>
        </form>
    </div>
@stop