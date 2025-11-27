<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Envia tu Reporte</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<div class="container">
    <h1>Envía tu Reporte</h1>

    <form id="formReporte" onsubmit="enviarFormulario(event)" enctype="multipart/form-data">

        <label>Nombre Completo</label>
        <input type="text" name="nombre" required>

        <label>Tipo de Aporte</label>
        <select name="tipo_aporte" required>
            <option value="Fotografía">Fotografía</option>
            <option value="Video">Video</option>
            <option value="Mensaje">Mensaje</option>
            <option value="Documento">Documento</option>
        </select>

        <label>Mensaje para Cota 2050:</label>
        <textarea name="mensaje" rows="5" required></textarea>

        <label>Adjuntar Archivo (Opcional)</label>
        <input type="file" name="archivo">

        <button type="submit">GUARDAR EN LA CÁPSULA</button>
    </form>
</div>

<script>
function enviarFormulario(event) {
    event.preventDefault();

    let formData = new FormData(document.getElementById("formReporte"));

    fetch("https://tu-dominio.com/api/guardar.php", {
        method: "POST",
        body: formData
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            alert("Reporte guardado correctamente");
        } else {
            alert("Error: " + data.error);
        }
    });
}
</script>
</body>
</html>
