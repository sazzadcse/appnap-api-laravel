<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\URL;

class ProductController extends Controller {

    protected $_success_response_code = 1000; //success;
    protected $_error_response_code   = 4000; //error;
    protected $_db_response_code      = 1004; //database error ;
    protected $_no_data_response_code = 4004; //no data available ;
    protected $_validation_error_code = 1002; //validation ;

    const RESPONSE_TYPE_SUCCESS    = 'success';
    const RESPONSE_TYPE_ERROR      = 'error';
    const RESPONSE_TYPE_USER_ERROR = 'userError';
    const HTTP_NOT_FOUND           = 404;
    const HTTP_OK                  = 200;

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index() {
        $user = Auth::user();
        if( $user ){
            $products = Product::where('user_id', $user->id)->get();

            $data = [];
            //URL::to('/');
            $totalProducts = count($products);
            if( $totalProducts > 0 ){

                foreach( $products as $product ) {
                    $data[] = [
                        'id' => $product->id,
                        'name' => $product->name,
                        'price' => $product->price,
                        'category' => $product->category,
                        'image' => env('APP_URL') . '/public/images/' . $product->image,
                    ];
                }

                return response()->json( [
                    'code'      => $this->_success_response_code,
                    'status'    => self::RESPONSE_TYPE_SUCCESS,
                    'message'   => 'Product Found!',
                    'total'     => $totalProducts,
                    'data'      => $data
                ] );
            }else{
                return response()->json( [
                    'code'      => $this->_success_response_code,
                    'status'    => self::RESPONSE_TYPE_SUCCESS,
                    'message'   => 'Product Not Found!',
                    'total'     => 0,
                    'data'      => $data
                ] );
            }

        }

        return response()->json( [
            'code'      => $this->_success_response_code,
            'status'    => self::RESPONSE_TYPE_SUCCESS,
            'message'   => 'Product Not Found!',
            'total'     => 0,
            'data'      => $data
        ] );
    }
    
    public function get_all_products() {

        $products = Product::get();

        $data = [];
        $totalProducts = count($products);
        if( $totalProducts > 0 ){

            foreach( $products as $product ) {
                $data[] = [
                    'id' => $product->id,
                    'name' => $product->name,
                    'price' => $product->price,
                    'category' => $product->category,
                    'image' => env('APP_URL') . '/public/images/' . $product->image,
                ];
            }

            return response()->json( [
                'code'      => $this->_success_response_code,
                'status'    => self::RESPONSE_TYPE_SUCCESS,
                'message'   => 'Product Found!',
                'total'     => $totalProducts,
                'data'      => $data
            ] );
        }

        return response()->json( [
            'code'      => $this->_success_response_code,
            'status'    => self::RESPONSE_TYPE_SUCCESS,
            'message'   => 'Product Not Found!',
            'total'     => 0,
            'data'      => $data
        ] );

    }


    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request) {

        $validator = Validator::make( $request->all(), [
            'name' => 'required',
            'price' => 'required',
            'category' => 'required',
            'image' => 'required|image|mimes:png,jpg,jpeg|max:2048',
        ] );

        if( $validator->fails() ){
            return response()->json( [
                'code'    => $this->_validation_error_code,
                'status'  => self::RESPONSE_TYPE_ERROR,
                'message' => $validator->errors(),
                'data'    => []
            ] );
        }

        $imageName = time() .'.'. $request->image->extension();

        $request->image->move(public_path('images'), $imageName);

        $data = $request->all();
        $data['user_id'] = auth()->id();
        $data['image'] = $imageName;

        $product = Product::create($data);

        if($product){

            return response()->json( [
                'code'    => $this->_success_response_code,
                'status'  => self::RESPONSE_TYPE_SUCCESS,
                'message' => 'Product Created successfully!',
                'data'    => $product
            ] );

        }

        return response()->json( [
            'code'    => $this->_error_response_code,
            'status'  => self::RESPONSE_TYPE_ERROR,
            'message' => 'Product Not Created!',
            'data'    => []
        ] ); 


    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
