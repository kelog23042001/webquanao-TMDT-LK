@extends('admin_layout')
@section('admin_contend')
<div class="page-title">
    <div class="row">
        <div class="col-12 col-md-6 order-md-1 order-last">
            <h3>Thêm Mã Giảm GIá</h3>
            <!-- <p class="text-subtitle text-muted">For user to check they list</p> -->
        </div>
        <div class="col-12 col-md-6 order-md-2 order-first">
            <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                <!-- <a href="{{URL::to('/add-category-product')}}" class="btn btn-success">Add Category</a> -->
                <ol class="breadcrumb">
                    <!-- <li class="breadcrumb-item"><a href="index.html">Dashboard</a></li> -->
                    <!-- <li class="breadcrumb-item active" aria-current="page">DataTable</li> -->
                </ol>
            </nav>
        </div>
    </div>
    <?php

    use Illuminate\Support\Facades\Session;

    $message = Session::get('message');
    if ($message) {
        echo $message;
        Session::put('message', null);
    }
    ?>
</div>
<div class="card-body">
    <form action="{{URL::to('/insert-coupon-code')}}" method="post" enctype="multipart/form-data">
        @csrf

        <div class="form-group">
            <label for="exampleInputEmail1">Tên mã giảm giá</label>
            <input type="text" name="coupon_name" class="form-control" id="exampleInputEmail1"
                >
        </div>
        <div class="form-group">
            <label for="exampleInputEmail1">Mã giảm giá</label>
            <input type="text" name="coupon_code" class="form-control" id="exampleInputEmail1" required
                autocomplete="coupon_code">
            @error('coupon_code')
            <div class="alert alert-danger">{{ $message }}</div>
            @enderror
        </div>
        <div class="form-group">
            <label for="exampleInputPassword1">Số lượng</label>
            <input type="text" name="coupon_time" class="form-control" id="exampleInputEmail1">
        </div>
        <div class="form-group">
            <label for="exampleInputPassword1">Tính năng mã</label>
            <select name="coupon_condition" class="form-control input-sm m-bot15">
                <option value="1">Giảm theo phần trăm</option>
                <option value="2">Giảm theo tiền</option>
            </select>
        </div>
        <div class="form-group">
            <label for="exampleInputPassword1">Nhập số % hoặc tiền giảm</label>
            <input type="text" name="coupon_number" class="form-control" id="exampleInputEmail1">
        </div>
        <div class="form-group">
            <label for="exampleInputPassword1">Ngày bắt đầu</label>
            <input type="text" name="coupon_date_start" class="form-control" id="datepicker2">
        </div>
        <div class="form-group">
            <label for="exampleInputPassword1">Ngày hết hạn</label>
            <input type="text" name="coupon_date_end" class="form-control " id="datepicker1">
        </div>
        <button type="submit" name="add_coupon" class="btn btn-info">Thêm mã giảm giá </button>



    </form>
</div>

@endsection