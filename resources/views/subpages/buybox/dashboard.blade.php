@extends('layout.app')
@extends('inc.side_menu')
@extends('inc.nav_bar')
@extends('modal.logout')
@section('title', $pageTitle)
@section('content')
    <!-- Begin Page Content -->
    <div class="container-fluid">
        <!-- Begin Breadcrumb -->
    {{ Breadcrumbs::render('buybox_dashboard') }}
    <!-- End Breadcrumb -->
        <!-- Page Heading -->
        <div class="d-sm-flex align-items-center justify-content-between mb-4">
            <h1 class="h3 mb-0 text-gray-800">{{isset($pageHeading)?$pageHeading:''}}</h1>
        </div>
        <!-- Content Row -->
        <div class="row">

        </div>
    </div>
    <!-- /.container-fluid -->
@endsection
