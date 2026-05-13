<?php
include("../../includes/seguridad.php");
include("../../config/conexion.php");

// Obtener vehículos
$vehiculos = pg_query($conexion, "SELECT id_vehiculo, placa FROM vehiculos ORDER BY placa");

// Obtener conductores (para el select de responsable)
$conductores = pg_query($conexion, "SELECT id_conductor, nombre FROM conductores ORDER BY nombre");

// Configurar includes
$titulo = 'Registrar Mantenimiento | Pequeña Roma Tours';
$ruta_css = '../../assets/css/estilos.css';
$ruta_index = '../../index.php';
$titulo_nav = 'Registrar Mantenimiento';

include("../../includes/header.php");
include("../../includes/navbar.php");
?>

<!-- BOTONES VOLVER + VER LISTA -->
<div class="container mb-3">
    <div class="d-flex justify-content-between">
        <a href="listar_mantenimiento.php" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left"></i> Volver a Mantenimientos
        </a>
        <a href="listar_mantenimiento.php" class="btn btn-primary">
            <i class="bi bi-list-ul"></i> Ver Mantenimientos
        </a>
    </div>
</div>

<div class="container mb-5">
    <div class="main-card">
        <div class="card-header" style="background: linear-gradient(135deg, #fd7e14, #e06d0a);">
            <h4><i class="bi bi-tools"></i> Registro de Mantenimiento</h4>
        </div>
        <div class="card-body">
            <form method="POST" action="guardar_mantenimiento.php">
                <div class="row">
                    <!-- Fecha -->
                    <div class="col-md-4 mb-3">
                        <label class="form-label fw-semibold">Fecha</label>
                        <input type="date" name="fecha" class="form-control" value="<?= date('Y-m-d') ?>" required>
                    </div>

                    <!-- Vehículo -->
                    <div class="col-md-4 mb-3">
                        <label class="form-label fw-semibold">Vehículo</label>
                        <select name="vehiculo_id" class="form-select select2" required>
                            <option value="">Buscar vehículo...</option>
                            <?php while($v = pg_fetch_assoc($vehiculos)): ?>
                                <option value="<?= $v['id_vehiculo'] ?>"><?= htmlspecialchars($v['placa']) ?></option>
                            <?php endwhile; ?>
                        </select>
                    </div>

                    <!-- RESPONSABLE (CONDUCTOR) - MODIFICADO A SELECT -->
                    <div class="col-md-4 mb-3">
                        <label class="form-label fw-semibold">Responsable (Conductor)</label>
                        <select name="responsable_id" class="form-select" required>
                            <option value="">-- Seleccionar conductor --</option>
                            <?php while($conductor = pg_fetch_assoc($conductores)): ?>
                                <option value="<?= $conductor['id_conductor'] ?>">
                                    <?= htmlspecialchars($conductor['nombre']) ?>
                                </option>
                            <?php endwhile; ?>
                        </select>
                    </div>

                    <!-- Mecánico -->
                    <div class="col-md-4 mb-3">
                        <label class="form-label fw-semibold">Mecánico</label>
                        <input type="text" name="mecanico" class="form-control" placeholder="Nombre del mecánico" required>
                    </div>

                    <!-- Taller -->
                    <div class="col-md-4 mb-3">
                        <label class="form-label fw-semibold">Taller</label>
                        <input type="text" name="taller" class="form-control" placeholder="Nombre del taller" required>
                    </div>

                    <!-- Tipo -->
                    <div class="col-md-4 mb-3">
                        <label class="form-label fw-semibold">Tipo</label>
                        <select name="tipo" class="form-select" required>
                            <option value="">Seleccione</option>
                            <option value="Preventivo">🛠 Preventivo</option>
                            <option value="Correctivo">⚠ Correctivo</option>
                        </select>
                    </div>

                    <!-- Problema -->
                    <div class="col-md-12 mb-3">
                        <label class="form-label fw-semibold">Problema</label>
                        <input type="text" name="problema" class="form-control" placeholder="Ej: ruido en frenos, falla en motor..." required>
                    </div>

                    <!-- Costo -->
                    <div class="col-md-4 mb-3">
                        <label class="form-label fw-semibold">Costo</label>
                        <div class="input-group">
                            <span class="input-group-text">S/</span>
                            <input type="number" step="0.01" name="costo" class="form-control" placeholder="0.00">
                        </div>
                    </div>

                    <!-- Observaciones -->
                    <div class="col-md-12 mb-3">
                        <label class="form-label fw-semibold">Observaciones</label>
                        <textarea name="observaciones" class="form-control" rows="3" placeholder="Detalles del mantenimiento..."></textarea>
                    </div>
                </div>

                <div class="d-flex gap-2 mt-3">
                    <button type="submit" class="btn btn-success"><i class="bi bi-save"></i> Guardar</button>
                    <a href="listar_mantenimiento.php" class="btn btn-outline-secondary"><i class="bi bi-arrow-left"></i> Cancelar</a>
                </div>
            </form>
        </div>
    </div>
</div>

<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0/dist/css/select2.min.css" rel="stylesheet">
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0/dist/js/select2.min.js"></script>
<script>
$(document).ready(function() {
    $('.select2').select2({ width: '100%' });
});
</script>

<?php include("../../includes/footer.php"); ?>