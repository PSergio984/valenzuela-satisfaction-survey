@extends('errors::minimal')

@section('title', __('Unauthorized'))
@section('code', '401')
@section('message', __('You need to be logged in to access this page. Please sign in and try again.'))
