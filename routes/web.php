<?php

use App\Models\Barang;
use App\Models\Produk;
use App\Models\Satuan;
use App\Models\Kategori;
use App\Models\Supplier;
use App\Models\ApprovedItems;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ApiController;
use App\Http\Controllers\PdfController;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Storage;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Response;
use App\Http\Controllers\StockController;
use App\Http\Controllers\BarangController;
use App\Http\Controllers\ProdukController;
use App\Http\Controllers\SatuanController;
use App\Http\controllers\LaporanController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\CostumerController;
use App\Http\Controllers\KategoriController;
use App\Http\Controllers\StockOutController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\ApiProdukController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\PembelianController;
use App\Http\Controllers\PenjualanController;
use App\Http\Controllers\PermissionController;
use App\Http\Controllers\ReportStockController;
use App\Http\Controllers\OrderLanjutanController;
use App\Http\Controllers\PesananBarangController;
use App\Http\Controllers\LaporanPreorderController;

Route::get('/run-migrate', function () {
    Artisan::call('migrate', ['--force' => true]);
    return 'Migration done!';
});

Route::get('/', function () {
    return view('auth.login');
});

Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::middleware('auth')->group(function () {

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::get('/permission', [PermissionController::class, 'index'])->name('permission.index');
    Route::get('/permission/create', [PermissionController::class, 'create'])->name('permission.create');
    Route::post('/permission', [PermissionController::class, 'store'])->name('permission.store');
    Route::get('/permission/{id}/edit', [PermissionController::class, 'edit'])->name('permission.edit');
    Route::post('/permission/{id}', [PermissionController::class, 'update'])->name('permission.update');
    Route::delete('/permission', [PermissionController::class, 'destroy'])->name('permission.destroy');

    Route::get('/roles', [RoleController::class, 'index'])->name('roles.index');
    Route::get('/roles/create', [RoleController::class, 'create'])->name('roles.create');
    Route::post('/roles', [RoleController::class, 'store'])->name('roles.store');
    Route::get('/roles/{id}/edit', [RoleController::class, 'edit'])->name('roles.edit');
    Route::post('/roles/{id}', [RoleController::class, 'update'])->name('roles.update');
    Route::delete('/roles', [RoleController::class, 'destroy'])->name('roles.destroy');

    Route::get('/kategori', [KategoriController::class, 'index'])->name('kategori.index');
    Route::get('/kategori/create', [KategoriController::class, 'create'])->name('kategori.create');
    Route::post('/kategori', [KategoriController::class, 'store'])->name('kategori.store');
    Route::get('/kategori/import', [KategoriController::class, 'import'])->name('kategori.import');
    Route::post('/kategori/import', [KategoriController::class, 'storeImport'])->name('kategori.storeImport');
    Route::get('/kategori/{id}/edit', [KategoriController::class, 'edit'])->name('kategori.edit');
    Route::post('/kategori/{id}', [KategoriController::class, 'update'])->name('kategori.update');
    Route::delete('/kategori', [KategoriController::class, 'destroy'])->name('kategori.destroy');

    Route::get('/barang', [BarangController::class, 'index'])->name('barang.index');
    Route::get('/barang/create', [BarangController::class, 'create'])->name('barang.create');
    Route::post('/barang', [BarangController::class, 'store'])->name('barang.store');
    Route::get('/barang/import', [BarangController::class, 'import'])->name('barang.import');
    Route::post('/barang/import', [BarangController::class, 'storeImport'])->name('barang.storeImport');
    Route::get('/barang/{id}/edit', [BarangController::class, 'edit'])->name('barang.edit');
    Route::post('/barang/{id}', [BarangController::class, 'update'])->name('barang.update');
    Route::delete('/barang', [BarangController::class, 'destroy'])->name('barang.destroy');

    Route::get('/produk', [ProdukController::class, 'index'])->name('produk.index');
    Route::get('/produk/create', [ProdukController::class, 'create'])->name('produk.create');
    Route::post('/produk', [ProdukController::class, 'store'])->name('produk.store');
    Route::get('/produk/import', [ProdukController::class, 'import'])->name('produk.import');
    Route::post('/produk/import', [ProdukController::class, 'storeImport'])->name('produk.storeImport');
    Route::get('/produk/{id}/edit', [ProdukController::class, 'edit'])->name('produk.edit');
    Route::post('/produk/{id}', [ProdukController::class, 'update'])->name('produk.update');
    Route::delete('/produk', [ProdukController::class, 'destroy'])->name('produk.destroy');

    Route::get('/supplier', [SupplierController::class, 'index'])->name('supplier.index');
    Route::get('/supplier/create', [SupplierController::class, 'create'])->name('supplier.create');
    Route::post('/supplier', [SupplierController::class, 'store'])->name('supplier.store');
    Route::get('/supplier/import', [SupplierController::class, 'import'])->name('supplier.import');
    Route::post('/supplier/import', [SupplierController::class, 'storeImport'])->name('supplier.storeImport');
    Route::get('/supplier/{id}/edit', [SupplierController::class, 'edit'])->name('supplier.edit');
    Route::post('/supplier/{id}', [SupplierController::class, 'update'])->name('supplier.update');
    Route::delete('/supplier', [SupplierController::class, 'destroy'])->name('supplier.destroy');

    Route::get('/costumer', [CostumerController::class, 'index'])->name('costumer.index');
    Route::get('/costumer/create', [CostumerController::class, 'create'])->name('costumer.create');
    Route::post('/costumer', [CostumerController::class, 'store'])->name('costumer.store');
    Route::get('/costumer/import', [CostumerController::class, 'import'])->name('costumer.import');
    Route::post('/costumer/import', [CostumerController::class, 'storeImport'])->name('costumer.storeImport');
    Route::get('/costumer/{id}/edit', [CostumerController::class, 'edit'])->name('costumer.edit');
    Route::post('/costumer/{id}', [CostumerController::class, 'update'])->name('costumer.update');
    Route::delete('/costumer', [CostumerController::class, 'destroy'])->name('costumer.destroy');

    Route::get('/satuan', [SatuanController::class, 'index'])->name('satuan.index');
    Route::get('/satuan/create', [SatuanController::class, 'create'])->name('satuan.create');
    Route::post('/satuan', [SatuanController::class, 'store'])->name('satuan.store');
    Route::get('/satuan/{id}/edit', [SatuanController::class, 'edit'])->name('satuan.edit');
    Route::post('/satuan/{id}', [SatuanController::class, 'update'])->name('satuan.update');
    Route::delete('/satuan', [SatuanController::class, 'destroy'])->name('satuan.destroy');

    Route::get('/pesanan', [PesananBarangController::class, 'index'])->name('pesanan.index');

    Route::get('/get-nama-barang-list', [ApiProdukController::class, 'getNamaBarangList']);
    Route::get('/get-satuan-list', [ApiProdukController::class, 'getSatuanList']);
    Route::get('/get-supplier-list', [ApiProdukController::class, 'getSupplierList']);
    Route::get('/get-product-list', [ApiProdukController::class, 'getProdukList']);
    Route::get('/get-kategori-list', [ApiProdukController::class, 'getKategoriList']);
    Route::get('/get-latest-harga/{produk_id}', [ApiProdukController::class, 'getLatestHarga']);
    Route::get('/get-produk-by-name/{nama}', [ApiProdukController::class, 'getProdukListByNamaBarang']);
    Route::get('/get-merek-options/{nama_barang}', [ApiProdukController::class, 'getMerekOptions'])
        ->where('nama_barang', '.*');




    // Route::get('/get-product-list', [PesananBarangController::class, 'getProductList']);

    Route::post('/pesanan', [PesananBarangController::class, 'store'])->name('pesanan.store');
    // Route untuk laporan pembelian barang



    Route::get('/polaporan', [LaporanPreorderController::class, 'index'])->name('polaporan.index');
    Route::get('/polaporan/{id}', [LaporanPreorderController::class, 'show'])->name('polaporan.show');
    Route::get('/get-preorder-items/{id}', [LaporanPreorderController::class, 'getPreorderItems']);
    Route::delete('/polaporan', [LaporanPreorderController::class, 'destroy'])->name('polaporan.destroy');
    Route::patch('/update-item-order-status/{id}', [LaporanPreorderController::class, 'updateStatus'])->name('item-order.update-status');
    Route::post('/update-margin/{id}', [LaporanPreorderController::class, 'updateMargin']);
    Route::post('/approve-preorder/{id}', [LaporanPreorderController::class, 'approvePreorder'])->name('approve.preorder');
    Route::get('/api/preorders/{id}/items', [LaporanPreorderController::class, 'getItems'])->name('preorders.items');


    Route::prefix('pembelian')->group(function () {
        Route::post('/stok-in', [StockController::class, 'inputStock'])->name('stok.in');
    });
    Route::get('/pembelian', [PembelianController::class, 'index'])->name('pembelian.index');
    Route::get('/pembelian/{id}', [PembelianController::class, 'show'])->name('pembelian.show');

    Route::get('/penjualan', [PenjualanController::class, 'index'])->name('penjualan.index');
    Route::get('/penjualan/show/{id}', [PenjualanController::class, 'show'])->name('penjualan.show');
    Route::post('/stock-out', [PenjualanController::class, 'store'])->name('stockOut.store');
    Route::get('/api/batch-search', [StockController::class, 'searchBatch']);
    Route::post('/stok-keluar', [StockOutController::class, 'store'])->name('stok.keluar');





    Route::get('/user', [UserController::class, 'index'])->name('user.index');
    Route::get('/user/create', [UserController::class, 'create'])->name('user.create');
    Route::post('/user', [UserController::class, 'store'])->name('user.store');
    Route::get('/user/{id}/edit', [UserController::class, 'edit'])->name('user.edit');
    Route::post('/user/{id}', [UserController::class, 'update'])->name('user.update');
    Route::delete('/user', [UserController::class, 'destroy'])->name('user.destroy');



    Route::post('/preview-penawaran', [PdfController::class, 'previewPenawaran'])->name('preview.penawaran');
    Route::get('/pembelian/{id}/pdf', [PdfController::class, 'generatePDF'])->name('pembelian.pdf');
    Route::get('/approved/{id}/pdf', [PdfController::class, 'penerimaanPDF'])->name('tandaTerima.pdf');
    Route::get('/gambar/{filename}', function ($filename) {
        $path = storage_path('app/public/image/' . $filename);

        if (!file_exists($path)) {

            abort(404);
        }

        return Response::file($path);
    });
    Route::get('/print-invoice/{id}', [PdfController::class, 'printInvoice'])->name('invoice.print');



    Route::get('/laporan', [LaporanController::class, 'index'])->name('laporan.index');
    Route::get('/laporan/transaksi', [LaporanController::class, 'index'])->name('laporan.transaksi');
    Route::get('/laporan/{id}/print', [LaporanController::class, 'printspb'])->name('laporan.print');
    Route::get('/laporan/export', [LaporanController::class, 'export'])->name('laporan.export');


    Route::get('/stock', [ReportStockController::class, 'StockReport'])->name('laporan.report_stock');

    Route::get('/laporan/stok-in', [ReportStockController::class, 'reportIndex'])->name('stok.report.index');
    Route::get('/laporan/stok-in/{approved}', [ReportStockController::class, 'reportDetail'])->name('stok.report.detail');
    Route::get('/laporan/stok', [ReportStockController::class, 'ExStockReport'])->name('laporan.stock_report');
    Route::get('/laporan/stok/export', [ReportStockController::class, 'exportStockReport'])->name('laporan.stock_export');

    Route::post('/order-lanjutan/store', [OrderLanjutanController::class, 'store']);
    Route::get('/order-lanjutan/{id}', [OrderLanjutanController::class, 'lanjutan'])->name('preorder.lanjutan');
    Route::post('/order-lanjutan/create', [OrderLanjutanController::class, 'createOrderLanjutan']);
    Route::post('/order-lanjutan/batal', [OrderLanjutanController::class, 'batalOrderLanjutan'])->name('preorder.lanjutan.batal');

    Route::get('/laporan/stockout', [ReportStockController::class, 'laporanStockOut'])->name('laporan.stockout_report');

    Route::get('/penjualan-periode', [DashboardController::class, 'penjualanPeriode']);
    Route::get('/barang-terlaris', [DashboardController::class, 'barangTerlaris']);










    // Route::get('/get-product-list', [ApiController::class, 'getProducts']);
    // Route::get('/get-kategori-list', [ApiController::class, 'getKategori']);
    // Route::get('/get-satuan-list', [ApiController::class, 'getSatuan']);
    // Route::get('/get-supplier-list', [ApiController::class, 'getSuppliers']);
});

require __DIR__ . '/auth.php';
