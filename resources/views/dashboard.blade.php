@extends('layouts.main')

@section('content')
<div class="container mt-4">
    <div class="row row-cols-1 row-cols-md-4 g-4">
      <!-- Card Total Obat -->
      <div class="col">
        <div class="card text-white" style="background-color:#3B82F6;">
          <div class="card-body">
            <h5 class="card-title text-white">Total Obat</h5>
            <strong><p class="fs-2">{{ $totalObat }}</p></strong>
          </div>
        </div>
      </div>
  
      <!-- Card Obat Hampir Habis -->
      <div class="col">
        <div class="card text-dark" style="background-color:#FACC15;">
          <div class="card-body">
            <h5 class="card-title ">Obat Hampir Habis</h5>
            <strong><p class="fs-2">{{ $obatHampirHabis }}</p></strong>
          </div>
        </div>
      </div>
  
      <!-- Card Obat Expired -->
      <div class="col">
        <div class="card text-white" style="background-color:#EF4444;">
          <div class="card-body">
            <h5 class="card-title text-white">Obat Kedaluwarsa</h5>
            <strong><p class="fs-2">{{ $obatKedaluwarsa }}</p></strong>
          </div>
        </div>
      </div>
  
      <!-- Card Transaksi Hari Ini -->
      <div class="col">
        <div class="card text-white" style="background-color:#8B5CF6;">
          <div class="card-body">
            <h5 class="card-title text-white">Transaksi Hari Ini</h5>
            <strong><p class="fs-2">{{ $transaksiHariIni }}</p></strong>
          </div>
        </div>
      </div>
    </div>
  </div>
  
@endsection