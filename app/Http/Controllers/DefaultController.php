<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use \GuzzleHttp\Client;
use \GuzzleHttp\Exception\RequestException;
use \Guzzle\Http\Exception\ClientErrorResponseException;
use \GuzzleHttp\Exception\ServerException;
use \GuzzleHttp\Exception\BadResponseException;

use \Predis\Client as ClientR;

use \Illuminate\Support\Facades\Redis as Redis;

use \Monolog\Logger;
use \Monolog\Handler\StreamHandler;

use Illuminate\Support\Facades\DB;

use \Carbon\Carbon as Carbon;

class DefaultController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');

    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        return view('home');
    }

    public function fetchUserData(Request $request){

        $response = ['status' => true, 'code' => 200];

        $client = new Client([
            'verify' => false
        ]);

        $clientR = new ClientR([
            'scheme' => 'tcp',
            'host'   => env('REDIS_HOST'),
            'port'   => env('REDIS_PORT'),
            'password' => env('REDIS_PASSWORD'),
            'db' => env('REDIS_DB')
        ]);

        $data = [];

        try{

            $usernames = explode(",",$request->get('usernames'));

            $usernames = array_slice($usernames, 0, 10);

            asort($usernames);

            if(count($usernames) === 1 && $usernames[0] === ''){
                $response['code'] = 416;
                $response['error'] = "No usernames selected.";
                $response['status'] = false;
                \Log::warning($response['error']);
            }else{

                foreach($usernames as $username){

                    //check to see if username's already been cached

                    $hasCache = false;
                    $renewCache = false;

                    $fetchTime = DB::table("fetch_times")->where("username",$username)->first();

                    $fetchTimeDataExists = false;


                    if($fetchTime !== NULL){ //cache exists

                        $fetchTimeDataExists = true;

                        //check how long it's been cached
                        $fetchTimeLastUpdated = Carbon::parse($fetchTime->updated_at)->timestamp;
                        $timeLapsed = microtime(true) - $fetchTimeLastUpdated;

                        if($timeLapsed >= 120){
                            $renewCache = true;
                        }

                    }

                    if(!$renewCache){
                        $cachedUsername = $clientR->get('username:'.$username);     
                    }
        
                    $hasCache = isset($cachedUsername) && gettype($cachedUsername) === 'string';

                    if($hasCache){
                        $res = json_decode($cachedUsername);
                        $data[] = $res;

                    }else{

                        try {
                            $res = $client->request('GET', 'https://api.github.com/users/'.$username, [
                                'headers' => [
                                    'Accept' => 'application/vnd.github.v3+json'
                                ]
                            ]);

                            $content = $res->getBody();

                            $clientR->pipeline(function ($pipe) use ($username,$content,$fetchTimeDataExists){
                                $pipe->set('username:'.$username,$content);
         
                                if($fetchTimeDataExists){
                                    DB::table("fetch_times")->where("username",$username)->update([
                                        'updated_at' => DB::Raw('NOW()')
                                    ]);
                                }else{

                                    DB::table("fetch_times")->insert([
                                        'updated_at' => DB::Raw('NOW()'),
                                        'username' => $username,
                                        'created_at' => DB::Raw('NOW()')
                                    ]);
                                }

                                
                            });

                            $data[] = json_decode($content);

                        }catch(\Exception $e){
                            $clientR->pipeline(function ($pipe) use ($username,$fetchTimeDataExists){
                                $pipe->set('username:'.$username,'account_not_found');
                                if($fetchTimeDataExists){
                                    DB::table("fetch_times")->where("username",$username)->update([
                                        'updated_at' => DB::Raw('NOW()')
                                    ]);
                                }else{
                                    DB::table("fetch_times")->insert([
                                        'updated_at' => DB::Raw('NOW()'),
                                        'username' => $username,
                                        'created_at' => DB::Raw('NOW()')
                                    ]);
                                }
                            });
                            \Log::warning($e->getMessage());
                            $response['code'] = 415;
                            $response['error'] = "Some errors on username data search (username not found, or belongs to an organization).";
                        }

                    }
                    unset($cachedUsername);   
                }
            }

            

            $response['data'] = $data;

        }catch(\Exception $e){
            \Log::error('Programmatic error: ' . $e->getMessage());
            $response['error'] = $e->getMessage();
            $response['code'] = 417;
            $response['status'] = false;
        }

        return response()->json($response);

    }

    public function viewUserList(Request $request){
        $response = ['status' => true, 'code' => 200];



        return response()->json($response);
    }

}
