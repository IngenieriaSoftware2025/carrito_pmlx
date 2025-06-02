<div class="row justify-content-center p-3">
    <div class="col-lg-12">
        <div class="card custom-card shadow-lg" style="border-radius: 10px; border: 1px solid #007bff;">
            <div class="card-body p-3">
                <div class="row mb-3">
                    <h4 class="text-center mb-2 text-primary">CARRITO DE COMPRAS</h4>
                </div>

                <div class="row p-3">
                    <form id="FormVentas">
                        <input type="hidden" id="venta_id" name="venta_id">

                        <div class="row mb-4">
                            <div class="col-lg-6">
                                <label for="venta_cliente_id" class="form-label">SELECCIONAR CLIENTE</label>
                                <select class="form-select" id="venta_cliente_id" name="venta_cliente_id">
                                    <option value="">-- SELECCIONE UN CLIENTE --</option>
                                </select>
                            </div>
                            <div class="col-lg-6 d-flex align-items-end">
                                <button type="button" class="btn btn-info" id="BtnCargarProductos">
                                    <i class="bi bi-cart-plus me-1"></i>Cargar Productos
                                </button>
                            </div>
                        </div>

                        <div class="row mb-4" id="seccionProductos" style="display: none;">
                            <div class="col-12">
                                <h5 class="text-primary">Productos Disponibles</h5>
                                <div class="table-responsive">
                                    <table class="table table-striped table-hover">
                                        <thead>
                                            <tr>
                                                <th width="5%">Seleccionar</th>
                                                <th width="40%">Producto</th>
                                                <th width="15%">Precio</th>
                                                <th width="15%">Stock</th>
                                                <th width="20%">Cantidad</th>
                                                <th width="5%">Acción</th>
                                            </tr>
                                        </thead>
                                        <tbody id="productosDisponibles">
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>

                        <div class="row mb-4" id="seccionCarrito" style="display: none;">
                            <div class="col-12">
                                <h5 class="text-success">Carrito de Compras</h5>
                                <div class="table-responsive">
                                    <table class="table table-bordered">
                                        <thead class="table-success">
                                            <tr>
                                                <th>Producto</th>
                                                <th>Precio Unitario</th>
                                                <th>Cantidad</th>
                                                <th>Subtotal</th>
                                                <th>Acción</th>
                                            </tr>
                                        </thead>
                                        <tbody id="carritoItems">
                                        </tbody>
                                        <tfoot>
                                            <tr class="table-info">
                                                <td colspan="3" class="text-end"><strong>TOTAL:</strong></td>
                                                <td><strong><span id="totalVenta">Q. 0.00</span></strong></td>
                                                <td></td>
                                            </tr>
                                        </tfoot>
                                    </table>
                                </div>
                            </div>
                        </div>

                        <div class="row justify-content-center mt-4">
                            <div class="col-auto">
                                <button class="btn btn-success" type="submit" id="BtnGuardarVenta" style="display: none;">
                                    <i class="bi bi-save me-1"></i>Guardar Venta
                                </button>
                            </div>
                            <div class="col-auto">
                                <button class="btn btn-warning" type="button" id="BtnModificarVenta" style="display: none;">
                                    <i class="bi bi-pencil-square me-1"></i>Modificar Venta
                                </button>
                            </div>
                            <div class="col-auto">
                                <button class="btn btn-secondary" type="button" id="BtnLimpiarVenta">
                                    <i class="bi bi-arrow-clockwise me-1"></i>Limpiar Todo
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row justify-content-center p-3">
    <div class="col-lg-12">
        <div class="card custom-card shadow-lg" style="border-radius: 10px; border: 1px solid #007bff;">
            <div class="card-body p-3">
                <h3 class="text-center">VENTAS REALIZADAS</h3>

                <div class="table-responsive p-2">
                    <table class="table table-striped table-hover table-bordered w-100 table-sm" id="TableVentas">
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modalDetalleVenta" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Detalle de Venta</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="contenidoDetalleVenta">
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                <button type="button" class="btn btn-primary" id="BtnGenerarFactura">
                    <i class="bi bi-file-pdf me-1"></i>Generar Factura
                </button>
            </div>
        </div>
    </div>
</div>

<script src="<?= asset('build/js/ventas/index.js') ?>"></script>