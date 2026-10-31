@extends('errors.layout')

@section('code', '422')
@section('title', __('ui.Request Could Not Be Processed'))
@section('message', $exception->getMessage() ?: __('ui.The submitted data could not be processed.'))
