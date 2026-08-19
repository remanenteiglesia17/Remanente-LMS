<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Certificado de Finalización</title>
    <style>
        @page {
            margin: 0;
            size: a4 landscape;
        }

        body {
            font-family: 'Times-Roman', 'Georgia', serif;
            margin: 0;
            padding: 24px;
            background-color: #fcfbf9;
            color: #1e293b;
        }

        /* Marcos y Bordes Elegantes */
        .outer-border {
            border: 2px solid #c59b27;
            padding: 8px;
            height: 94%;
        }

        .inner-border {
            border: 1px solid #c59b27;
            padding: 25px 35px;
            height: 92%;
            text-align: center;
            background-color: #ffffff;
            position: relative;
        }

        /* Encabezados */
        .title {
            font-size: 30px;
            font-weight: bold;
            letter-spacing: 3px;
            color: #0f172a;
            text-transform: uppercase;
            margin-top: 15px;
            margin-bottom: 5px;
        }

        .sub-title {
            font-family: 'Helvetica', 'Arial', sans-serif;
            font-size: 13px;
            color: #64748b;
            margin-bottom: 25px;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        /* Nombre del Estudiante */
        .student-name {
            font-size: 38px;
            font-weight: bold;
            color: #1e3a8a;
            text-transform: uppercase;
            margin: 15px 0;
            letter-spacing: 1.5px;
        }

        .achievement-text {
            font-family: 'Helvetica', 'Arial', sans-serif;
            font-size: 13px;
            color: #475569;
            margin-top: 15px;
            margin-bottom: 8px;
        }

        /* Título del Curso */
        .course-title {
            font-size: 24px;
            font-weight: bold;
            color: #b45309;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-bottom: 30px;
            line-height: 1.3;
        }

        /* Tabla de Credenciales / Pie */
        .credentials-table {
            width: 100%;
            margin-top: 25px;
            border-collapse: separate;
            border-spacing: 15px 0;
        }

        .credential-box {
            background-color: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 6px;
            padding: 10px 8px;
            text-align: center;
            width: 33%;
        }

        .box-label {
            font-family: 'Helvetica', 'Arial', sans-serif;
            font-size: 9px;
            color: #64748b;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 4px;
            font-weight: bold;
        }

        .box-value {
            font-family: 'Helvetica', 'Arial', sans-serif;
            font-size: 11px;
            color: #0f172a;
            font-weight: bold;
        }
    </style>
</head>
<body>

    <div class="outer-border">
        <div class="inner-border">
            
            <!-- Encabezado -->
            <div class="title">Certificado de Finalización</div>
            <div class="sub-title">Otorgado con distinción a:</div>

            <!-- Nombre -->
            <div class="student-name">{{ $student_name }}</div>

            <!-- Texto descriptivo -->
            <div class="achievement-text">Por haber completado con éxito el curso:</div>
            
            <!-- Nombre del Curso -->
            <div class="course-title">{{ $course_title }}</div>

            <!-- Cajas de Verificación (Pie de Página) -->
            <table class="credentials-table">
                <tr>
                    <td class="credential-box">
                        <div class="box-label">Fecha de Emisión</div>
                        <div class="box-value">{{ $issue_date }}</div>
                    </td>
                    <td class="credential-box">
                        <div class="box-label">Código de Verificación</div>
                        <div class="box-value">{{ $certificate_code }}</div>
                    </td>
                    <td class="credential-box">
                        <div class="box-label">Validado por</div>
                        <div class="box-value">{{ $platform_name ?? config('app.name') }}</div>
                    </td>
                </tr>
            </table>

        </div>
    </div>

</body>
</html>