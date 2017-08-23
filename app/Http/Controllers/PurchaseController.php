<?php

namespace App\Http\Controllers;

use App\Http\Middleware\RedirectIfAuthenticated;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;

use App\Purchase;
use App\Game;
use App\User;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\DB;

class PurchaseController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    public function index(){

        if (!Auth::check()) {
            Session::flash("message-info", "Log in or create an account to track your purchases");
            return Redirect::route("login");
        }

        return view("purchases.index");
    }

    public function getPurchases(Request $request){
        $this->validate($request, [
            "user_id" => "required|numeric"
        ]);

        return ["success" => true, "data" => User::find($request->user_id)->purchases->load("games")];

    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return back();
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->validate($request, [
            "user_id" => "required|numeric",
            "store_id" => "required|numeric",
            "name" => "required|string",
            "price_paid" => "numeric",
            "price_paid" => "numeric",
            "date" => "nullable|date",
            "notes" => "nullable",
            "tier_purchased" => "nullable|numeric",
            "bundle_id" => "nullable|numeric",
        ]);

        $purchase = new Purchase($request->except(["_token"]));
        $purchase->save();

        return back();


    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $purchase = Purchase::find($id);
        if($purchase == null || !Auth::check() || $purchase->user_id != Auth::user()->id){
            Session::flash("message-danger", "Invalid purchase");
        }

        return view("purchases.purchase");
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
    public function updatePurchasePrice(Request $request)
    {
        $this->validate($request, [
            "purchase_id" => "required|numeric",
            "price_paid" => "required|numeric",
            "price_sold" => "required_without:price_paid|numeric",
        ]);

        if(isset($request->price_paid)){
            DB::update("update purchases set price_paid = ? where id = ? ", [$request->price_paid, $request->purchase_id]);
        }
        if(isset($request->price_sold)){
            DB::update("update purchases set price_sold = ? where id = ? ", [$request->price_sold, $request->purchase_id]);
        }

        return ["success" => true];
    }

    public function updatePurchaseItemPrice(Request $request)
    {
        $this->validate($request, [
            "game_purchase_id" => "required|numeric",
            "price_paid" => "required|numeric",
            "price_sold" => "required_without:price_paid|numeric",
        ]);

        if(isset($request->price_paid)){
            DB::update("update game_purchase set price_paid = ? where id = ? ", [$request->price_paid, $request->game_purchase_id]);
        }
        if(isset($request->price_sold)){
            DB::update("update game_purchase set price_sold = ? where id = ? ", [$request->price_sold, $request->game_purchase_id]);
        }

        return true;
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $purchase = Purchase::find($id);
        if($purchase == null){
            Session::flash("message-danger", "The purchase could not be located. Nothing was deleted.");
        }
        else if ($purchase->user->id != Auth::user()->id){
            Session::flash("message-danger", "This purchase doesn't belong to you.");
        }
        else{
            Session::flash("message-success", "The purchase was deleted");
            $purchase->delete();
        }

        return back();

    }


    public function insertGamesIntoPurchase(Request $request){
        $this->validate($request, array(
            'game_ids' => 'required|max:255',
            'purchase_id' => 'required|numeric',
            'availability' => 'required|numeric',
            'price_paid' => 'nullable|numeric',
            'price_sold' => 'nullable|numeric',
            'key' => 'nullable|string',
            'notes' => 'nullable|string',
        ));

        // Get the Purchase
        $purchase = Purchase::find($request->purchase_id);
        if($purchase == null){
            return ["success" => false, "code" => 0, "message" => "Invalid Purchase ID"];
        }

        // Verify that the id's are in an array
        if(!is_array($request->game_ids)){
            $request->game_ids = [$request->game_ids];
        }

        // Add each game
        $results = [
            ["added" => null],
            ["skipped" => null]
        ];

        foreach($request->game_ids as $game_id){
            $game = Game::find($game_id);
            // Verify the game was found
            if($game == null){
                $results["skipped"][] = $game_id;
            }
            else{
                $data = $request->except(["_token", "game_ids"]);
                $data["game_id"] = $game_id;

                $purchase->games()->save($game, $data);
                $results["added"][] = $game_id;
            }
        }

        return ["success" => true, "results" => $results];
    }

    public function massAction(Request $request){
        $this->validate($request, array(
            'action' => 'required|max:255',
            'ids' => 'required',
            'move_to' => 'numeric|required_if:action,move',
        ));

        if($request->action == "delete"){
            foreach($request->ids as $game){
                DB::delete("delete from game_purchase where id = ? ", [$game]);
            }
        }
        else if($request->action == "move"){

            foreach($request->ids as $game){
                DB::update("update game_purchase set purchase_id = ? where id = ? ", [$request->move_to, $game]);
            }
        }

        return ["sucess" => true];
    }

    public function updateAvailability(Request $request){
        $this->validate($request, array(
            'game_purchase_id' => 'required|numeric',
            'availability' => 'required|numeric',
        ));

        DB::table("game_purchase")->where("id",$request->game_purchase_id)->update(["availability" => $request->availability]);

        return ["success" => "true", "data" => $request];
    }
}
