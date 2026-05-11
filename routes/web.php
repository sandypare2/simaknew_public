<?php
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DatausermController;
use App\Http\Controllers\DatacascadingmController;
use App\Http\Controllers\MappingpegawaimController;
use App\Http\Controllers\HistorytalentamController;
use App\Http\Controllers\MappingkpimController;
use App\Http\Controllers\DatapencapaianmController;
use App\Http\Controllers\KinerjapegawaimController;
use App\Http\Controllers\LaptalentamController;
use App\Http\Controllers\CetaktalentamController;
use App\Http\Controllers\DivisimController;
use App\Http\Controllers\MasterregionmController;
use App\Http\Controllers\MasterareamController;
use App\Http\Controllers\MasterlevelmController;
use App\Http\Controllers\MappingskormController;
use App\Http\Controllers\MappingpengukuranmController;

use App\Http\Controllers\RiwayatgrademController;
use App\Http\Controllers\KenaikangrademController;
use App\Http\Controllers\CetakkenaikanmController;

use App\Http\Controllers\PrealisasimController;
use App\Http\Controllers\PpenilaianmController;

use App\Http\Controllers\PeriodekinerjaController;

use App\Http\Controllers\FormsimkpController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Models\User;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/
Route::get('/', function () {
    return view('auth.login');
});
Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::controller(LoginController::class)->group(function () {
    Route::get('/login', 'index')->name('login')->middleware('guest');
    Route::post('/login', 'authenticate');
    Route::get('/logout', 'logout')->name('logout')->middleware('auth');
});

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::get('datauserm', [DatausermController::class, 'index'])->name('datauserm');
    Route::get('datauserm/{id}', [DatausermController::class, 'edit'])->name('datauserm.edit');
    Route::post('simpan-datauserm', [DatausermController::class, 'store'])->name('datauserm.store');
    Route::post('api/hapus-datauserm', [DatausermController::class, 'destroy']);
    Route::post('api/update-user-datauserm', [DatausermController::class, 'updateUser']);

    Route::get('datacascadingm', [DatacascadingmController::class, 'index'])->name('datacascadingm');
    Route::get('datacascadingm/{id}', [DatacascadingmController::class, 'edit'])->name('datacascadingm.edit');
    Route::post('simpan-datacascadingm', [DatacascadingmController::class, 'store'])->name('datacascadingm.store');
    Route::post('api/hapus-datacascadingm', [DatacascadingmController::class, 'destroy']);
    Route::post('api/import-datacascadingm', [DatacascadingmController::class, 'importCascading']);
    Route::post('api/importcabang-datacascadingm', [DatacascadingmController::class, 'importcabangCascading']);
    Route::post('api/reset-datacascadingm', [DatacascadingmController::class, 'resetCascading']);
    Route::post('api/proses-datacascadingm', [DatacascadingmController::class, 'prosesCascading']);
    Route::post('api/resetmapping-datacascadingm', [DatacascadingmController::class, 'resetmappingCascading']);
    Route::post('api/fetch-level-datacascadingm', [DatacascadingmController::class, 'fetchLevel']);    

    Route::get('mappingpegawaim', [MappingpegawaimController::class, 'index'])->name('mappingpegawaim');
    Route::get('mappingpegawaim/{id}', [MappingpegawaimController::class, 'edit'])->name('mappingpegawaim.edit');
    Route::post('simpan-mappingpegawaim', [MappingpegawaimController::class, 'store'])->name('mappingpegawaim.store');
    Route::post('api/hapus-mappingpegawaim', [MappingpegawaimController::class, 'destroy']);
    Route::post('api/fetch-level-mappingpegawaim', [MappingpegawaimController::class, 'fetchLevel']);   
    Route::post('api/fetch-pegawai-mappingkpim', [MappingpegawaimController::class, 'fetchPegawai']); 

    Route::get('historytalentam', [HistorytalentamController::class, 'index'])->name('historytalentam');
    Route::get('historytalentam/{id}', [HistorytalentamController::class, 'edit'])->name('historytalentam.edit');
    Route::post('simpan-historytalentam', [HistorytalentamController::class, 'store'])->name('historytalentam.store');
    Route::post('api/hapus-historytalentam', [HistorytalentamController::class, 'destroy']);

    Route::get('mappingkpim', [MappingkpimController::class, 'index'])->name('mappingkpim');
    Route::get('mappingkpim/{id}', [MappingkpimController::class, 'edit'])->name('mappingkpim.edit');
    Route::post('simpan-mappingkpim', [MappingkpimController::class, 'store'])->name('mappingkpim.store');
    Route::post('api/hapus-mappingkpim', [MappingkpimController::class, 'destroy']);
    Route::post('api/fetch-level-mappingkpim', [MappingkpimController::class, 'fetchLevel']);
    Route::get('api/fetch-detail-mappingkpim', [MappingkpimController::class, 'fetchDetail']);
    Route::get('api/fetch-rincian-mappingkpim', [MappingkpimController::class, 'fetchRincian']);
    Route::post('api/pilih-mappingkpim', [MappingkpimController::class, 'savePilih']);
    Route::post('api/batal-mappingkpim', [MappingkpimController::class, 'saveBatal']);

    Route::post('datapencapaianm/hitung-kpi-atasan', [DatapencapaianmController::class, 'hitungAtasan'])->name('datapencapaianm.hitung-kpi-atasan');
    Route::post('datapencapaianm/hitung-all-atasan', [DatapencapaianmController::class, 'hitungAtasanAll'])->name('datapencapaianm.hitung-all-atasan');
    Route::get('export-datapencapaianm', [DatapencapaianmController::class, 'exportDatapencapaian'])->name('export-datapencapaianm');
    Route::get('datapencapaianm', [DatapencapaianmController::class, 'index'])->name('datapencapaianm');
    Route::post('simpan-datapencapaianm', [DatapencapaianmController::class, 'store'])->name('datapencapaianm.store');
    Route::get('api/fetch-detail-datapencapaianm', [DatapencapaianmController::class, 'fetchDetail']);
    // Route::post('api/export-datapencapaianm', [DatapencapaianmController::class, 'exportDatapencapaian'])->name('export-datapencapaianm');

    Route::get('kinerjapegawaim', [KinerjapegawaimController::class, 'index'])->name('kinerjapegawaim');
    Route::get('kinerjapegawaim/{id}', [KinerjapegawaimController::class, 'edit'])->name('kinerjapegawaim.edit');
    Route::post('simpan-kinerjapegawaim', [KinerjapegawaimController::class, 'store'])->name('kinerjapegawaim.store');
    Route::post('api/proses-talenta-kinerjapegawaim', [KinerjapegawaimController::class, 'prosesTalenta']);
    Route::post('api/reset-talenta-kinerjapegawaim', [KinerjapegawaimController::class, 'resetTalenta']);
    Route::post('api/hapus-kinerjapegawaim', [KinerjapegawaimController::class, 'destroy']);
    Route::get('api/fetch-detail-kinerjapegawaim', [KinerjapegawaimController::class, 'fetchDetail']);
    Route::post('api/get-skorkuantitas-kinerjapegawaim', [KinerjapegawaimController::class, 'getSkorkuantitas']);
    Route::post('api/get-skorkualitas-kinerjapegawaim', [KinerjapegawaimController::class, 'getSkorkualitas']);
    Route::post('api/get-skorwaktu-kinerjapegawaim', [KinerjapegawaimController::class, 'getSkorwaktu']);
    Route::post('api/get-skorkinerja-kinerjapegawaim', [KinerjapegawaimController::class, 'getSkorkinerja']);
    Route::post('api/get-skorindividu-kinerjapegawaim', [KinerjapegawaimController::class, 'getSkorindividu']);
    Route::post('api/get-talenta-kinerjapegawaim', [KinerjapegawaimController::class, 'getTalenta']);

    Route::get('riwayatgradem', [RiwayatgrademController::class, 'index'])->name('riwayatgradem');
    Route::get('riwayatgradem/{id}', [RiwayatgrademController::class, 'edit'])->name('riwayatgradem.edit');
    Route::post('simpan-riwayatgradem', [RiwayatgrademController::class, 'store'])->name('riwayatgradem.store');
    Route::post('api/hapus-riwayatgradem', [RiwayatgrademController::class, 'destroy']);

    Route::get('kenaikangradem', [KenaikangrademController::class, 'index'])->name('kenaikangradem');
    Route::get('kenaikangradem/{id}', [KenaikangrademController::class, 'edit'])->name('kenaikangradem.edit');
    Route::post('simpan-kenaikangradem', [KenaikangrademController::class, 'store'])->name('kenaikangradem.store');
    Route::post('api/hapus-kenaikangradem', [KenaikangrademController::class, 'destroy']);
    Route::get('export-kenaikangradem', [KenaikangrademController::class, 'exportKenaikan'])->name('export-kenaikangradem');

    Route::get('cetakkenaikanm', [CetakkenaikanmController::class, 'index'])->name('cetakkenaikanm');

    Route::get('formsimkp', [FormsimkpController::class, 'index'])->name('formsimkp');

    Route::get('laptalentam', [LaptalentamController::class, 'index'])->name('laptalentam');
    Route::get('export-laptalentam', [LaptalentamController::class, 'exportTalenta'])->name('export-laptalentam');
    
    Route::get('cetaktalentam', [CetaktalentamController::class, 'index'])->name('cetaktalentam');

    Route::get('divisim', [DivisimController::class, 'index'])->name('divisim');
    Route::get('divisim/{id}', [DivisimController::class, 'edit'])->name('divisim.edit');
    Route::post('simpan-divisim', [DivisimController::class, 'store'])->name('divisim.store');
    Route::post('api/hapus-divisim', [DivisimController::class, 'destroy']);

    Route::get('masterregionm', [MasterregionmController::class, 'index'])->name('masterregionm');
    Route::get('masterregionm/{id}', [MasterregionmController::class, 'edit'])->name('masterregionm.edit');
    Route::post('simpan-masterregionm', [MasterregionmController::class, 'store'])->name('masterregionm.store');
    Route::post('api/hapus-masterregionm', [MasterregionmController::class, 'destroy']);

    Route::get('masteraream', [MasterareamController::class, 'index'])->name('masteraream');
    Route::get('masteraream/{id}', [MasterareamController::class, 'edit'])->name('masteraream.edit');
    Route::post('simpan-masteraream', [MasterareamController::class, 'store'])->name('masteraream.store');
    Route::post('api/hapus-masteraream', [MasterareamController::class, 'destroy']);

    Route::get('masterlevelm', [MasterlevelmController::class, 'index'])->name('masterlevelm');
    Route::get('masterlevelm/{id}', [MasterlevelmController::class, 'edit'])->name('masterlevelm.edit');
    Route::post('simpan-masterlevelm', [MasterlevelmController::class, 'store'])->name('masterlevelm.store');
    Route::post('api/hapus-masterlevelm', [MasterlevelmController::class, 'destroy']);

    Route::get('mappingskorm', [MappingskormController::class, 'index'])->name('mappingskorm');
    Route::get('mappingskorm/{id}', [MappingskormController::class, 'edit'])->name('mappingskorm.edit');
    Route::post('simpan-mappingskorm', [MappingskormController::class, 'store'])->name('mappingskorm.store');
    Route::post('api/hapus-mappingskorm', [MappingskormController::class, 'destroy']);

    Route::get('mappingpengukuranm', [MappingpengukuranmController::class, 'index'])->name('mappingpengukuranm');
    Route::get('mappingpengukuranm/{id}', [MappingpengukuranmController::class, 'edit'])->name('mappingpengukuranm.edit');
    Route::post('simpan-mappingpengukuranm', [MappingpengukuranmController::class, 'store'])->name('mappingpengukuranm.store');
    Route::post('api/hapus-mappingpengukuranm', [MappingpengukuranmController::class, 'destroy']);

    Route::get('prealisasim', [PrealisasimController::class, 'index'])->name('prealisasim');
    Route::get('prealisasim/{id}', [PrealisasimController::class, 'edit'])->name('prealisasim.edit');
    Route::post('simpan-prealisasim', [PrealisasimController::class, 'store'])->name('prealisasim.store');
    Route::post('api/hapus-prealisasim', [PrealisasimController::class, 'destroy']);

    Route::get('ppenilaianm', [PpenilaianmController::class, 'index'])->name('ppenilaianm');
    Route::get('ppenilaianm/{id}', [PpenilaianmController::class, 'edit'])->name('ppenilaianm.edit');
    Route::post('simpan-ppenilaianm', [PpenilaianmController::class, 'store'])->name('ppenilaianm.store');
    Route::post('api/hapus-ppenilaianm', [PpenilaianmController::class, 'destroy']);

    Route::get('periodekinerja', [PeriodekinerjaController::class, 'index'])->name('periodekinerja');
    Route::get('periodekinerja/{id}', [PeriodekinerjaController::class, 'edit'])->name('periodekinerja.edit');
    Route::post('simpan-periodekinerja', [PeriodekinerjaController::class, 'store'])->name('periodekinerja.store');
    Route::post('api/hapus-periodekinerja', [PeriodekinerjaController::class, 'destroy']);

    Route::get('matakuliahm', [MatakuliahmController::class, 'index'])->name('matakuliahm');
    Route::get('matakuliahm/{id}', [MatakuliahmController::class, 'edit'])->name('matakuliahm.edit');
    Route::post('simpan-matakuliahm', [MatakuliahmController::class, 'store'])->name('matakuliahm.store');
    Route::post('api/hapus-matakuliahm', [MatakuliahmController::class, 'destroy']);

    Route::get('masterkrsm', [MasterkrsmController::class, 'index'])->name('masterkrsm');
    Route::get('masterkrsm/{id}', [MasterkrsmController::class, 'edit'])->name('masterkrsm.edit');
    Route::post('simpan-masterkrsm', [MasterkrsmController::class, 'store'])->name('masterkrsm.store');
    Route::post('api/hapus-masterkrsm', [MasterkrsmController::class, 'destroy']);

    Route::get('dosenm', [DosenmController::class, 'index'])->name('dosenm');
    Route::get('dosenm/{id}', [DosenmController::class, 'edit'])->name('dosenm.edit');
    Route::post('simpan-dosenm', [DosenmController::class, 'store'])->name('dosenm.store');
    Route::post('api/hapus-dosenm', [DosenmController::class, 'destroy']);

    Route::get('prodim', [ProdimController::class, 'index'])->name('prodim');
    Route::get('prodim/{id}', [ProdimController::class, 'edit'])->name('prodim.edit');
    Route::post('simpan-prodim', [ProdimController::class, 'store'])->name('prodim.store');
    Route::post('api/hapus-prodim', [ProdimController::class, 'destroy']);

    Route::get('datamahasiswam', [DatamahasiswamController::class, 'index'])->name('datamahasiswam');
    Route::get('datamahasiswam/{id}', [DatamahasiswamController::class, 'edit'])->name('datamahasiswam.edit');
    Route::post('simpan-datamahasiswam', [DatamahasiswamController::class, 'store'])->name('datamahasiswam.store');
    Route::post('api/hapus-datamahasiswam', [DatamahasiswamController::class, 'destroy']);

    Route::get('userm', [UsermController::class, 'index'])->name('userm');
    Route::get('userm/{id}', [UsermController::class, 'show'])->name('userm.show');
    Route::get('userm/{id}', [UsermController::class, 'edit'])->name('userm.edit');
    Route::post('simpan-userm', [UsermController::class, 'store'])->name('userm.store');
    Route::post('api/hapus-userm', [UsermController::class, 'destroy']);
    Route::post('api/fetch-afiliasi-userm', [UsermController::class, 'fetchAfiliasi']);
    Route::post('api/fetch-afiliasi2-userm', [UsermController::class, 'fetchAfiliasi2']);
    Route::get('edit-profile', [UsermController::class, 'editProfile']);
    Route::post('api/ganti-pass', [UsermController::class, 'gantipass']);
    Route::post('api/reset-pass', [UsermController::class, 'resetpass']);

});    