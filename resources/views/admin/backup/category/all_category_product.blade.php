@extends('admin_layout')
@section('admin_contend')
<div class="table-agile-info">
  <div class="panel panel-default">
    <div class="panel-heading">
      Liệt kê danh mục sản phẩm
    </div>

    <div class="row w3-res-tb">
      <div class="col-sm-5 m-b-xs">
        <select class="input-sm form-control w-sm inline v-middle">
          <option value="0">Bulk action</option>
          <option value="1">Delete selected</option>
          <option value="2">Bulk edit</option>
          <option value="3">Export</option>
        </select>
        <button class="btn btn-sm btn-default">Apply</button>
      </div>
      <div class="col-sm-4">
      </div>
      <div class="col-sm-3">
        <div class="input-group">
          <input type="text" class="input-sm form-control" placeholder="Search">
          <span class="input-group-btn">
            <button class="btn btn-sm btn-default" type="button">Go!</button>
          </span>
        </div>
      </div>
    </div>
    <div class="table-responsive">
                        <?php
                            use Illuminate\Support\Facades\Session;

                            $message = Session::get('message');
                            if($message){
                                echo $message;
                                Session::put('message',null);
                            }
                        ?>
      <table class="table table-striped b-t b-light">
        <thead>
          <tr>
            <th style="width:20px;">
              <label class="i-checks m-b-none">
                <input type="checkbox"><i></i>
              </label>
            </th>
            <th>Tên danh mục</th>
            <th>Thuộc danh mục</th>
            <th>Từ khoá danh mục</th>
            <th>Slug</th>
            <th>Hển thị</th>

            <th style="width:30px;"></th>
          </tr>
        </thead>
        <tbody>
            @foreach($all_category_product as $key => $cate_pro)
          <tr>
            <td><label class="i-checks m-b-none"><input type="checkbox" name="post[]"><i></i></label></td>
            <td>{{ $cate_pro->category_name }}</td>
            <td>
                @if( $cate_pro->category_parent  == 0)
                   <span style="color: red;">---Danh mục cha---</span>
                @else
                    @foreach($category_product as $key => $cate_sub_pro)
                        @if($cate_sub_pro->category_id == $cate_pro->category_parent)
                            <span style="color: green;">{{$cate_sub_pro->category_name}}</span>
                        @endif
                    @endforeach
                @endif

            </td>
            <td>{{ $cate_pro->meta_keywords }}</td>
            <td>{{ $cate_pro->slug_category_product }}</td>
            <td>
                <span class="text-ellipsis">
                <?php
                        if($cate_pro->category_status == 0){
                    ?>
                        <a href="{{URL::to('/unactive-category-product/'.$cate_pro->category_id)}} "><span class = "fa-thumb-styling fa fa-thumbs-down"> </span></a>
                    <?php
                        }else{
                    ?>
                        <a href="{{URL::to('/active-category-product/'.$cate_pro->category_id)}}"><span class = "fa-thumb-styling fa fa-thumbs-up"> </span></a>
                    <?php
                        }
                    ?>
                </span>
            </td>
            <td>
              <a href="{{URL::to('/edit-category-product/'.$cate_pro->category_id)}}" style="font-size: 20px;" class="active styling-edit" ui-toggle-class=""><i class="fa fa-pencil-square-o text-success text-active"></i></a>
              <a  onclick="return confirm('Bạn có chắc chắn muốn xoá?')" href="{{URL::to('/delete-category-product/'.$cate_pro->category_id)}}"  style="font-size: 20px;" class="active styling-edit" ui-toggle-class=""><i class="fa fa-times text-danger text"></i></a>
            </td>
          </tr>
          @endforeach
        </tbody>
      </table>
    </div>
    <footer class="panel-footer">
      <div class="row">

        <div class="col-sm-5 text-center">
          <small class="text-muted inline m-t-sm m-b-sm">showing 20-30 of 50 items</small>
        </div>
        <div class="col-sm-7 text-right text-center-xs">
          <ul class="pagination pagination-sm m-t-none m-b-none">
          {!! $all_category_product->links("pagination::bootstrap-4") !!}
          </ul>
        </div>
      </div>
    </footer>
  </div>
</div>
@endsection
