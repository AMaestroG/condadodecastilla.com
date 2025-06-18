<?php
// Configuración de errores solo para desarrollo
//ini_set('display_errors', 1);
//ini_set('display_startup_errors', 1);
//error_reporting(E_ALL);

require_once __DIR__ . '/../includes/session.php';
ensure_session_started();

require_once __DIR__ . '/../includes/auth.php';
require_admin_login();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Estadísticas de Visitas Web</title>
    <link rel="stylesheet" href="../assets/css/epic_theme.css">
    <link rel="stylesheet" href="../assets/css/admin_theme.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js@3.7.0/dist/chart.min.js"></script> <!-- Specific version for stability -->
</head>
<body class="admin-page">
    <header class="admin-header">
        <h1>Panel de Estadísticas de Visitas</h1>
    </header>
    <nav class="admin-nav">
        <a href="../index.php">Inicio</a>
        <a href="edit_texts.php">Textos</a>
        <a href="tienda_admin.php">Tienda</a>
        <a href="../museo/editar_pieza.php">Piezas Museo</a>
        <a href="create_user.php">Crear Usuario</a>
        <a href="logout.php">Cerrar sesión</a>
    </nav>
    
    <div class="chart-container">
        <canvas id="visitsChart"></canvas>
    </div>
    <div id="errorMessage"></div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const canvas = document.getElementById('visitsChart');
            const errorMessageDiv = document.getElementById('errorMessage');

            if (!canvas) {
                console.error('Canvas element "visitsChart" not found!');
                errorMessageDiv.textContent = 'Error crítico: El elemento canvas para el gráfico no se encontró en la página.';
                errorMessageDiv.style.display = 'block';
                return;
            }
            const ctx = canvas.getContext('2d');
            let visitChartInstance; // To store the chart instance

            fetch('get_stats.php')
                .then(response => {
                    if (!response.ok) {
                        // Try to get more specific error from response body if possible for common HTTP errors
                        return response.json().then(errData => {
                            throw new Error(`HTTP error! Status: ${response.status}. Message: ${errData.message || response.statusText}`);
                        }).catch(() => {
                             throw new Error(`HTTP error! Status: ${response.status}. ${response.statusText}`);
                        });
                    }
                    return response.json();
                })
                .then(apiResponse => {
                    if (apiResponse && typeof apiResponse.success === 'boolean') {
                        if (apiResponse.success) {
                            if (apiResponse.data && apiResponse.data.length > 0) {
                                const labels = apiResponse.data.map(item => item.section_name);
                                const dataValues = apiResponse.data.map(item => item.total_visits);

                                if (visitChartInstance) {
                                    visitChartInstance.destroy(); 
                                }
                                visitChartInstance = new Chart(ctx, {
                                    type: 'bar',
                                    data: {
                                        labels: labels,
                                        datasets: [{
                                            label: 'Total de Visitas',
                                            data: dataValues,
                                            backgroundColor: 'rgba(54, 162, 235, 0.7)',
                                            borderColor: 'rgba(54, 162, 235, 1)',
                                            borderWidth: 1,
                                            hoverBackgroundColor: 'rgba(54, 162, 235, 0.9)'
                                        }]
                                    },
                                    options: {
                                        responsive: true,
                                        maintainAspectRatio: false, // Better for fitting in container
                                        scales: {
                                            y: {
                                                beginAtZero: true,
                                                title: {
                                                    display: true,
                                                    text: 'Número de Visitas',
                                                    font: { size: 14 }
                                                },
                                                ticks: {
                                                    precision: 0 // Ensure whole numbers for visit counts
                                                }
                                            },
                                            x: {
                                                title: {
                                                    display: true,
                                                    text: 'Sección del Sitio',
                                                    font: { size: 14 }
                                                }
                                            }
                                        },
                                        plugins: {
                                            legend: {
                                                display: true,
                                                position: 'top',
                                            },
                                            title: {
                                                display: true,
                                                text: 'Visitas por Sección del Sitio Web',
                                                font: { size: 18, weight: 'bold' },
                                                padding: { top: 10, bottom: 20 }
                                            },
                                            tooltip: {
                                                mode: 'index',
                                                intersect: false,
                                            }
                                        }
                                    }
                                });
                                errorMessageDiv.style.display = 'none'; // Hide error message on success
                            } else {
                                // Data array is empty
                                errorMessageDiv.textContent = apiResponse.message || 'No hay datos de visitas para mostrar.';
                                errorMessageDiv.style.display = 'block';
                                console.log('No visit data available as per API response.');
                                if (visitChartInstance) visitChartInstance.destroy(); // Clear any old chart
                            }
                        } else {
                            // API returned success: false
                            errorMessageDiv.textContent = 'Error al cargar estadísticas: ' + (apiResponse.message || 'La API indicó un fallo.');
                            errorMessageDiv.style.display = 'block';
                            console.error('API Error (success:false):', apiResponse.message);
                            if (visitChartInstance) visitChartInstance.destroy();
                        }
                    } else {
                        // Invalid JSON structure from API
                        throw new Error('Respuesta de API no válida o malformada.');
                    }
                })
                .catch(error => {
                    console.error('Error en fetch o procesamiento de datos:', error);
                    errorMessageDiv.textContent = 'No se pudo conectar con el servidor o procesar los datos de estadísticas. ' + error.message;
                    errorMessageDiv.style.display = 'block';
                    if (visitChartInstance) visitChartInstance.destroy();
                });
        });
    </script>
</body>
</html>
