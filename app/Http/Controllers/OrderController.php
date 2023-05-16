<?php

namespace App\Http\Controllers;

use App\Mail\OrderMadeEmail;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

use Illuminate\Validation\ValidationException;

class OrderController extends Controller
{
    public function index()
    {
        try {
            $orders = Order::all();
            return response()->json($orders);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to retrieve orders'], 500);
        }
    }

    public function show($id)
    {
        try {            
            $order = Order::findOrFail($id);
            return response()->json($order);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 404);
        }
    }

    public function store(Request $request)
{
    try {        
        // Validate the USSD request input
   /*      $request->validate([
            'sessionId' => 'required',
            'phone_number' => 'required',
            'user_input' => 'required',
        ]); */
       
        $sessionId = $request->input('sessionId');
        $serviceCode = $request->input('serviceCode');
        $phoneNumber = $request->input('phoneNumber');
        $text = $request->input('text');
        
        // Remove any leading or trailing whitespaces and convert to uppercase
        $text = trim($text);
        $text = strtoupper($text);

        // Start USSD response
        $ussdResponse = '';

        // Check the text input to determine the USSD logic
        $cacheKey = $sessionId;        
        if(!Cache::has($cacheKey) && $text === ''){
            $sessionData = Cache::put($cacheKey, [
                "product_id"=>0,
                "quantity"=>0,
                "date"=>0
            ],240);            
            // Initial menu
            $user = User::where('ussd',$serviceCode)->with('products')->first();
    
            $ussdResponse = "CON Welcome to RapidPurchase!\n";
            $ussdResponse .= "Select a product\n";
            $index = 0;
            foreach($user->products as $product){
                $index++;
                $ussdResponse .= "$index. $product->name \n";
            }                        
        }else{
            $sessionData = Cache::get($cacheKey);
            
            if ($sessionData["product_id"] == 0) {
                
                $index = intval($text)-1;
                
                $user = User::where('ussd',$serviceCode)->with('products')->first();
                $product = $user->products[$index];
            
                $ussdResponse = "CON Enter quantity of $product?->name !\n"; 
                Cache::put($cacheKey, [
                        "product_id" => $product->id,
                        "quantity"=>0,
                        "date"=>0
                    ], 120);                                
            }elseif ($sessionData["quantity"] == 0) {
                $text = explode('*',$text)[1];
                $ussdResponse = "CON Please select date to deliver !\n";
                $ussdResponse .= "1. Today !\n";
                $ussdResponse .= "2. Tomorrwo !\n";
                $ussdResponse .= "3. Next Tomorrow !\n";
                $ussdResponse .= "4. Next 2 Days !\n";
                $ussdResponse .= "5. Next 3 Days !\n";
                Cache::put($cacheKey,[
                        "product_id" => $sessionData["product_id"],
                        "quantity"=>intval($text),
                        "date"=>0
                    ],120);             
            }elseif ($sessionData["date"]  == 0) {
                  // Extract the product ID and quantity from the user input                                                        
                $product = Product::where('id', $sessionData['product_id'])->with('user')->first();
                $customer = User::where('phone_number', $phoneNumber)->first();
                $text = explode('*',$text)[2];
                if ($text == '1') {
                    $deliveryDate = Carbon::today();
                } elseif ($text == '2') {
                    $deliveryDate = Carbon::tomorrow();
                } elseif ($text == '3') {
                    $deliveryDate = Carbon::tomorrow()->addDay();
                } elseif ($text == '4') {
                    $deliveryDate = Carbon::tomorrow()->addDays(2);
                } elseif ($text == '5') {
                    $deliveryDate = Carbon::tomorrow()->addDays(3);
                } else {
                    // Invalid input
                    // Handle the error case accordingly
                }
                $order = Order::create([
                    'user_id' => $customer?->id,
                    'product_id' => $sessionData["product_id"],
                    'quantity' => $sessionData["quantity"],
                    'unit_price' => $product->price,
                    'date_needed' => $deliveryDate,
                    "phone_number" => $phoneNumber,
                ]);
                $order = Order::with('product.user')->where('id',$order->id)->first();
                // Send email to the product user
                $product = $order->product;
                    // Send email to the product user                                
                Mail::to($product->user->email)->send(new OrderMadeEmail($order));
                $ussdResponse = "END Order Completed.";
            }else {
                // Invalid input
                $ussdResponse = "END Invalid input. Please try again.";
            }

        }

        // Implement the logic to handle USSD input and generate the appropriate response
     
        return $ussdResponse;
    
      
    } catch (ValidationException $e) {
        return 'CON Failed to process USSD request, Invlid fields /n';        
    } catch (\Exception $e) {
        Log::error($e->getMessage());
        return "CON ".$e->getMessage()." \n";
    }
}

    public function storeN(Request $request)
    {
        try {
            $validatedData = $request->validate([
                'user_id' => 'required',
                'product_id' => 'required',
                'quantity' => 'required',
                'unit_price' => 'required',
                'date_needed' => 'required',
                // Add validation rules for other fields as needed
            ]);
    
            $order = Order::create($validatedData);
            $order = Order::with('product.user')->where('id',$order->id)->first();
            // Send email to the product user
            $productUser = $order->product->user;
            Mail::to($productUser->email)->send(new OrderMadeEmail($order));
    
            return "END Thank you, we will get back to you";
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to create order'], 500);
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $validatedData = $request->validate([
                'customer_id' => 'required',
                'product_id' => 'required',
                'quantity' => 'required',
                'unit_price' => 'required',
                'date_needed' => 'required',
                // Add validation rules for other fields as needed
            ]);

            $order = Order::findOrFail($id);
            $order->update($validatedData);

            return response()->json($order);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to update order'], 500);
        }
    }

    public function destroy($id)
    {
        try {
            $order = Order::findOrFail($id);
            $order->delete();

            return response()->json(['message' => 'Order deleted successfully']);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to delete order'], 500);
        }
    }
}
