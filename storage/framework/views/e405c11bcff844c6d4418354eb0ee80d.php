<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Certificado de Finalización</title>

    <?php
        // Convertir marco a Base64
        $bgPath = public_path('images/certificate.png');
        $bgBase64 = file_exists($bgPath) 
            ? 'data:image/png;base64,' . base64_encode(file_get_contents($bgPath)) 
            : null;

        // Convertir logo a Base64
        $logoBase64 = null;
        if (!empty($logo_path) && file_exists($logo_path)) {
            $type = pathinfo($logo_path, PATHINFO_EXTENSION);
            $logoBase64 = 'data:image/' . $type . ';base64,' . base64_encode(file_get_contents($logo_path));
        }
    ?>

    <style>
        @page {
            margin: 0px;
            size: a4 landscape;
        }

        html, body {
            margin: 0px;
            padding: 0px;
            width: 100%;
            height: 100%;
            font-family: 'Helvetica', 'Arial', sans-serif;
            background-color: #ffffff;
            color: #1e293b;
        }

        .page-container {
            position: relative;
            width: 100%;
            height: 100%;
        }

        /* Imagen de Fondo (Marco) */
        .bg-frame {
            position: absolute;
            top: 0px;
            left: 0px;
            width: 100%;
            height: 100%;
            z-index: 1;
        }

        /* Logo de la Plataforma */
        .logo-container {
            position: absolute;
            top: 35px;
            right: 50px;
            width: 130px;
            text-align: center;
            z-index: 10;
        }

        .logo-img {
            max-width: 110px;
            max-height: 100px;
        }

        /* Contenido Central sobrepuesto */
        .content-container {
            position: absolute;
            top: 0px;
            left: 0px;
            width: 100%;
            z-index: 5;
            padding-top: 65px;
            text-align: center;
        }

        /* Encabezados */
        .title {
            font-family: 'Times-Roman', 'Georgia', serif;
            font-size: 40px;
            font-weight: bold;
            letter-spacing: 4px;
            color: #0f172a;
            text-transform: uppercase;
            margin-bottom: 6px;
        }

        .sub-title {
            font-size: 11px;
            font-weight: bold;
            color: #64748b;
            text-transform: uppercase;
            letter-spacing: 2px;
            margin-bottom: 20px;
        }

        /* Nombre del Estudiante */
        .student-name {
            font-family: 'Times-Roman', 'Georgia', serif;
            font-size: 34px;
            font-weight: bold;
            color: #1e3a8a;
            text-transform: uppercase;
            letter-spacing: 2px;
            margin-bottom: 15px;
        }

        .achievement-text {
            font-size: 12px;
            color: #475569;
            margin-bottom: 10px;
        }

        /* Título del Curso */
        .course-title-wrapper {
            margin-bottom: 35px;
        }

        .course-title {
            font-family: 'Times-Roman', 'Georgia', serif;
            font-size: 22px;
            font-weight: bold;
            color: #b45309;
            text-transform: uppercase;
            letter-spacing: 1.5px;
            display: inline-block;
            border-bottom: 2px solid #b45309;
            padding-bottom: 4px;
        }

        /* Tabla de Credenciales / Pie */
        .credentials-table {
            width: 82%;
            margin: 0 auto;
            border-collapse: separate;
            border-spacing: 15px 0;
        }

        .credential-box {
            background-color: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 6px;
            padding: 10px 8px;
            text-align: center;
            width: 33.33%;
        }

        .box-label {
            font-size: 8px;
            color: #64748b;
            text-transform: uppercase;
            letter-spacing: 0.8px;
            font-weight: bold;
            margin-bottom: 4px;
        }

        .box-value {
            font-size: 11px;
            color: #0f172a;
            font-weight: bold;
        }
    </style>
</head>

<body>

    <div class="page-container">

        <!-- Marco en capa posterior -->
            <img class="bg-frame" src="<?php echo e(public_path('images/certificate.png')); ?>" alt="Marco Certificado">

            <!-- Logo de la Plataforma -->
            <div class="logo-container">
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(!empty($logo_path) && file_exists($logo_path)): ?>
                <img src="<?php echo e($logo_path); ?>" class="logo-img" alt="Logo">
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </div>

        <!-- Contenido dinámico -->
        <div class="content-container">
<br>
<br>
<br>
<br>
<br>
<br>
            <div class="title">Certificado de Finalización</div>
            <div class="sub-title">Otorgado con distinción a:</div>

            <div class="student-name"><?php echo e($student_name); ?></div>

            <div class="achievement-text">Por haber completado con éxito el curso:</div>

            <div class="course-title-wrapper">
                <span class="course-title"><?php echo e($course_title); ?></span>
            </div>

            <table class="credentials-table">
                <tr>
                    <td class="credential-box">
                        <div class="box-label">Fecha de Emisión</div>
                        <div class="box-value"><?php echo e($issue_date); ?></div>
                    </td>
                    <td class="credential-box">
                        <div class="box-label">Código de Verificación</div>
                        <div class="box-value"><?php echo e($certificate_code); ?></div>
                    </td>
                    <td class="credential-box">
                        <div class="box-label">Validado por</div>
                        <div class="box-value"><?php echo e($platform_name ?? config('app.name')); ?></div>
                    </td>
                </tr>
            </table>

        </div>

    </div>

</body>

</html><?php /**PATH C:\xampp\htdocs\www\Canvas-Church60\resources\views/certificates/template.blade.php ENDPATH**/ ?>