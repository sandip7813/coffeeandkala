@extends('adminlte::page')

@section('title', 'Dashboard')

@section('content_header')
    <h3 class="mb-0">Dashboard</h3>
@stop

@section('content')
    <div class="row">
        <div class="col-12 col-sm-6 col-xl-3">
            <x-adminlte-info-box title="Welcome" text="Coffee & Kala" icon="bi bi-cup-hot" theme="primary" />
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Admin panel</h3>
        </div>
        <div class="card-body">
            <p class="mb-0">AdminLTE 4 is installed. Configure the sidebar menu in <code>config/adminlte.php</code>.</p>
        </div>
    </div>
@stop
