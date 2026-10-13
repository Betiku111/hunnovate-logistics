@extends('layouts.public')

@section('title', 'Hunnovate Logistics — Fast, Reliable Delivery in Nigeria')
@section('meta_description', 'Same-day, intercity, and business delivery with live tracking, verified riders, and transparent pricing. Motorcycle, Car, and Truck services across Nigeria.')

@section('content')
    @include('home.sections.hero')
    @include('home.sections.vehicle-cards')
    @include('home.sections.services')
    @include('home.sections.how-it-works')
    @include('home.sections.why-choose-us')
    @include('home.sections.tracking-preview')
    @include('home.sections.statistics', ['stats' => $stats])
    @include('home.sections.testimonials', ['testimonials' => $testimonials])
    @include('home.sections.service-areas', ['serviceAreas' => $serviceAreas])
    @include('home.sections.faq')
    @include('home.sections.final-cta')
@endsection