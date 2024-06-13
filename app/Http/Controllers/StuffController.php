<?php

namespace App\Http\Controllers;

use App\Models\Stuff;
use Illuminate\Http\Request;
use App\Helpers\ApiFormatter;

class StuffController extends Controller
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

            //ambil data yang mau dit   ampilkan
            // $data = Stuff::all()->toArray();
            $stuff = Stuff::with('stuffStocks','inboundStuffs', 'lendings' )->get();

            return ApiFormatter::sendResponse(200, 'success', $stuff);
        } catch (\Exception $err) {
            return ApiFormatter::sendResponse(400, 'bad request', $err->getMessage());
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
        try {
            // validasi
            $this->validate($request, [
                'name' => 'required',
                'category' => 'required',
            ]);

            // proses ambil data
            $data = Stuff::create([
                'name' => $request->name,
                'category' => $request->category,
            ]);

            return ApiFormatter::sendResponse(200, 'success', $data);
        } catch (\Exception $err) {
            return ApiFormatter::sendResponse(400, 'bad request', $err->getMessage());
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Stuff  $stuff
     * @return \Illuminate\Http\Response
     */
    public function show($id)// untuk mencari data yang memiliki id mana yang akan di show 
    {
        try {
            $data = Stuff::where('id', $id)->first();
            if (is_null($data)) {
                return ApiFormatter::sendResponse(400, 'bad request', 'Data not found');
            } else {
                return ApiFormatter::sendResponse(200, 'succes', $data);
            }

        } catch (\Exception $err) {
            return ApiFormatter::sendResponse(400, 'bad request', $err->getMessage());
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Stuff  $stuff
     * @return \Illuminate\Http\Response
     */
    public function edit(Stuff $stuff)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Stuff  $stuff
     * @return \Illuminate\Http\Response
     */

    public function update(Request $request, $id)
    {
        try {
             // Validate the request
            $this->validate($request, [
                'name' => 'required',
                'category' => 'required',
            ]);
     
             // Find the stuff by id and update it
            $checkProses = Stuff::where('id', $id)->update([
                'name' => $request->name,
                'category' => $request->category,
            ]);
     
            if ($checkProses) {
                $data = Stuff::find($id);
                return ApiFormatter::sendResponse(200, 'success', $data);
            } else {
                return ApiFormatter::sendResponse(400, 'bad request', 'Gagal Mengubah Data!');
            }
        } catch (\Exception $err) {
            return ApiFormatter::sendResponse(400, 'bad request', $err->getMessage());
        }
    }
     
    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Stuff  $stuff
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        try {
            $stuff = Stuff::findOrFail($id);

            // Check if there are related models
            if ($stuff->inboundStuffs()->exists() || $stuff->stuffStocks()->exists() || $stuff->lendings()->exists()) {
            return ApiFormatter::sendResponse(400, 'bad request', 'Tidak Dapat Menghapus Data Stuff karena ada relasi terkait.');
        }

        // If no related models, proceed with deletion
        $stuff->delete();

        return ApiFormatter::sendResponse(200, 'success', 'Data Stuff Berhasil Dihapus!');
        } catch (\Exception $err) {
            return ApiFormatter::sendResponse(400, 'bad request', $err->getMessage());
        }
    }


    public function trash()
    {
        try {
            // onlyTrashed : mencari data yang deletes_at nya BUKAN null
            // onlyTrashed() berfungsi untuk mencari data yang telah dihapus oleh softdeletes / data yang deleted_at nya telah terisi. Setelah dicari, data diambil dengan get()
            $data = Stuff::onlyTrashed()->get();

            return ApiFormatter::sendResponse(200, 'success', $data);
        } catch (\Expection $err) {
            return ApiFormatter::sendResponse(400, 'bad request', $err->getMessage());
        }
    }

    public function restore($id)
    {
        try {
            $checkProses = Stuff::onlyTrashed()->where('id', $id)->restore();

            if ($checkProses) {
                $data = Stuff::find($id);
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
            $checkProses = Stuff::onlyTrashed()->where('id', $id)->forceDelete();

            return ApiFormatter::sendResponse(200, 'success', 'Data Berhasil Di Hapus Permanen!');
        } catch (\Expection $err) {
            return ApiFormatter::sendResponse(400, 'bad request', $err->getMessage());  
        }
    }
}
