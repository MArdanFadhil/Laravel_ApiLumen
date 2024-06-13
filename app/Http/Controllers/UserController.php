<?php

namespace App\Http\Controllers;

use App\Helpers\ApiFormatter;
use App\Models\User; 
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;


class UserController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api', ['except' => ['login', 'logout']]);
    }
    public function index()
    {
        try {
            // ambil data yang mau ditampilkan
            $users = User::all()->toArray();

            return ApiFormatter::sendResponse(200, 'success', $users);
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
                'username' => 'required',
                'email' => 'required|email|min:3',
                'password' => 'required',
                'role' => 'required|in:admin,staff'
            ]);

            // proses tambah data
            // NamaModel::create(['column' => $request->name_or_key, ])
            $data = User::create([
                'username' => $request->username,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'role' => $request->role,
            ]);
            
            return ApiFormatter::sendResponse(200, 'success', $data);
        } catch (\Exception $err){
            return ApiFormatter::sendResponse(400, 'bad request', $err->getMessage());
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        try {
            $data = User::where('id', $id)->first();

            if (is_null($data)) {
                return ApiFormatter::sendResponse(400, 'bad request', 'Data not found!');
            } else {
                return ApiFormatter::sendResponse(200, 'success', $data);
            } 
        } catch (\Exception $err) {
            return ApiFormatter::senResponse(400, 'bad request', $err->getMessage());
        }
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
        try {
            $this->validate($request, [
                'username' => 'required',
                'email' => 'required|email|min:3',
                'role' => 'required|in:admin, staff'
            ]);

            if ($request->password) {
                    $checkProses = User::where('id', $id)->update([
                    'username' => $request->username,
                    'email' => $request->email,
                    'role' => $request->role,
                    'password' => Hash::make($request->password),
                ]);
            } else {
                $checkProses = User::where('id', $id)->update([
                    'username' => $request->username,
                    'email' => $request->email,
                    'role' => $request->role,
                ]);
            }

            if ($checkProses) {
                $data = User::find($id);
                return ApiFormatter::sendResponse(200, 'success', $data);
            } else {
                return ApiFormatter::sendResponse(400, 'bad request', 'Gagal mengubah data!');
            }
        } catch (\Exception $err) {
            return ApiFormatter::sendResponse(400, 'bad request', $err->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        try {
            $checkProses = User::where('id', $id)->delete();

            return ApiFormatter::sendResponse(200, 'success', 'Data stuff berhasil dihapus!');
        } catch (\Exception $err) {
            return ApiFormatter::sendResponse(400, 'bad request', $err->getMessage());
        }
    }

    public function trash()
    {
        try {
            // onlyTrashed: mencari data yang deletes_at nya BUKAN null
            $data = User::onlyTrashed()->get();

            return ApiFormatter::sendResponse(200, 'success', $data);
        } catch (\Exception $err) {
            return ApiFormatter::sendResponse(400, 'bad request', $err->getMessage());
        }
    }

    public function restore($id)
    {
        try {
            $checkProses = User::onlyTrashed()->where('id', $id)->restore();

            if ($checkProses) {
                $data = User::find(id);
                return ApiFormatter::sendResponse(200, 'success', $data);
            } else {
                return ApiFormatter::sendResponse(400, 'bad request', 'Gagal mengembalikan data!');
            }
        } catch (\Exception $err) {
            return ApiFormatter::sendResponse(400, 'bad request', $err->getMessage());
        }
    }

    public function forceDestroy($id) 
    {
        try {
            $checkProses = User::onlyTrashed()->where('id', $id)->forceDelete();

            return ApiFormatter::sendResponse(200, 'success', 'Berhasil menghapus permanen data stuff!');
        } catch (\Exception $err) {
            return ApiFormatter::sendResponse(400, 'bad request', $err->getMessage());
        }
    }

    public function login(Request $request)
    {
        try {
            $this->validate($request, [
                'email' => 'required',
                'password' => 'required',
            ]);

            $user = User::where('email', $request->email)->first(); // Mencari dan mendapatkan data user berdasarkan email yang digunakan

            if (!$user) {
                // Jika email tidak terdaftar maka akan dikembalikan response error
                return ApiFormatter::sendResponse(400, false, 'Login Failed! User Doesnt Exists');
            } else {
                // Jika email terdaftar, selanjutnya pencocokan password yang diinput dengan password di db dengan menggunakan Hash::check()
                $isValid = Hash::check($request->password, $user->password);

                if (!$isValid) {
                    // Jika password tidak cocok maka akan mendapatkan pesan error
                    return ApiFormatter::sendResponse(400, false, 'Login Failed! Password Doesnt Match');
                } else {
                    // Jika password sesuai selanjutnya akan membuat token
                    // bin2hex digunakan untuk dapat mengonversi string karakter ASCII menjadi nilai heksadesimal
                    // random_bytes menghasilkan byte pseudo-acak yang aman secara kriptografis dengan panjang 40 karakter
                    $generateToken = bin2hex(random_bytes(40));
                    //Token inilah nantinya yang digunakan pada proses authentication user yang login

                    $user->update([
                        'token' => $generateToken
                        // update kolom token dengan value hasil dari generateToken di row user yang ingin login
                    ]);

                    return ApiFormatter::sendResponse(200, 'Login Successfully', $user);
                }
            }
        } catch (\Exception $err) {
            return ApiFormatter::sendResponse(400, false, $err->getMessage());
        }
    }

    public function logout(Request $request) {
        try {
            $this->validate($request, [
                'email' => 'required',
            ]);

            $user = User::where('email', $request->email)->first();

            if (!$user) {
                return ApiFormatter::sendResponse(400, 'Logout Failed! User Doesnt Exists');
            } else {
                if (!$user->token){
                    return ApiFormatter::sendResponse(400, 'Logout Failed! User Doesnt Logout Science');
                } else {
                    $logout = $user->update(['token' => null]);

                    if ($logout) {
                        return ApiFormatter::sendResponse(200, 'Logout Successfully!');
                    }
                }
            }
        } catch (\Exception $err) {
            return ApiFormatter::sendResponse(400, false, $err->getMessage());
        }
    }
}