<?php

use App\Http\Controllers\ActaConformidadController;
use App\Http\Controllers\CajaChicaController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CarritoController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\ProductoController;
use App\Http\Controllers\SalidasController;
use App\Http\Controllers\EntradaController;
use App\Http\Controllers\ClienteController;
use App\Http\Controllers\CondicioneController;
use App\Http\Controllers\CServicioController;
use App\Http\Controllers\CventaController;
use App\Http\Controllers\DatoController;
use App\Http\Controllers\DocumentoController;
use App\Http\Controllers\HmarcaController;
use App\Http\Controllers\PendientesController;
use App\Http\Controllers\HservicioController;
use App\Http\Controllers\ObservacioneController;
use App\Http\Controllers\PendientesTecnicosController;
use App\Http\Controllers\PermisoController;
use App\Http\Controllers\PlantillaController;
use App\Http\Controllers\RecomendacioneController;
use App\Http\Controllers\ServicioController;
use App\Http\Controllers\TbcategoriaController;
use App\Http\Controllers\TbmarcaController;
use App\Http\Controllers\TbproductoController;
use App\Http\Controllers\TbsubcategoriaController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\SidebarController;
use App\Http\Controllers\TbprovinciaController;
use App\Http\Controllers\TrealizadoController;
use App\Http\Controllers\InventarioController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

//Rutas no autenticadas
Route::redirect('/', '/login');

Route::middleware(['auth:web'])->group(function () {
    //rutas autenticadas
    Route::get('/dashboard', [DashboardController::class, 'dashboard'])->name('dashboard');

    Route::resource('/actasConformidad', ActaConformidadController::class);

    Route::resource('/cajaChica', CajaChicaController::class);

    Route::resource('/pendientestecnicos', PendientesTecnicosController::class);

    // Modulo de los clientes - estado
    Route::resource('/clientes', ClienteController::class);
    Route::post('/clientes/update-ctg', [ClienteController::class, 'updateCtg'])->name('clientes.updateCtg');
    Route::post('/datos/{dato}', [DatoController::class, 'update'])->name('datos.update');

    // Route::resource('/roles', RoleController::class);

    // Inventario de productos
    Route::resource('/categories', CategoryController::class);
    Route::resource('/productos', ProductoController::class);
    route::post('/producto/{producto}', [ProductoController::class, 'update'])->name('producto.update');
    Route::resource('/salidas', SalidasController::class);
    Route::resource('/entradas', EntradaController::class);
    Route::post('/comprobarSalida', [SalidasController::class , 'comprobarSalida']);
    Route::post('/validarstock', [SalidasController::class , 'validarstock']);
    Route::post('/salidas.store', [SalidasController::class , 'store']);
    Route::put('/salidas.update', [SalidasController::class , 'update']);

    // Hojas de Servicio
    Route::resource('cuadropendientes', PendientesController::class);

    // Catalago de productos
    Route::post('/tbproductos/{tbproducto}', [TbproductoController::class, 'update'])->name('tbproductos.update');
    Route::resource('/tbproductos', TbproductoController::class);
    Route::resource('/tbmarcas', TbmarcaController::class);
    Route::resource('/tbcategorias', TbcategoriaController::class);
    Route::resource('/tbsubcategorias', TbsubcategoriaController::class);
    Route::resource('/sidebar', SidebarController::class);

    // Usuarios - roles - permisos - restore
    Route::get('trashed_users',[UserController::class,'trashed_users'])->name('users.trashed');
    Route::get('restore_users/{id}',[UserController::class,'restore'])->name('users.restore');
    Route::delete('delete_users_deletePermanently/{id}',[UserController::class,'deletePermanently'])->name('users.deletePermanently');

    // Cotizaciones por Servicio
    Route::resource('c_servicos',CServicioController::class);
    Route::post('/cservicios/cambiar_estado', [CServicioController::class, 'cambiarEstado'])->name('cservicios.cambiar_estado');
    Route::post('/cservicios/codigoSunat', [CServicioController::class, 'codigoSunat'])->name('cservicios.codigoSunat');
    Route::get('/balanzas', [CServicioController::class, 'balanza'])->name('balanza');
    Route::get('requiere/{id}', [CServicioController::class, 'cotiza'])->name('requiere.cotiza');
    Route::resource('plantillas',PlantillaController::class);
    Route::resource('trealizados',TrealizadoController::class);
    Route::resource('condiciones',CondicioneController::class);
    Route::resource('observaciones',ObservacioneController::class);
    Route::resource('documentos',DocumentoController::class);
    Route::resource('recomendaciones',RecomendacioneController::class);

    //Consulta para consumir la api de sunat para el modulo de clientes
    Route::get('/consultar-reniec', function (Request $request) {
        $numeroDocumento = $request->query('numero');
        $token = 'apis-token-10192.lgbp44SaTMi8tlleXcrrFYn79hAwerEU';

        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $token
        ])->get('https://api.apis.net.pe/v2/sunat/ruc', [
            'numero' => $numeroDocumento
        ]);

        return $response->json();
    });

    // Modulo de clientes - datos - estado
    Route::resource('datos', DatoController::class);
    Route::resource('/tbprovincias', TbprovinciaController::class);

    // Cotizaciones por venta
    Route::resource('/cventas', CventaController::class);
    Route::post('/guardarProductosCotizacion', [CventaController::class, 'guardarProductosCotizacion'])->name('cventas.guardarProductosCotizacion');
    Route::post('/consultarDatosCot', [CventaController::class, 'consultarDatosCot']);
    Route::post('/consultarDatosProductosCot', [CventaController::class, 'consultarDatosProductosCot']);
    Route::post('/cventas/cambiar_estado', [CventaController::class, 'cambiarEstado'])->name('cventas.cambiar_estado');

    // Hojas de Salida de los servicios
    Route::resource('hservicios',HservicioController::class);
    Route::post('/hservicios/{hservicio}', [HservicioController::class, 'update'])->name('hservicios.update');
    Route::resource('hmarcas',HmarcaController::class);
    Route::resource('servicios', ServicioController::class);
    Route::post('/servicios/cambiar_estado', [ServicioController::class, 'cambiarEstado'])->name('servicios.cambiar_estado');

    // Carrito de Compras
    Route::resource('carritos',CarritoController::class);

    Route::get('/inventario', [InventarioController::class, 'index'])->name('inventario.index');

});

Route::middleware(['auth:web'])->group(function () {
    Route::resource('/users', UserController::class);
    Route::resource('roles', RoleController::class);
    Route::resource('permisos', PermisoController::class);
});

// inicio de rutas con primevue
Route::post('/guardar-pendientes', [PendientesTecnicosController::class, 'guardarPendientes']);
Route::get('/api/archivos-precios', [PendientesTecnicosController::class, 'listarArchivos']);
Route::get('/fn_obtenerIndexListadePendientes', [PendientesTecnicosController::class,'obtenerIndexListadePendientes']);

Route::put('/datos/update/{id}', [DatoController::class, 'update'])->name('datos.update');
Route::get('/fn_obtenerDatosdeClientesparaPendientes', [PendientesController::class,'obtenerDatosdeClientesparaPendientes']);
Route::get('/fn_obtenerDatosClientesRelacionados', [DatoController::class,'obtenerDatosClientesRelacionados']);

Route::get('/fn_obtenerDatosConsultarClientes', [ClienteController::class,'obtenerDatosConsultarClientes']);
Route::get('/fn_obtenerDatosCiudades', [ClienteController::class,'obtenerDatosCiudades']);

Route::get('/fn_obtenerListadeTecnicosActa', [ActaConformidadController::class, 'obtenerListadeTecnicosActa']);
Route::get('/fn_obtenerListadeActaClientesPdf', [ActaConformidadController::class, 'obtenerListadeActaClientesPdf']);
Route::get('/fn_obtenerDatosdeActaClientes', [ActaConformidadController::class, 'obtenerDatosdeActaClientes']);
Route::get('/fn_obtenerListadeActaClientes', [ActaConformidadController::class, 'obtenerListadeActaClientes']);
Route::delete('/actasconformidad/{id}', [ActaConformidadController::class, 'destroy'])->name('actasConformidad.destroy');

Route::delete('/cajaChica/{idCaja}', [CajaChicaController::class, 'destroy'])->name('cajaChica.destroy');

Route::get('/fn_obtenerDatosdeCompras', [CarritoController::class, 'obtenerDatosdeCompras']);

Route::get('/fn_obtenerCategoriasProductos', [ProductoController::class, 'obtenerCategoriasProductos']);
Route::get('/fn_obtenerIndexInventario', [ProductoController::class, 'obtenerIndexInventario']);
Route::delete('/productos/{id}', [ProductoController::class, 'destroy'])->name('productos.destroy');

Route::get('/fn_ObtenerProductosCategorias', [CategoryController::class, 'obtenerProductosCategorias']);
Route::get('/fn_obtenerCategorias', [CategoryController::class, 'obtenerCategorias']);

Route::get('/salidas/edit/{salida}', [SalidasController::class, 'editSalidas'])->name('salidas.editSalidas');
Route::get('/fn_obtenerDatosConsultarProductosSalidas', [SalidasController::class, 'obtenerDatosConsultarProductosSalidas']);
Route::get('/fn_obtenerDatosUsuariosSalidas', [SalidasController::class, 'obtenerDatosUsuariosSalidas']);
Route::get('/listarSalidasRelacionadas', [SalidasController::class, 'listarSalidasRelacionadas'])->name('salidas.listarSalidasRelacionadas');
Route::get('/fn_obtenerListadeSalidasUnicas', [SalidasController::class, 'obtenerListadeSalidasUnicas']);
Route::get('/fn_obtenerIndexListadeSalidas', [SalidasController::class, 'obtenerIndexListadeSalidas']);
Route::delete('/salidas/{salida}', [SalidasController::class, 'destroy'])->name('salidas.destroy');

Route::put('/entradas/update/{id}', [EntradaController::class, 'update'])->name('entradas.update');
Route::delete('/entradas/{id}', [EntradaController::class, 'destroy'])->name('entradas.destroy');
Route::get('/fn_obtenerDatosConsultarProductos', [EntradaController::class, 'obtenerDatosConsultarProductos']);
Route::get('/fn_obtenerEntradas', [EntradaController::class, 'obtenerEntradas']);

Route::get('/fn_obtenerIndexCategorias', [CategoryController::class, 'obtenerIndexCategorias']);
Route::put('/categories/update/{category}', [CategoryController::class, 'update'])->name('categories.update');
Route::delete('/categories/{category}', [CategoryController::class, 'destroy'])->name('categories.destroy');

route::post('/roles', [RoleController::class, 'store'])->name('roles.store');
Route::get('/fn_obtenerRolesdeUsuariosIndex', [RoleController::class, 'obtenerRolesdeUsuariosIndex']);
Route::get('/fn_obtenerPermisosdeUsuariosIndex', [PermisoController::class, 'obtenerPermisosdeUsuariosIndex']);
route::get('/fn_obtenerPermisosdeRoles/{role}', [RoleController::class, 'obtenerPermisosdeRoles']);
Route::get('/fn_obtenerDatosdeUsuarios', [UserController::class, 'obtenerDatosdeUsuarios']);
Route::get('/fn_obtenerRolesdeUsuarios', [UserController::class, 'obtenerRolesdeUsuarios']);
route::post('/fn_registrarDatosdeUsuario', [UserController::class, 'store'])->name('fn_registrarDatosdeUsuario');

// termino de rutas con primevue

Route::get('/fn_consultarDatosDiagnosticosServ', [HservicioController::class, 'consultarDatosDiagnosticosServ']);

Route::get('/fn_registrarDatosCajaChica', [CajaChicaController::class, 'registrarDatosCajaChica']);
Route::get('/fn_obtenerDatosCajaChica', [CajaChicaController::class, 'obtenerDatosCajaChica']);
Route::get('/fn_consulta_EliminarPago', [CajaChicaController::class,'consulta_EliminarPago']);
Route::get('/fn_consulta_AgregarDetalleEgresoEditar', [CajaChicaController::class, 'consulta_AgregarDetalleEgresoEditar']);

Route::get('/fn_obtenerFechaHojaServicio', [HservicioController::class, 'obtenerFechaHojaServicio']);
Route::post('/fn_registrarInformesTecnicosdeCliente', [HservicioController::class, 'fn_registrarInformesTecnicosdeCliente'])->name('hservicios.fn_registrarInformesTecnicosdeCliente');
Route::get('/fn_verificarFechayDatos', [HservicioController::class, 'verificarFechayDatos']);
Route::get('/fn_obtenerHojasServicioAnteriores', [HservicioController::class, 'obtenerHojasServicioAnteriores']);
Route::get('/fn_guardarCotizacion', [CventaController::class, 'guardarCotizacion']);
Route::get('/fn_ObtenerHojasServicio', [HservicioController::class, 'obtenerHojasServicio']);
Route::get('/fn_ObtenerHojasServicioDiarias', [HservicioController::class, 'obtenerHojasServicioDiarias']);
Route::get('/validarIdCot', [CventaController::class, 'validarIdCot']);
route::get('/fn_ObtenerDatosSalidas', [ProductoController::class, 'fn_ObtenerDatosSalidas'])->name('fn_ObtenerDatosSalidas');
Route::get('/fn_obtenerSubcategoriasRegistradas', [TbsubcategoriaController::class, 'obtenerSubcategoriasRegistradas']);
Route::get('/fn_obtenerMarcasRegistradas', [TbmarcaController::class, 'obtenerMarcasRegistradas']);
Route::get('/fn_obtenerProvinciasRegistradas', [TbprovinciaController::class, 'obtenerProvinciasRegistradas']);
