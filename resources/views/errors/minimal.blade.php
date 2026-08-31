@extends('errors.layout')

@section('code', $exception ? $exception->getStatusCode() : '500')
@section('icon', 'bi-exclamation-octagon-fill')
@section('title', $exception ? ($exception->getMessage() ?: __('errors.generic_title')) : __('errors.generic_title'))
@section('subtitle', __('errors.generic_subtitle'))
@section('message', __('errors.generic_message'))
