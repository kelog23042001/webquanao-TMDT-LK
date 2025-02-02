<?php

namespace App\Http\Controllers;

use App\Models\CategoryPost;
use App\Models\Comment;
use App\Models\Rate;
use App\Models\Gallery;
use App\Models\Product;
use App\Models\CategoryProductModel;
use CloudinaryLabs\CloudinaryLaravel\Facades\Cloudinary;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Session;

session_start();
class ProductController extends Controller
{
    public function reply_comment(Request $request)
    {
        $data = $request->all();
        $comment = new Comment();
        $comment->comment = $data['comment'];
        $comment->comment_name = 'Admin';
        $comment->comment_product_id = $data['comment_product_id'];
        $comment->comment_status = 0;
        $comment->comment_parent_comment = $data['comment_id'];
        $comment->save();
    }
    public function allow_comment(Request $request)
    {
        $data = $request->all();
        $comment = Comment::find($data['comment_id']);
        $comment->comment_status = $data['comment_status'];
        $comment->save();
    }
    public function list_comment()
    {
        $comment = Comment::with('product')->where('comment_parent_comment', '=', 0)
            ->orderBy('comment_status', 'desc')->orderBy('comment_date', 'desc')->get();
        $comment_rep = Comment::with('product')->where('comment_parent_comment', '>', 0)->get();

        return view('admin.comment.list_comment', compact('comment', 'comment_rep'));
    }

    public function send_comment(Request $request)
    {
        // dd($request->all());
        $product_id = $request->product_id;
        $comment_name = $request->comment_name;
        $comment_email = $request->comment_email;
        $comment_content = $request->comment_content;
        $comment = new Comment();
        $comment->comment = $comment_content;
        $comment->comment_name = $comment_name;
        $comment->comment_email = $comment_email;
        $comment->comment_product_id = $product_id;
        $comment->comment_status = 1;
        $comment->save();
    }

    public function load_comment(Request $request)
    {
        $product_id = $request->product_id;
        $rates = Rate::where('product_id', $product_id)->orderBy('rating_id', 'desc')->get();
        $avgRating = $rates->avg('rating');
        $avgRating = round($avgRating, 1);
        $output = '';
        foreach ($rates as $key => $review) {
            $output .= '<li>
            <div class="review-heading">
                <h5 class="name">' . $review->user->name . '</h5>
                <p class="date">' . $review->created_at . '</p>
                <div class="review-rating">';
            for ($i = 0; $i < $review->rating; $i++) {
                $output .= '<i class="fa fa-star"></i>';
            }
            $output .= '</div>
            </div>
            <div class="review-body">
                <p>';
            if (empty($review->comment)) {
                $output .= 'Người dùng không bình luận gì!';
            } else if ($review->visible == 0) {
                $output .= 'Nội dung bình luận đã bị ẩn!';
            } else {
                $output .= $review->comment;
            }
            $output .= '</p>
            </div>
        </li>';
        }
        return response()->json([
            'output' => $output,
            'rates' => $rates,
            'avgRating' => $avgRating
        ]);
    }
    public function add_product()
    {
        $cate_product = DB::table('tbl_category_product')->orderBy('category_id', 'desc')->get();
        $brand_product = DB::table('tbl_brand_product')->orderBy('brand_id', 'desc')->get();
        return view('admin.product.add_product', compact('cate_product', 'brand_product'));
    }

    public function all_product()
    {
        $all_product = DB::table('tbl_product')
            ->join('tbl_category_product', 'tbl_category_product.category_id', '=', 'tbl_product.category_id')->where('deleted', 0)
            ->get();
        return view('admin.product.all_product', compact('all_product'));
    }
    public function tab_new_product(Request $request)
    {
        $data = $request->all();
        $products = Product::where('category_id', $data['category_id'])->get();
        $output = '';
        $product_count = $products->count();
        if ($product_count > 0) {
            $output .= '<div class="products-slick" data-nav="#slick-nav-1">';
            foreach ($products as $key => $product) {
                $output .= '
                <div class="product">
                    <a class="cart_product_url_{{$product->product_id}}" href="">
                        <div class="product-img">
                            <img src="{{$product->product_image}}" alt="">
                            <div class="product-label">
                                <span class="sale">-30%</span>
                                <span class="new">NEW</span>
                            </div>
                        </div>
                    </a>
                    <div class="product-body">
                        <p class="product-category">{{$product->category->category_name}}</p>
                        <h3 class="product-name"><a href="#">{{$product->product_name}}</a></h3>
                        <h4 class="product-price"></h4>
                        <div class="product-rating">
                            <i class="fa fa-star"></i>
                            <i class="fa fa-star"></i>
                            <i class="fa fa-star"></i>
                            <i class="fa fa-star"></i>
                            <i class="fa fa-star"></i>
                        </div>
                        <div class="product-btns">
                            <button class="add-to-wishlist" id="{{$product->product_id}}" onclick="add_wistlist(this.id)">
                                <i class=" fa fa-heart-o"></i>
                                <span class="tooltipp">add to wishlist</span>
                            </button>
                            <button class="add-to-compare" id="{{$product->product_id}}" data-toggle="modal" onclick="add_compare(this.id)" data-target="#compareModal">
                                <i class="fa fa-exchange"></i>
                                <span class="tooltipp">so sánh</span>
                            </button>
                        </div>
                    </div>
                    <div class="add-to-cart">
                        <button class="add-to-cart-btn" id="{{$product->product_id}}" onclick="Addtocart(this.id);"><i class="fa fa-shopping-cart"></i> add to cart</button>
                    </div>
                </div>';
            }
            $output .= '</div>';
            $output .= '<div id="slick-nav-1" class="products-slick-nav"></div>';
        }
        echo $output;
    }
    public function save_product(Request $request)
    {
        // dd($request->all());
        try {
            $data = array();
            $this->validate($request, [
                'product_name' => ['required', 'max:255', 'unique:tbl_product'],
            ]);
            // $data['product_tags']    = $request->product_tags;
            $data['product_name']    = $request->product_name;
            $data['product_slug']    = $request->product_slug;
            $data['product_quantity'] = $request->product_quantity;
            $data['product_price']   = $request->product_price;
            $data['price_cost']   = $request->price_cost;
            $data['product_desc']    = $request->description;
            $data['category_id']     = $request->product_cate;
            $data['product_status']  = 1;

            $thumbnailUrl = Cloudinary::upload($request['product_image'][0]->getRealPath(), [
                'folder' => 'Products',
            ])->getSecurePath();
            $data['product_image'] = $thumbnailUrl;
            $product = new Product();
            // dd($product->insertGetId($data));
            $newProduct = $product->insertGetId($data);

            foreach ($request->file('product_image') as $key => $file) {
                $uploadedImages = Cloudinary::upload($file->getRealPath(), [
                    'folder' => 'Products',
                ])->getSecurePath();

                $image = new Gallery();
                $image::create([
                    'imageUrl' => $uploadedImages,
                    'product_id' => $newProduct,
                ]);
                // dd($image);
            }
            Session::put('message', 'Thêm sản phẩm thành công');
        } catch (Exception $e) {
            dd($e);
            Session::put('message', 'Thêm sản phẩm Không thành công');
        }
        return Redirect::to('/all-product');
    }

    public function unactive_product($product_id)
    {
        DB::table('tbl_product')->where('product_id', $product_id)->update(['product_status' => 0]);
        return Redirect::to('/all-product');
    }

    public function active_product($product_id)
    {
        DB::table('tbl_product')->where('product_id', $product_id)->update(['product_status' => 1]);
        return Redirect::to('/all-product');
    }

    public function edit_product($product_id)
    {
        $categories = CategoryProductModel::orderBy('category_id', 'desc')->get();
        // $product = Product::where('product_id', $product_id)->first();
        $product = Product::find($product_id);
        return view('admin.product.edit_product', compact('product', 'categories'));
    }

    public function delete_product($product_id)
    {
        Product::where('product_id', $product_id)->update(['deleted' => 1]);
        Session::put('message', 'Xoá danh mục sản phẩm thành công');
        return Redirect::to('/all-product');
    }

    public function update_product(Request $request, $product_id)
    {
        // dd($request->all());;

        $data = array();
        // $data['product_tags']    = $request->product_tags;
        $data['product_name']    = $request->product_name;
        $data['product_slug']    = $request->product_slug;
        $data['product_quantity']    = $request->product_quantity;
        $data['product_desc']    = $request->description;
        $data['product_price']   = $request->product_price;
        $data['price_cost']   = $request->price_cost;
        $data['category_id']     = $request->product_cate;
        if ($request->file('product_image')) {
            $uploadedImages = Cloudinary::upload($request->file('product_image')->getRealPath(), [
                'folder' => 'Products',
            ])->getSecurePath();
            $data['product_image']     = $uploadedImages;
        }
        Product::where('product_id', $product_id)->update($data);
        Session::put('message', 'Cập nhập sản phẩm thành công');
        return Redirect::to('/all-product');
    }
    //end admin page

    public function details_product($product_id, Request $request)
    {
        $category_post = CategoryPost::orderby('cate_post_id', 'DESC')->paginate(5);

        $categories = DB::table('tbl_category_product')->where('category_status', 1)->orderBy('category_name', 'asc')->get();
        $brand = DB::table('tbl_brand_product')->where('brand_status', '0')->orderBy('brand_id', 'desc')->get();

        $detail_product = Product::find($product_id);

        // dd($detail_product->rates[0]->comment);
        $rating = Rate::where('product_id', $product_id)->avg('rating');
        $rating = round($rating, 1);

        $rates = Rate::where('product_id', $product_id)->paginate(5);

        // dd($rating);
        $product_image = $detail_product->product_image;
        $product_id = $detail_product->product_id;
        $category_id = $detail_product->category_id;
        $product_cate = $detail_product->category->category_name;

        $meta_decs = $detail_product->product_desc;
        $meta_title =  $detail_product->product_name;
        $meta_keyword =  $detail_product->product_slug;
        $url_canonical = $request->url();

        $related_product = Product::join('tbl_category_product', 'tbl_category_product.category_id', '=', 'tbl_product.category_id')
            ->where('tbl_category_product.category_id', $category_id)->whereNotIn('tbl_product.product_id', [$product_id])->get();
        // dd($related_product);
        $product = Product::find($product_id);
        $product->product_views = $product->product_views + 1;
        $product->save();

        return view('user.pages.product.show_detail', compact('rates', 'product', 'product_cate', 'categories', 'rating', 'category_id', 'detail_product', 'related_product', 'category_post'))
            ->with('meta_decs', $meta_decs)->with('meta_title', $meta_title)->with('meta_keyword', $meta_keyword)
            ->with('url_canonical', $url_canonical);
    }
    public function tag(Request $request, $product_tag)
    {
        $category_post = CategoryPost::orderby('cate_post_id', 'DESC')->where('cate_post_status', "0")->get();

        $categories = CategoryProductModel::where('category_status', '1')->orderBy('category_id', 'desc')->get();
        $brand = DB::table('tbl_brand_product')->where('brand_status', '1')->orderBy('brand_id', 'desc')->get();
        $tag = str_replace("-", "", $product_tag);

        $pro_tag = Product::where('product_status', 1)
            ->where('product_name', 'LIKE', '%' . $tag . '%')
            ->orWhere('product_tags', 'LIKE', '%' . $tag . '%')
            ->get();
        $meta_decs = 'Tag: ' . $product_tag;
        $meta_title =  'Tag: ' . $product_tag;
        $meta_keyword =  'Tag: ' . $product_tag;
        $url_canonical = $request->url();
        return view('user.pages.product.tag')
            ->with('category_post', $category_post)
            ->with('categories', $categories)
            ->with('brand', $brand)
            ->with('meta_decs', $meta_decs)
            ->with('meta_title', $meta_title)
            ->with('meta_keyword', $meta_keyword)
            ->with('url_canonical', $url_canonical)
            ->with('product_tag', $product_tag)
            ->with('pro_tag', $pro_tag);
    }

    public function rating(Request $request)
    {
        $data = $request->all();
        // dd($data);
        $rating = new Rate();
        $rating->product_id = $data['product_id'];
        $rating->rating = $data['rating'];
        $rating->user_id = $data['user_id'];
        $rating->comment = $data['comment_content'];
        $rating->save();
    }
}
