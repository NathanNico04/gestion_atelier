import "bootstrap";
import "bootstrap-icons/font/bootstrap-icons.css";
import "bootstrap/dist/css/bootstrap.min.css";
import "./styles/app.css";
import Chart from 'chart.js/auto';

document.addEventListener("DOMContentLoaded", function () {
    const dataDiv = document.getElementById("chart-data");

    if (!dataDiv) {
        console.error("❌ Erreur : Le div #chart-data n'existe pas.");
        return;
    }

    // Récupérer les données et les parser
    let chartData;
    try {
        const rawData = dataDiv.getAttribute("data-fields");
        chartData = JSON.parse(rawData);
    } catch (error) {
        console.error("❌ Erreur lors du parsing des données JSON :", error);
        return;
    }

    const ctx = document.getElementById('myChart');
    if (!ctx) {
        console.error("❌ Erreur : Le canvas #myChart n'existe pas.");
        return;
    }

    // Vérifier si Chart.js est bien chargé
    if (typeof Chart === 'undefined') {
        console.error("❌ Chart.js ne semble pas être chargé.");
        return;
    }

    // Création du graphique
    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: ['0', '1', '2', '3', '4', '5'],
            datasets: [{
                label: 'Notes de l\'atelier',
                data: chartData,
                backgroundColor: 'rgba(54, 162, 235, 0.5)',
                borderColor: 'rgba(54, 162, 235, 1)',
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: { beginAtZero: true }
            }
        }
    });

    console.log("✅ Graphique chargé avec succès !");
});