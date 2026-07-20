<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\User;
use App\Models\ObatLog;
use App\Models\SatuanObat;
use Illuminate\Support\Str;
use App\Models\InventoryLog;
use App\Models\KategoriAlat;
use Illuminate\Http\Request;
use App\Models\InventoryObat;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\KategoriConsumable;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use App\Models\InventoryAlat;
use App\Models\InventoryConsumable;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class InventarisObatController extends Controller
{
    public function dashboard()
    {
        return view('inventaris.dashboard');
    }
    public function index()
    {
        $data = InventoryObat::without('User')->orderBy('updated_at', 'desc')->paginate(20);
        $satuanObat = SatuanObat::select('id', 'nama_satuan')->get()->toArray();
        // dd($satuanObat);
        return view('inventaris.obat', compact('data', 'satuanObat'));
    }

    public function show($uuid)
    {
        $data = InventoryObat::where('id', $uuid)->first();
        $MaxPaginate = 10;
        $totalinventoryLogs = InventoryLog::where('obat_id', $uuid)->orderBy('created_at', 'desc')->count();
        $totalobatLogs = ObatLog::where('obat_id', $uuid)->orderBy('created_at', 'desc')->count();
        $paginationControl = ($totalinventoryLogs > $totalobatLogs) ? 'inventoryLogs' : 'obatLogs';
        $inventoryLogs = InventoryLog::where('obat_id', $uuid)->orderBy('created_at', 'desc')->paginate($MaxPaginate);
        $obatLogs = $obatLogs = ObatLog::where('obat_id', $uuid)->orderBy('created_at', 'desc')->paginate($MaxPaginate);
        $kategoriObat = SatuanObat::select('id', 'nama_satuan')->get()->toArray();
        return view('inventaris.partials.detail', compact('data', 'inventoryLogs', 'obatLogs', 'paginationControl', 'kategoriObat'));
    }

    public function deposit(Request $request, $uuid)
    {
        try {
            // Validasi input
            $request->validate([
                'Qty' => 'required|numeric|min:1',
                'production_date' => 'required|date',
                'expired_date' => 'required|date|after:production_date',
            ]);

            // Mulai transaksi database
            DB::beginTransaction();

            // Periksa apakah obat dengan id yang diberikan ada
            $obat = InventoryObat::where('id', $uuid)->firstOrFail();

            // Tambahkan stok obat
            $obat->stok += $request->Qty;
            $obat->save();

            // Catat log transaksi
            InventoryLog::create([
                'obat_id' => $uuid,
                'type' => 'deposit',
                'Qty' => $request->Qty,
                'production_date' => $request->production_date,
                'expired_date' => $request->expired_date,
                'description' => 'Deposit From ' . auth()->user()->name,
                'user_id' => auth()->id(),
            ]);

            // Commit transaksi
            DB::commit();

            return back()->with('success', 'Berhasil menambahkan stok obat');
        } catch (\Throwable $th) {
            // Rollback transaksi jika terjadi kesalahan
            DB::rollback();

            // Tangani kesalahan dengan lebih baik
            if ($th instanceof ModelNotFoundException) {
                return back()->with('error', 'Obat dengan ID ' . $uuid . ' tidak ditemukan');
            } elseif ($th instanceof ValidationException) {
                return back()->withErrors($th->errors())->withInput()->with('error', 'Gagal menambahkan stok obat: ' . $th->getMessage());
            } else {
                // Logging kesalahan ke file log atau sistem monitoring
                Log::error('Gagal menambahkan stok obat: ' . $th->getMessage());
                return back()->with('error', 'Gagal menambahkan stok obat');
            }
        }
    }

    public function logPengguna($ulid)
    {
        $data = ObatLog::where('id', $ulid)->first();
        return $data->toJson();
        // return view('inventaris.partials.log-pengguna', compact('data'));
    }

    public function store(Request $request)
    {
        try {
            if (!$request->filled('satuanID')) {
                return back()->withInput()->with('error', 'Pilih satuan obat terlebih dahulu');
            }
            // Validasi input
            $request->validate([
                'nama_obat' => 'required|string',
                'satuanID' => 'required|exists:satuan_obats,id|not_in:notSelected',
            ]);

            if (InventoryObat::where('nama_obat', $request->nama_obat)->exists()) {
                $dataObat = InventoryObat::where('nama_obat', $request->nama_obat)->get();
                $nameSatuan = SatuanObat::find($request->satuanID)->nama_satuan;
                foreach ($dataObat as $obat) {
                    if ($obat->satuan_id == $request->satuanID) {
                        return back()->withInput()->with('error', 'Obat dengan nama ' . $request->nama_obat . ' dan satuan ' . $nameSatuan . ' sudah dibuat sebelumnya.');
                    } else {
                        continue;
                    }

                    if ($obat->createdBy) {
                        return back()->withInput()->with('error', 'Obat dengan nama ' . $request->nama_obat . ' dan satuan ' . $nameSatuan . ' sudah dibuat sebelumnya oleh ' . $obat->User->name);
                    } else {
                        continue;
                    }
                }
            }

            // Mulai transaksi database
            DB::beginTransaction();

            // Tambahkan obat baru
            $obat = InventoryObat::create([
                'nama_obat' => $request->nama_obat,
                'kode_obat' => InventoryObat::factory()->uniqueKodeObat($request->nama_obat),
                'satuan_id' => $request->satuanID,
                'createdBy' => auth()->id(),
            ]);

            // Catat log transaksi
            InventoryLog::create([
                'obat_id' => $obat->id,
                'type' => 'created',
                'description' => 'Create From ' . auth()->user()->name,
                'user_id' => auth()->id(),
            ]);

            // Commit transaksi
            DB::commit();

            return back()->with('success', 'Berhasil menambahkan obat');
        } catch (\Throwable $th) {
            // Rollback transaksi jika terjadi kesalahan
            DB::rollback();

            // Tangani kesalahan dengan lebih baik
            if ($th instanceof ValidationException) {
                return back()->withErrors($th->errors())->withInput()->with('error', 'Gagal menambahkan obat ' . $request->nama_obat . ' lengkapi data terlebih dahulu');
            } else {
                // Logging kesalahan ke file log atau sistem monitoring
                Log::error('Gagal menambahkan stok obat: ' . $th->getMessage());
                return back()->with('error', 'Gagal menambahkan obat');
            }
        }
    }

    public function filter(Request $request)
    {
        try {
            $input = $request->all();

            if ($input['satuan_obat'] === 'Pilih') {
                $input['satuan_obat'] = null;
            }

            $validatedData = Validator::make($input, [
                'nama_obat' => 'nullable|string',
                'itemID' => 'nullable|string',
                'satuan_obat' => 'nullable|exists:satuan_obats,id',
            ])->validate();

            $upperName = strtoupper($validatedData['nama_obat'] ?? '');
            $upperItemID = strtoupper($validatedData['itemID'] ?? '');

            $query = InventoryObat::query();

            // filter berdasarkan nama obat jika disediakan
            if (!empty($upperName)) {
                $query->whereRaw('UPPER(nama_obat) LIKE ?', ['%' . $upperName . '%']);
            }

            // filter berdasarkan kode obat jika disediakan
            if (!empty($upperItemID)) {
                $query->where('kode_obat', 'like', '%' . $upperItemID . '%');
            }

            // filter berdasarkan satuan obat jika disediakan
            if (!empty($validatedData['satuan_obat'])) {
                $query->where('satuan_id', $validatedData['satuan_obat']);
            }

            // // Logging for debugging
            // Log::info('Filter parameters:', $validatedData);
            // Log::info('Query SQL:', [$query->toSql(), $query->getBindings()]);

            $data = $query->orderBy('updated_at', 'desc')->paginate(20);

            $table = view('components.inventaris.table-obat', compact('data'))->render();

            return response()->json(['table' => $table]);
        } catch (\Throwable $th) {
            // Tangani kesalahan dengan lebih baik
            if ($th instanceof ValidationException) {
                return response()->json(['error' => $th->errors()]);
            } else {
                // Logging kesalahan ke file log atau sistem monitoring
                Log::error('Gagal memfilter obat: ' . $th->getMessage());
                return response()->json(['error' => 'Gagal memfilter obat']);
            }
        }
    }


    public function destroy($uuid)
    {
        // implementasi soft delete NOK
        try {
            // Mulai transaksi database
            DB::beginTransaction();

            // Periksa apakah obat dengan id yang diberikan ada
            $obat = InventoryObat::where('id', $uuid)->firstOrFail();

            // Hapus entitas terkait dari tabel inventory_logs
            $inventoryLogs = InventoryLog::where('obat_id', $uuid)->get();
            $inventoryLogs->each->delete();

            // Hapus entitas terkait dari tabel obat_logs
            $obatLogs = ObatLog::where('obat_id', $uuid)->get();
            $obatLogs->each->delete();

            if ($obat->foto_obat) {
                Storage::disk('public')->delete('foto_obat/' . $obat->foto_obat);
            }
            // Hapus obat
            $obat->delete();

            // Commit transaksi
            DB::commit();

            return back()->with('success', 'Berhasil menghapus obat');
        } catch (\Throwable $th) {
            // Rollback transaksi jika terjadi kesalahan
            DB::rollback();

            // Tangani kesalahan dengan lebih baik
            if ($th instanceof ModelNotFoundException) {
                return back()->with('error', 'Obat dengan ID ' . $uuid . ' tidak ditemukan');
            } else {
                // Logging kesalahan ke file log atau sistem monitoring
                Log::error('Gagal menghapus obat: ' . $th->getMessage());
                return back()->with('error', 'Gagal menghapus obat');
            }
        }
    }

    public function withdraw(Request $request, $uuid)
    {
        try {
            // Validasi input
            $request->validate([
                'Qty' => 'required|numeric|min:1',
                'type' => 'required|in:withdraw,expired',
            ]);

            // Mulai transaksi database
            DB::beginTransaction();

            // Periksa apakah obat dengan id yang diberikan ada
            $obat = InventoryObat::where('id', $uuid)->firstOrFail();

            // if ($obat->stok < $request->Qty) {
            //     throw ValidationException::withMessages(['Qty' => 'Stok obat tidak mencukupi']);
            // }

            // Kurangi stok obat
            $obat->stok -= $request->Qty;
            $obat->save();

            // Catat log transaksi
            if ($request->description == null) {
                InventoryLog::create([
                    'obat_id' => $uuid,
                    'type' => $request->type,
                    'Qty' => $request->Qty,
                    'description' => 'Withdraw From ' . auth()->user()->name,
                    'user_id' => auth()->id(),
                ]);
            } else {
                InventoryLog::create([
                    'obat_id' => $uuid,
                    'type' => $request->type,
                    'Qty' => $request->Qty,
                    'description' => $request->description,
                    'user_id' => auth()->id(),
                ]);
            }

            // Commit transaksi
            DB::commit();

            return back()->with('success', 'Berhasil mengurangi stok obat');
        } catch (\Throwable $th) {
            // Rollback transaksi jika terjadi kesalahan
            DB::rollback();

            // Tangani kesalahan dengan lebih baik
            if ($th instanceof ModelNotFoundException) {
                return back()->with('error', 'Obat dengan ID ' . $uuid . ' tidak ditemukan');
            } elseif ($th instanceof ValidationException) {
                return back()->withErrors($th->errors())->withInput()->with('error', 'Gagal mengurangi stok obat: ' . $th->getMessage());
            } else {
                // Logging kesalahan ke file log atau sistem monitoring
                Log::error('Gagal mengurangi stok obat: ' . $th->getMessage());
                return back()->with('error', 'Gagal mengurangi stok obat');
            }
        }
    }

    public function updateFotoObat(Request $request, $uuid)
    {
        try {
            // dd($request->all());
            // Validasi input
            $request->validate([
                'foto_obat' => 'required|image|mimes:jpeg,png,jpg,gif|max:2000', // max 2MB
            ]);

            // Mulai transaksi database
            DB::beginTransaction();

            // Periksa apakah obat dengan id yang diberikan ada
            $obat = InventoryObat::where('id', $uuid)->firstOrFail();

            // Hapus foto obat lama jika ada
            if ($obat->foto_obat) {
                Storage::disk('public')->delete('foto_obat/' . $obat->foto_obat);
            }

            // Simpan foto obat baru
            $file = $request->file('foto_obat');
            $foto_obatName = Str::random(24) . '.' . $file->extension();
            $file->storeAs('foto_obat', $foto_obatName, 'public');
            $obat->foto_obat = $foto_obatName;
            $obat->save();

            // Commit transaksi
            DB::commit();

            return back()->with('success', 'Berhasil mengubah foto obat');
        } catch (\Throwable $th) {
            // Rollback transaksi jika terjadi kesalahan
            DB::rollback();

            // Tangani kesalahan dengan lebih baik
            if ($th instanceof ModelNotFoundException) {
                return back()->with('error', 'Obat dengan ID ' . $uuid . ' tidak ditemukan');
            } elseif ($th instanceof ValidationException) {
                return back()->withErrors($th->errors())->withInput()->with('error', 'Gagal mengubah foto obat: ' . $th->getMessage());
            } else {
                // Logging kesalahan ke file log atau sistem monitoring
                Log::error('Gagal mengubah foto obat: ' . $th->getMessage());
                return back()->with('error', 'Gagal mengubah foto obat');
            }
        }
    }

    public function pengaturanInventaris()
    {
        $data = SatuanObat::orderBy('nama_satuan', 'asc')->get();
        $dataAlat = KategoriAlat::orderBy('nama_kategori', 'asc')->get();
        $dataAlatSisa = KategoriConsumable::orderBy('nama_kategori', 'asc')->get();
        // dd($data);
        return view('inventaris.pengaturan', compact('data', 'dataAlat', 'dataAlatSisa'));
    }
    public function printInventaris(Request $request)
    {
        $request->validate([
            'jenis_data' => 'required', // obat, alat, alat_sisa
        ]);

        // get time
        Carbon::setLocale('id');
        $time = Carbon::now()->isoFormat('dddd, D MMMM Y, HH:mm');
        
        try {
            if ($request->jenis_data == 'obat') {
                $data = InventoryObat::orderBy('nama_obat', 'asc')
                ->with('SatuanObat')
                ->get();
                $view = 'print.inventaris';
                $type = "Obat";
            } elseif ($request->jenis_data == 'alat_terpakai') {
                $data = InventoryAlat::with(relations: 'KategoriAlat')
                ->orderBy('nama_alat', 'asc')
                ->get();
                $view = 'print.inventaris-alat-terpakai';
                $type = "Alat Terpakai";
            } elseif ($request->jenis_data == 'alat_tersisa') {
                $data = InventoryConsumable::with(relations: 'KategoriConsumable')
                ->orderBy('nama_alat', 'asc')
                ->get();;
                $view = 'print.inventaris-alat-tersisa';
                $type = "Alat Tersisa";
            }

            $pdf = Pdf::loadView($view, compact('data', 'time'))->setPaper('a3', 'landscape');
            $fileName = preg_replace('/[^A-Za-z0-9_\-]/', '_', 'Laporan Inventaris '.$type.'_'. $time) . '.pdf';
            $filePath = storage_path('app/public/' . $fileName);

            // Simpan file pdf
            $pdf->save($filePath);
            // Mengembalikan URL atau path ke file PDF yang baru disimpan
            return response()->download($filePath)->deleteFileAfterSend(true);
        } catch (\Exception $th) {
            Log::error('Gagal Mencetak Laporan Inventaris: ' . $th->getMessage());
            return back()->with('error', 'Gagal Mencetak Laporan Inventaris:' . $th->getMessage());
        }
    }

    public function storeKategori(Request $request)
    {
        try {
            // Validasi input
            $request->validate([
                'nama_satuan' => 'required|string|unique:satuan_obats,nama_satuan',
            ]);

            // Mulai transaksi database
            DB::beginTransaction();

            // Tambahkan kategori baru
            $satuanObat = SatuanObat::create([
                'nama_satuan' => $request->nama_satuan,
            ]);

            // Commit transaksi
            DB::commit();

            return back()->with('success', 'Berhasil menambahkan kategori obat');
        } catch (\Throwable $th) {
            // Rollback transaksi jika terjadi kesalahan
            DB::rollback();

            // Tangani kesalahan dengan lebih baik
            if ($th instanceof ValidationException) {
                return back()->withErrors($th->errors())->withInput()->with('error', 'Gagal menambahkan kategori obat: ' . $th->getMessage());
            } else {
                // Logging kesalahan ke file log atau sistem monitoring
                Log::error('Gagal menambahkan kategori obat: ' . $th->getMessage());
                return back()->with('error', 'Gagal menambahkan kategori obat');
            }
        }
    }

    public function updateKategori(Request $request, $id)
    {
        try {
            // Validasi input
            $request->validate([
                'nama_satuan' => 'required|string',
            ]);

            // Mulai transaksi database
            DB::beginTransaction();

            // Periksa apakah satuan obat dengan id yang diberikan ada
            $satuanObat = SatuanObat::where('id', $id)->firstOrFail();

            // Update nama satuan obat
            $satuanObat->nama_satuan = $request->nama_satuan;
            $satuanObat->save();

            // Commit transaksi
            DB::commit();

            return back()->with('success', 'Berhasil mengubah kategori obat');
        } catch (\Throwable $th) {
            // Rollback transaksi jika terjadi kesalahan
            DB::rollback();

            // Tangani kesalahan dengan lebih baik
            if ($th instanceof ModelNotFoundException) {
                return back()->with('error', 'Kategori obat dengan ID ' . $id . ' tidak ditemukan');
            } elseif ($th instanceof ValidationException) {
                return back()->withErrors($th->errors())->withInput()->with('error', 'Gagal mengubah kategori obat: ' . $th->getMessage());
            } else {
                // Logging kesalahan ke file log atau sistem monitoring
                Log::error('Gagal mengubah kategori obat: ' . $th->getMessage());
                return back()->with('error', 'Gagal mengubah kategori obat');
            }
        }
    }

    public function deleteKategori($id)
    {
        try {
            // Mulai transaksi database
            DB::beginTransaction();

            $getInventori = InventoryObat::where('satuan_id', $id)->get();

            if ($getInventori->count() > 0) {
                // return back()->with('error', 'Kategori obat tidak bisa dihapus karena masih digunakan');
                foreach ($getInventori as $inventori) {
                    $inventori->update([
                        'satuan_id' => null,
                    ]);
                }
            }

            // Periksa apakah satuan obat dengan id yang diberikan ada
            $satuanObat = SatuanObat::where('id', $id)->firstOrFail();

            // Hapus satuan obat
            $satuanObat->delete();

            // Commit transaksi
            DB::commit();

            return back()->with('success', 'Berhasil menghapus kategori obat');
        } catch (\Throwable $th) {
            // Rollback transaksi jika terjadi kesalahan
            DB::rollback();

            // Tangani kesalahan dengan lebih baik
            if ($th instanceof ModelNotFoundException) {
                return back()->with('error', 'Kategori obat dengan ID ' . $id . ' tidak ditemukan');
            } else {
                // Logging kesalahan ke file log atau sistem monitoring
                Log::error('Gagal menghapus kategori obat: ' . $th->getMessage());
                return back()->with('error', 'Gagal menghapus kategori obat');
                // return back()->with('error', $th->getMessage());
            }
        }
    }
    public function update(Request $request, $id)
    {
        try {
            // Validasi input
            $request->validate([
                'satuan_obat' => 'required|exists:satuan_obats,nama_satuan',
            ]);

            // Mulai transaksi database
            DB::beginTransaction();

            // get satuan obat id
            $satuanID = SatuanObat::where('nama_satuan', $request->satuan_obat)->firstOrFail()->id;

            // Periksa apakah obat dengan id yang diberikan ada
            $obat = InventoryObat::where('id', $id)->firstOrFail();
            // Update obat
            $obat->satuan_id = $satuanID;
            $obat->save();

            // Commit transaksi
            DB::commit();

            return back()->with('success', 'Berhasil mengubah obat');
        } catch (\Throwable $th) {
            // Rollback transaksi jika terjadi kesalahan
            DB::rollback();

            // Tangani kesalahan dengan lebih baik
            if ($th instanceof ModelNotFoundException) {
                return back()->with('error', 'Obat dengan ID ' . $id . ' tidak ditemukan');
            } elseif ($th instanceof ValidationException) {
                return back()->withErrors($th->errors())->withInput()->with('error', 'Gagal mengubah obat: ' . $th->getMessage());
            } else {
                // Logging kesalahan ke file log atau sistem monitoring
                Log::error('Gagal mengubah obat: ' . $th->getMessage());
                return back()->with('error', 'Gagal mengubah obat');
            }
        }
    }
}
