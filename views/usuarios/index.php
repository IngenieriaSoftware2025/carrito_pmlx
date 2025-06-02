<div class="row justify-content-center p-3">
    <div class="col-lg-10">
        <div class="card custom-card shadow-lg" style="border-radius: 10px; border: 1px solidrgb(54, 0, 61);">
            <div class="card-body p-3">
                <div class="row mb-3">
                    <h4 class="text-center mb-2 text-success">INGRESAR USUARIOS</h4>
                </div>

                <div class="row justify-content-center p-5 shadow-lg">

                    <form id="FormUsuarios">
                        <input type="hidden" id="usuario_id" name="usuario_id">

                        <div class="row mb-3 justify-content-center">
                            <div class="col-lg-6">
                                <label for="usuario_nombres" class="form-label">INGRESE SUS NOMRES</label>
                                <input type="text" class="form-control" id="usuario_nombres" name="usuario_nombres" placeholder="Paola Mercedes">
                            </div>
                            <div class="col-lg-6">
                                <label for="usuario_apellidos" class="form-label">INGRESE SUS APELLIDOS</label>
                                <input type="text" class="form-control" id="usuario_apellidos" name="usuario_apellidos" placeholder="Lopez Xitumul">
                            </div>
                        </div>

                        <div class="row mb-3 justify-content-center">
                            <div class="col-lg-6">
                                <label for="usuario_nit" class="form-label">INGRESE SU NIT</label>
                                <input type="number" class="form-control" id="usuario_nit" name="usuario_nit" placeholder="Ingrese aca su nit">
                            </div>
                            <div class="col-lg-6">
                                <label for="usuario_telefono" class="form-label">INGRESE SU TELEFONO</label>
                                <input type="number" class="form-control" id="usuario_telefono" name="usuario_telefono" placeholder="5744-4158">
                            </div>
                        </div>



                        <div class="row mb-3 justify-content-center mb-3">
                            <div class="col-lg-6">
                                <label for="usuario_correo" class="form-label">INGRESE SU CORREO ELECTRONICO</label>
                                <input type="email" class="form-control" id="usuario_correo" name="usuario_correo" placeholder="ejemplo@gmail.com">
                            </div>
                            <div class="col-lg-6">
                                <label for="usuario_estado" class="form-label">ESTADO DEL USUARIO</label>
                                <select name="usuario_estado" class="form-select" id="usuario_estado">
                                    <option value="" class="text-center"> -- ELECCION EL ESTADO -- </option>
                                    <option value="P">PRESENTE</option>
                                    <option value="F">FALTANDO</option>
                                    <option value="C">COMISION</option>
                                </select>

                            </div>
                        </div>

                        <div class="row mb-3 ">
                            <div class="col-lg-6">
                                <label for="usuario_fecha" class="form-label">FECHA</label>
                                <input type="datetime-local" class="form-control" id="usuario_fecha" name="usuario_fecha" placeholder="ejemplo@gmail.com">
                            </div>
                        </div>

                        <div class="row justify-content-center mt-5">
                            <div class="col-auto">
                                <button class="btn btn-success" type="submit" id="BtnGuardar">
                                    Guardar
                                </button>
                            </div>

                            <div class="col-auto ">
                                <button class="btn btn-warning d-none" type="button" id="BtnModificar">
                                    Modificar
                                </button>
                            </div>

                            <div class="col-auto">
                                <button class="btn btn-secondary" type="reset" id="BtnLimpiar">
                                    Limpiar
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
        <div class="card custom-card shadow-lg" style="border-radius: 10px; border: 1px solidrgba(81, 5, 117, 0.69);">
            <div class="card-body p-3">
                <h3 class="text-center text-success">USUARIOS REGISTRADOS</h3>
                
                <div class="table-responsive p-2">
                    <table class="table table-striped table-hover table-bordered w-100 table-sm" id="TableUsuarios">
                    </table>
                </div>

            </div>
        </div>
    </div>
</div>
</div>
<script src="<?= asset('build/js/usuarios/index.js') ?>"></script>