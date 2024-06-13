<?php

namespace App\Http\Controllers;

use App\Helpers\ApiFormatter;
use Illuminate\Http\Request;
use App\models\Stuff;
use App\models\StuffStock;
use Illuminate\Support\Facades\Validator;

class StuffStockController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api');
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        try {
            $getStuffStock = StuffStock::with('stuff')->get();

            return ApiFormatter::sendResponse(200, true, 'Successfully Get All Stuff Stock Data', $getStuffStock);
        } catch (\Exception $err) {
            return ApiFormatter::sendResponse(400, false, $err->getMessage());
        }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
       
    }


    /**
     * Display the specified resource.
     *
     * @param  \App\Models\StuffStock  $stuffStock
     * @return \Illuminate\Http\Response
     */
    public function show(StuffStock $stuffStock)
    {
        try{
            $stock = StuffStock::with('stuff')->find($id);

            return response()->json([
                'success' => true, 
                'message' => 'Lihat semua stock barang dengan id ' . $id,
                'data' => $stock
            ], 200);
        } catch(\Exception $err){
            return response() -> json([
                'success' => false,
                'message' => 'dara dengan id' . $id .'tidak ditemukan'
            ], 400);
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\StuffStock  $stuffStock
     * @return \Illuminate\Http\Response
     */
    public function edit(StuffStock $stuffStock)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\StuffStock  $stuffStock
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, StuffStock $stuffStock)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\StuffStock  $stuffStock
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        try {
            $checkProses = StuffStock::where('id', $id)->delete();

            return ApiFormatter::sendResponse(200, 'success', 'Data Stuff Berhasil Dihapus!');
        } catch (\Expextion $err) {
            return ApiFormatter::sendResponse(400, 'bad request', $err->getMassage());
        }
    }

    public function addStock(Request $request, $id)
    {
        try {
            $getStuffStock = stuffStock::find($id);

            if(!$getStuffStock) {
                return ApiFormatter::sendResponse(404, false, 'Data Stuff Stock Not Found');
            } else {
                $this->validate($request, [
                    'total_available' => 'required',
                    'total_defec' => 'required',
                ]);

                $addStock = $getStuffStock->update([
                    'total_available' => $getStuffStock['total_available'] + $request->total_available,
                    'total_defec' => $getStuffStock['total_defec'] - $request->total_defec,
                ]);

                if($addStock) {
                    $getStockAdded = StuffStock::where('id', $id)->with('stuff')->first();

                    return ApiFormatter::sendResponse(200, true, 'Successfully Add A Stock Of Stuff Stock Data', $getStockAdded);
                }
            }
        } catch (\Exception $err) {
            return ApiFormatter::sendResponse(500, $err->getMessage());
        }
    }

    public function subStock(Request $request, $id) 
    {
        try {
            $getStuffStock = StuffStock::find($id);

            if(!$getStuffStock) {
                return ApiFormatter::sendResponse(404, false, 'Data Stuff Stock Not Found');
            } else {
                $this->validate($request, [
                    'total_available' => 'required',
                    'total_defec' => 'required',
                ]);

                $isStockAvailable = $getStuffStock['total_available'] - $request->total_available;
                $isStockDefec = $getStuffStock['total_defec'] - $request->total_defec;

                if ($isStockAvailable < 0 || $isStockDefec < 0) {
                    return ApiFormatter::sendResponse(400, true, 'A Substraction Stock Cant Less Than A Stock Stored');
                } else {
                    $subStock = $getStuffStock->update([
                        'total_available' => $isStockAvailable,
                        'total_defec' => $isStockDefec,
                    ]);

                    if($subStock) {
                        $getStockSub = StuffStock::where('id', $id)->with('stuff')->first();

                        return ApiFormatter::sendResponse(200, true, 'Successfully Sub A Stock Of Stuff Stock Data', $getStockSub);
                    }
                }
            }
        } catch (\Exception $err) {
            return ApiFormatter::sendResponse(500, $err->getMessage());
        }
    }

    public function trash()
    {
        try {
            $data =StuffStock::onlyTrashed()->get();

            return ApiFormatter::sendResponse(200, 'success', $data);
        } catch (\Expection $err) {
            return ApiFormatter::sendResponse(400, 'bad request', $err->getMessage());
        }
    }

    public function restore($id)
    {
        try {
            $checkProses = StuffStock::onlyTrashed()->where('id', $id)->restore();

            if ($checkProses) {
                $data = Stuffstock::find($id);
                return ApiFormatter::sendResponse(200, 'success', $data);
            } else {
                return ApiFormatter::sendResponse(400, 'bad request', 'Gagal Mengembalikan Data!');
            }
        } catch (\Expection $err) {
            return ApiFormatter::sendResponse(400, 'bad request', $err->getMessage());
        }
    }

    public function forceDestroy($id)
    {
        try {
            $checkProses = StuffStock::onlyTrashed()->where('id', $id)->forceDelete();

            return ApiFormatter::sendResponse(200, 'success', 'Data Berhasil Di Hapus Permanen!');
        } catch (\Expection $err) {
            return ApiFormatter::sendResponse(400, 'bad request', $err->getMessage());  
        }
    }
}
