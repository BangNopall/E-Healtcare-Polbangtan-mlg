<?php

namespace App\Http\Controllers;

use App\Models\ConsumableLog;
use Illuminate\Support\Str;
use App\Models\InventoryConsumable;
use App\Models\KategoriConsumable;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Validator;

class InventarisConsumableController extends Controller
{
    public function index()
    {
        $data = InventoryConsumable::without('User')->orderBy('updated_at', 'desc')->paginate(20);
        $kategori = KategoriConsumable::select('id', 'nama_kategori')->get()->toArray();
        return view('inventaris.alat-noused', compact('data', 'kategori'));
    }

    public function show($uuid)
    {
        $data = InventoryConsumable::where('id', $uuid)->first();
        $inventoryLogs = ConsumableLog::where('alat_id', $uuid)->orderBy('created_at', 'desc')->paginate(20);
        $kategoriAlat = KategoriConsumable::select('id', 'nama_kategori')->get()->toArray();
        return view('inventaris.partials.alat-noused.detail', compact('data', 'inventoryLogs', 'kategoriAlat'));
    }

    public function destroy($uuid)
    {

        // dd($uuid);
        try {
            DB::beginTransaction();

            $inventory = InventoryConsumable::where('id', $uuid)->first();

            ConsumableLog::create([
                'alat_id' => $uuid,
                'user_id' => auth()->id(),
                'Qty' => 0,
                'description' => 'Alat ' . $inventory->nama_alat . ' berhasil dihapus dari inventory beserta log nya oleh ' . auth()->user()->name . '.',
                'date' => date('Y-m-d'),
                'time' => date('H:i:s'),
                'type' => 'Deleted',
            ]);
            $inventoryLogs = ConsumableLog::where('alat_id', $uuid)->get();
            $inventoryLogs->each(function ($log) {
                $log->delete(); // Menggunakan soft delete
            });

            if ($inventory->foto_consumable) {
                Storage::disk('public')->delete('foto_consumable/' . $inventory->foto_consumable);
            }

            $inventory->delete();

            DB::commit();

            return redirect()->back()->with('success', 'Data berhasil dihapus');
        } catch (\Exception $th) {
            DB::rollBack();

            if ($th instanceof ModelNotFoundException) {
                return back()->with('error', 'Alat dengan ID ' . $uuid . ' tidak ditemukan');
            } else {
                // Logging kesalahan ke file log atau sistem monitoring
                Log::error('Gagal menghapus alat: ' . $th->getMessage());
                return back()->with('error', 'Gagal menghapus alat');
            }
        }
    }

    public function storeConsumable(Request $request)
    {
        try {
            $request->validate([
                'nama_alat' => 'required',
                'kategori_id' => 'required|exists:kategori_alats,id',
            ]);

            if (InventoryConsumable::where('nama_alat', $request->nama_alat)->exists()) {
                $dataAlat = InventoryConsumable::where('nama_alat', $request->nama_alat)->get();
                $KategoriID = KategoriConsumable::where('id', $request->kategori_id)->first();
                foreach ($dataAlat as $key => $value) {
                    if ($value->kategori_id == $request->kategori_id) {
                        return back()->with('error', 'Alat ' . $request->nama_alat . ' sudah ada di kategori ' . $KategoriID->nama_kategori);
                    } else {
                        continue;
                    }

                    if ($value->createdBy) {
                        return back()->with('error', 'Alat ' . $request->nama_alat . ' sudah ada di kategori ' . $KategoriID->nama_kategori . ' dan dibuat oleh ' . $value->createdBy);
                    } else {
                        continue;
                    }
                }
            }

            // Mulai transaksi database
            DB::beginTransaction();

            $alat = InventoryConsumable::create([
                'nama_alat' => $request->nama_alat,
                'kategori_id' => $request->kategori_id,
                'kode_alat' => InventoryConsumable::factory()->uniqueKodeConsumable($request->nama_alat),
                'createdBy' => auth()->id(),
            ]);

            ConsumableLog::create([
                'alat_id' => $alat->id,
                'user_id' => auth()->id(),
                'Qty' => 0,
                'description' => 'Alat ' . $request->nama_alat . ' berhasil ditambahkan ke dalam sistem Oleh ' . auth()->user()->name . '.',
                'date' => date('Y-m-d'),
                'time' => date('H:i:s'),
                'type' => 'Created',
            ]);

            DB::commit();

            return back()->with('success', 'Data berhasil disimpan');
        } catch (\Exception $th) {
            // Rollback transaksi jika terjadi kesalahan
            DB::rollback();

            if ($th instanceof ValidationException) {
                return back()->withErrors($th->errors())->withInput()->with('error', 'Gagal menambahkan alat: ' . $th->getMessage());
            } else {
                // Logging kesalahan ke file log atau sistem monitoring
                Log::error('Gagal menambahkan alat: ' . $th->getMessage());
                // return back()->with('error', 'Gagal menambahkan alat');
                return back()->with('error', $th->getMessage());
            }
        }
    }

    public function filterConsumable(Request $request)
    {
        try {
            $input = $request->all();

            if ($input['kategori_alat'] === 'Pilih') {
                $input['kategori_alat'] = null;
            }

            $validatedData = Validator::make($input, [
                'nama_alat' => 'nullable|string',
                'itemID' => 'nullable|string',
                'kategori_alat' => 'nullable|exists:kategori_alats,id',
            ])->validate();

            $upperName = strtoupper($validatedData['nama_alat'] ?? '');
            $upperItemID = strtoupper($validatedData['itemID'] ?? '');

            $query = InventoryConsumable::query();

            // Filter berdasarkan nama alat jika disediakan
            if (!empty($upperName)) {
                $query->whereRaw('UPPER(nama_alat) LIKE ?', ['%' . $upperName . '%']);
            }

            // Filter berdasarkan kode alat jika disediakan
            if (!empty($upperItemID)) {
                $query->where('kode_alat', 'like', '%' . $upperItemID . '%');
            }

            // Filter berdasarkan kategori alat jika disediakan
            if (!empty($validatedData['kategori_alat'])) {
                $query->where('kategori_id', $validatedData['kategori_alat']);
            }

            $data = $query->orderBy('updated_at', 'desc')->paginate(20);

            $table = view('components.inventaris.table-consumable', compact('data'))->render();

            return response()->json(['table' => $table]);
        } catch (\Exception $th) {
            if ($th instanceof ValidationException) {
                return back()->withErrors($th->errors())->withInput()->with('error', 'Gagal memfilter Consumable: ' . $th->getMessage());
            } else {
                // Logging kesalahan ke file log atau sistem monitoring
                Log::error('Gagal memfilter Consumable: ' . $th->getMessage());
                return back()->with('error', 'Gagal memfilter Consumable');
            }
        }
    }

    public function updateConsumable(Request $request, $id)
    {
        try {
            // Validasi input
            $request->validate([
                'nama_kategori' => 'required|exists:kategori_consumables,nama_kategori',
            ]);

            // Mulai transaksi database
            DB::beginTransaction();

            // get satuan obat id
            $kategoriID = KategoriConsumable::where('nama_kategori', $request->nama_kategori)->firstOrFail()->id;

            // Periksa apakah obat dengan id yang diberikan ada
            $kategori = InventoryConsumable::where('id', $id)->firstOrFail();
            // Update kategori
            $kategori->kategori_id = $kategoriID;
            $kategori->save();

            // Commit transaksi
            DB::commit();

            return back()->with('success', 'Berhasil mengubah obat');
        } catch (\Throwable $th) {
            // Rollback transaksi jika terjadi kesalahan
            DB::rollback();

            // Tangani kesalahan dengan lebih baik
            if ($th instanceof ModelNotFoundException) {
                return back()->with('error', 'Alat dengan ID ' . $id . ' tidak ditemukan');
            } elseif ($th instanceof ValidationException) {
                return back()->withErrors($th->errors())->withInput()->with('error', 'Gagal mengubah Alat: ' . $th->getMessage());
            } else {
                // Logging kesalahan ke file log atau sistem monitoring
                Log::error('Gagal mengubah Alat: ' . $th->getMessage());
                return back()->with('error', 'Gagal mengubah Alat');
            }
        }
    }

    public function depositConsumable(Request $request, $uuid)
    {
        try {
            // validate request
            $request->validate([
                'user' => 'required|exists:users,name',
                'user_id' => 'required|exists:users,id',
                'Qty' => 'required|numeric|min:1',
                'date' => 'required',
                'time' => 'required',
            ]);

            // store data
            DB::beginTransaction();

            $inventory = InventoryConsumable::where('id', $uuid)->first();
            $inventory->stok += $request->Qty;
            $inventory->save();

            $date = date('Y-m-d', strtotime($request->date));
            $time = date('H:i:s', strtotime($request->time));

            ConsumableLog::create([
                'alat_id' => $uuid,
                'user_id' => $request->user_id,
                'Qty' => $request->Qty,
                'date' => $date,
                'time' => $time,
                'type' => 'Deposit',
                'description' => 'Alat ' . $inventory->nama_alat . ' berhasil deposit ke dalam sistem Oleh ' . $request->user . '.',
            ]);

            DB::commit();

            return redirect()->back()->with('success', 'Data berhasil disimpan');
        } catch (\Exception $th) {
            DB::rollBack();

            if ($th instanceof ModelNotFoundException) {
                return back()->with('error', 'Alat dengan ID ' . $uuid . ' tidak ditemukan');
            } elseif ($th instanceof ValidationException) {
                return back()->withErrors($th->errors())->withInput()->with('error', 'Gagal menambahkan stok obat: ' . $th->getMessage());
            } else {
                // Logging kesalahan ke file log atau sistem monitoring
                Log::error('Gagal menambahkan stok Alat: ' . $th->getMessage());
                return back()->with('error', 'Gagal menambahkan stok Alat');
            }
        }
    }

    public function withdrawConsumable(Request $request, $uuid)
    {
        try {
            $request->validate([
                'user' => 'required|exists:users,name',
                'user_id' => 'required|exists:users,id',
                'Qty' => 'required|numeric|min:1',
                'date' => 'required',
                'time' => 'required',
                'description' => 'nullable|string',
            ]);

            DB::beginTransaction();

            $alat = InventoryConsumable::where('id', $uuid)->first();

            $alat->stok -= $request->Qty;
            $alat->save();

            if ($request->description) {
                ConsumableLog::create([
                    'alat_id' => $uuid,
                    'user_id' => $request->user_id,
                    'Qty' => $request->Qty,
                    'description' => $request->description,
                    'date' => date('Y-m-d', strtotime($request->date)),
                    'time' => date('H:i:s', strtotime($request->time)),
                    'type' => 'Withdraw',
                ]);
            } else {
                ConsumableLog::create([
                    'alat_id' => $uuid,
                    'user_id' => $request->user_id,
                    'Qty' => $request->Qty,
                    'date' => date('Y-m-d', strtotime($request->date)),
                    'time' => date('H:i:s', strtotime($request->time)),
                    'type' => 'Withdraw',
                    'description' => 'Alat ' . $alat->nama_alat . ' berhasil diambil oleh ' . $request->user . ' sebanyak ' . $request->Qty . ' buah.'
                ]);
            }

            DB::commit();

            return redirect()->back()->with('success', 'Data berhasil disimpan');
        } catch (\Exception $th) {
            DB::rollBack();

            if ($th instanceof ModelNotFoundException) {
                return back()->with('error', 'Alat dengan ID ' . $uuid . ' tidak ditemukan');
            } elseif ($th instanceof ValidationException) {
                return back()->withErrors($th->errors())->withInput()->with('error', 'Gagal menambahkan stok obat: ' . $th->getMessage());
            } else {
                // Logging kesalahan ke file log atau sistem monitoring
                Log::error('Gagal menambahkan stok Alat: ' . $th->getMessage());
                return back()->with('error', 'Gagal menambahkan stok Alat');
            }
        }
    }

    public function updateFotoConsumable(Request $request, $uuid)
    {
        try {
            $request->validate([
                'foto_consumable' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2000', // max 2MB
            ]);

            DB::beginTransaction();
            $alat = InventoryConsumable::where('id', $uuid)->first();

            if ($alat->foto_consumable) {
                Storage::disk('public')->delete('foto_consumable/' . $alat->foto_consumable);
            }

            $file = $request->file('foto_consumable');
            $foto_consumable_name = Str::random(24) . '.' . $file->extension();
            $file->storeAs('foto_consumable', $foto_consumable_name, 'public');

            $alat->foto_consumable = $foto_consumable_name;
            $alat->save();

            DB::commit();

            return redirect()->back()->with('success', 'Foto alat berhasil diubah');
        } catch (\Exception $th) {
            DB::rollBack();
            if ($th instanceof ValidationException) {
                return back()->withErrors($th->errors())->withInput()->with('error', 'Gagal mengubah foto alat: ' . $th->getMessage());
            } else {
                // Logging kesalahan ke file log atau sistem monitoring
                Log::error('Gagal mengubah foto alat: ' . $th->getMessage());
                return back()->with('error', 'Gagal mengubah foto alat');
            }
        }
    }

    public function storeKategoriTersisa(Request $request)
    {
        try {
            $request->validate([
                'nama_kategori' => 'required|string|unique:kategori_alats,nama_kategori',
            ]);

            DB::beginTransaction();

            $KategoriConsumable = KategoriConsumable::create([
                'nama_kategori' => $request->nama_kategori,
            ]);

            DB::commit();

            return back()->with('success', 'Berhasil menambahkan kategori alat');
        } catch (\Throwable $th) {
            DB::rollback();

            if ($th instanceof ValidationException) {
                return back()->withErrors($th->errors())->withInput()->with('error', 'Gagal menambahkan kategori alat: ' . $th->getMessage());
            } else {
                Log::error('Gagal menambahkan kategori alat: ' . $th->getMessage());
                return back()->with('error', 'Gagal menambahkan kategori alat');
            }
        }
    }

    public function deleteKategoriTersisa($id)
    {
        try {
            // Mulai transaksi database
            DB::beginTransaction();

            $getInventory = InventoryConsumable::where('kategori_id', $id)->get();

            if ($getInventory->count() > 0) {
                foreach ($getInventory as $key => $value) {
                    $value->update([
                        'kategori_id' => null,
                    ]);
                }
            }

            // Hapus kategori alat tersisa
            $kategori = KategoriConsumable::where('id', $id)->firstOrFail();
            $kategori->delete();

            // Commit transaksi
            DB::commit();

            return back()->with('success', 'Berhasil menghapus kategori alat');
        } catch (\Throwable $th) {
            // Rollback transaksi jika terjadi kesalahan
            DB::rollback();

            // Tangani kesalahan dengan lebih baik
            if ($th instanceof ModelNotFoundException) {
                return back()->with('error', 'Kategori alat dengan ID ' . $id . ' tidak ditemukan');
            } else {
                // Logging kesalahan ke file log atau sistem monitoring
                Log::error('Gagal menghapus kategori alat: ' . $th->getMessage());
                return back()->with('error', 'Gagal menghapus kategori alat');
            }
        }
    }

    public function updateKategoriTersisa(Request $request, $id)
    {
        try {
            // Validasi input
            $request->validate([
                'nama_kategori_tersisa' => 'required|string',
            ]);

            // Mulai transaksi database
            DB::beginTransaction();

            // Periksa apakah satuan alat dengan id yang diberikan ada
            $kategori = KategoriConsumable::where('id', $id)->firstOrFail();

            // Update nama satuan alat
            $kategori->nama_kategori = $request->nama_kategori_tersisa;
            $kategori->save();

            // Commit transaksi
            DB::commit();

            return back()->with('success', 'Berhasil mengubah kategori alat tersisa');
        } catch (\Throwable $th) {
            // Rollback transaksi jika terjadi kesalahan
            DB::rollback();

            // Tangani kesalahan dengan lebih baik
            if ($th instanceof ModelNotFoundException) {
                return back()->with('error', 'Kategori alat tersisa dengan ID ' . $id . ' tidak ditemukan');
            } elseif ($th instanceof ValidationException) {
                return back()->withErrors($th->errors())->withInput()->with('error', 'Gagal mengubah kategori alat tersisa: ' . $th->getMessage());
            } else {
                // Logging kesalahan ke file log atau sistem monitoring
                Log::error('Gagal mengubah kategori alat tersisa: ' . $th->getMessage());
                return back()->with('error', 'Gagal mengubah kategori alat tersisa');
            }
        }
    }
}
