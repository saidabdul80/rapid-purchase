<?php

namespace App\Http\Controllers;

use App\Mail\OrderMadeEmail;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
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
            return response()->json(['error' => 'Order not found'], 404);
        }
    }

    public function store(Request $request)
{
    try {        
        // Validate the USSD request input
        $request->validate([
            'session_id' => 'required',
            'phone_number' => 'required',
            'user_input' => 'required',
        ]);
       
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
        $sessionKey = 'ussd_session_' . $phoneNumber;
        if(!session()->has($sessionKey) && $text === ''){
            $sessionData = session($sessionKey, [
                "product_id"=>0,
                "quantity"=>0,
                "date"=>""
            ]);
            // Initial menu
            $user = User::where('ussd',$serviceCode)->with('products')->first();
    
            $ussdResponse = "CON Welcome to RapidPurchase!\n";
            $ussdResponse .= "Select a product\n";
            $index = 0;
            foreach($user->products as $product){
                $index++;
                $ussdResponse .= "$index. $product->name .\"\n";
            }                        
        }else{
            $sessionData = session($sessionKey);
            if ($sessionData["product_id"] == 0) {
                
                $index = intval($text)-1;
                $user = User::where('ussd',$serviceCode)->with('products')->first();
                $product = $user->products[$index];
                $sessionData["product_id"] = $product->id;
                $ussdResponse = "CON Enter quantity of $product?->name !\n";                 
                session($sessionKey, $sessionData);                
            }elseif ($sessionData["quantity"] == 0) {

                $sessionData["quantity"]  = $text;
                session($sessionKey, $sessionData);
                $ussdResponse = "CON Please select date to deliver !\n";
                $ussdResponse .= "1. Today !\n";
                $ussdResponse .= "2. Tomorrwo !\n";
                $ussdResponse .= "3. Next Tomorrow !\n";
                $ussdResponse .= "4. Next 2 Days !\n";
                $ussdResponse .= "5. Next 3 Days !\n";

            }elseif ($sessionData["date"]  === "") {
                  // Extract the product ID and quantity from the user input                                                                           
                $product = Product::where('id', $sessionData['product_id'])->with('user')->first();
                $order = Order::create([
                    'user_id' => $product->user->id,
                    'product_id' => $sessionData["product_id"],
                    'quantity' => $sessionData["quantity"],
                    'unit_price' => $product->price * $sessionData["quantity"],
                    'date_needed' => $sessionData["date"]
                ]);
                    // Send email to the product user                                
                Mail::to($product->user->email)->send(new OrderMadeEmail($order));
                $ussdResponse = "END Order Completed.";
            }else {
                // Invalid input
                $ussdResponse = "END Invalid input. Please try again.";
            }

        }

        // Implement the logic to handle USSD input and generate the appropriate response
     
        return response($ussdResponse)->header('Content-Type', 'text/plain');
    
      
    } catch (ValidationException $e) {
        return response('Failed to process USSD request, Invlid fields')->header('Content-Type', 'text/plain');        
    } catch (\Exception $e) {
        Log::error($e->getMessage());
        return response('Failed to process USSD request')->header('Content-Type', 'text/plain');        
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
    
            // Send email to the product user
            $productUser = $order->product->user;
            Mail::to($productUser->email)->send(new OrderMadeEmail($order));
    
            return response()->json($order, 201);
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
