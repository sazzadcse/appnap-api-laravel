<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use Laravel\Passport\HasApiTokens;

class UserController extends Controller
{

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
    
    //define user registration
    public function user_register(Request $requiest) {
        
        $validator = Validator::make( $requiest->all(), [
            'name' => 'required',
            'email' => 'required|email|unique:users',
            'phone' => 'required',
            'password' => 'required|min:6',
        ] );

        if( $validator->fails() ){
            return response()->json( [
                'code'    => $this->_validation_error_code,
                'status'  => self::RESPONSE_TYPE_ERROR,
                'message' => $validator->errors(),
                'data'    => []
            ] );
        }

        $data = $requiest->all();

        if( $requiest->password !== $requiest->confirm_password){
            return response()->json( [
                'code'    => $this->_validation_error_code,
                'status'  => self::RESPONSE_TYPE_ERROR,
                'message' => 'Password dose not match!',
                'data'    => []
            ] );
        }

        $data['password'] = Hash::make( $requiest->password );

        $user = User::create( $data );

        if( $user ){
            return response()->json( [
                'code'    => $this->_success_response_code,
                'status'  => self::RESPONSE_TYPE_SUCCESS,
                'message' => 'User registration successfully!',
                'data'    => $user
            ] ); 
        }

        return response()->json( [
            'code'    => $this->_error_response_code,
            'status'  => self::RESPONSE_TYPE_ERROR,
            'message' => 'User registration fail!',
            'data'    => []
        ] ); 

    }

    // login
    public function login(Request $requiest) {
        
        $validator = Validator::make( $requiest->all(), [
            'email' => 'required|email',
            'password' => 'required|min:6',
        ] );

        if( $validator->fails() ){
            return response()->json( [
                'code'    => $this->_validation_error_code,
                'status'  => self::RESPONSE_TYPE_ERROR,
                'message' => $validator->errors(),
                'data'    => []
            ] );
        }

        //login
        if( Auth::attempt( [ 'email' => $requiest->email, 'password' => $requiest->password ] ) ){
            $user = Auth::user();
            $token = $user->createToken('usertoken')->accessToken;
            // $token = $user->createToken('usertoken')->accessToken;
            $user['token'] = $token;
            return response()->json( [
                'code'    => $this->_success_response_code,
                'status'  => self::RESPONSE_TYPE_SUCCESS,
                'message' => 'User Login Successfully!',
                'login'   => true,
                'token'   => $token,
                'data'    => $user
            ] ); 

        } else {

            return response()->json( [
                'code'    => $this->_error_response_code,
                'status'  => self::RESPONSE_TYPE_ERROR,
                'message' => 'Oppos! Email or Password invalid',
                'login'   => false,
                'token'   => $token,
                'data'    => $user
            ] ); 

        }


    }

    public function userDetails() {
       
        $user = Auth::user();

        if( $user ){
            return response()->json( [
                'code'    => $this->_success_response_code,
                'status'  => self::RESPONSE_TYPE_SUCCESS,
                'message' => 'Data found successfully',
                'data'    => $user
            ] ); 
        }else{

            return response()->json( [
                'code'    => $this->_error_response_code,
                'status'  => self::RESPONSE_TYPE_ERROR,
                'message' => 'Data not found',
                'data'    => []
            ] ); 

        }

    }

    public function logout()
    {
        $logout = Auth::user()->token()->revoke();
        if( $logout ){
            return response()->json( [
                'code'    => $this->_success_response_code,
                'status'  => self::RESPONSE_TYPE_SUCCESS,
                'message' => 'Logout Successfully',
                'data'    => []
            ] ); 
        }else{
            return response()->json( [
                'code'    => $this->_error_response_code,
                'status'  => self::RESPONSE_TYPE_ERROR,
                'message' => 'User not Logout!',
                'data'    => []
            ] ); 
        }
    }

}
