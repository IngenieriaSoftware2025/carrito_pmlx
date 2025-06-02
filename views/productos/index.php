<div class="row justify-content-center p-3">
    <div class="col-lg-10">
        <div class="card custom-card shadow-lg" style="border-radius: 10px; border: 1px solid #007bff;">
            <div class="card-body p-3">
                <div class="row mb-3">
                      <h3 class="text-center text-success">REGISTRAR PRODUCTOS</h3>
                </div>

                <div class="row justify-content-center p-5 shadow-lg">

                    <form id="FormProductos">
                        <input type="hidden" id="pro_id" name="pro_id">

                        <div class="row mb-3 justify-content-center">
                            <div class="col-lg-8">
                                <label for="pro_nombre" class="form-label">NOMBRE DEL PRODUCTO</label>
                                <input type="text" class="form-control" id="pro_nombre" name="pro_nombre" placeholder="Producto">
                            </div>
                        </div>

                        <div class="row mb-3 justify-content-center">
                            <div class="col-lg-4">
                                <label for="pro_precio" class="form-label">PRECIO UNITARIO</label>
                                <input type="number" class="form-control" id="pro_precio" name="pro_precio" placeholder="0" min="1">
                            </div>
                            <div class="col-lg-4">
                                <label for="pro_cantidad" class="form-label">CANTIDAD DISPONIBLE</label>
                                <input type="number" class="form-control" id="pro_cantidad" name="pro_cantidad" placeholder="0" min="0">
                            </div>
                        </div>

                        <div class="row justify-content-center mt-5">
                            <div class="col-auto">
                                <button class="btn btn-success" type="submit" id="BtnGuardar">
                                    <i class="bi bi-save me-1"></i>Guardar
                                </button>
                            </div>

                            <div class="col-auto ">
                                <button class="btn btn-warning d-none" type="button" id="BtnModificar">
                                    <i class="bi bi-pencil-square me-1"></i>Modificar
                                </button>
                            </div>

                            <div class="col-auto">
                                <button class="btn btn-secondary" type="reset" id="BtnLimpiar">
                                    <i class="bi bi-arrow-clockwise me-1"></i>Limpiar
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
    <div class="col-lg-10">
        <div class="card custom-card shadow-lg" style="border-radius: 10px; border: 1px solid #007bff;">
            <div class="card-body p-3">
                <h3 class="text-center text-success">PRODUCTOS REGISTRADOS</h3>

                <div class="table-responsive p-2">
                    <table class="table table-striped table-hover table-bordered w-100 table-sm" id="TableProductos">
                    </table>
                </div>

            </div>
        </div>
    </div>
</div>

<script src="<?= asset('build/js/productos/index.js') ?>"></script>