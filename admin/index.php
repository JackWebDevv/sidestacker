<?php
require_once('../includes/session_manager.php');
require_once('../includes/db_connect.php');
require_once('verify_admin.php');
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sidestacker Admin Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body class="bg-gray-100">
    <div class="flex min-h-screen">
        <!-- Sidebar -->
        <nav class="bg-gray-900 w-64 text-white flex flex-col p-4 fixed h-full">
            <h1 class="text-2xl font-bold mb-6">Admin Dashboard</h1>
            <ul class="space-y-2">
                <li><a href="dashboard.php" class="block px-4 py-2 rounded hover:bg-gray-700">Overview</a></li>
                <li><a href="users.php" class="block px-4 py-2 rounded hover:bg-gray-700">User Management</a></li>
                <li><a href="analytics.php" class="block px-4 py-2 rounded hover:bg-gray-700">Analytics</a></li>
                <li><a href="content.php" class="block px-4 py-2 rounded hover:bg-gray-700">Content Management</a></li>
                <li><a href="revenue.php" class="block px-4 py-2 rounded hover:bg-gray-700">Revenue Tracking</a></li>
            </ul>
            <div class="mt-auto">
                <a href="logout.php" class="block text-center py-2 bg-red-600 rounded hover:bg-red-700">Logout</a>
            </div>
        </nav>
        
        <!-- Main Content -->
        <main class="ml-64 p-8 flex-1">
            <h2 class="text-3xl font-bold mb-6">Welcome to the Admin Dashboard</h2>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div class="bg-white p-6 rounded-lg shadow-md text-center">
                    <h3 class="text-lg font-semibold">Total Users</h3>
                    <p class="text-4xl font-bold text-blue-600" id="totalUsers">Loading...</p>
                </div>
                <div class="bg-white p-6 rounded-lg shadow-md text-center">
                    <h3 class="text-lg font-semibold">Monthly Revenue</h3>
                    <p class="text-4xl font-bold text-green-600" id="monthlyRevenue">Loading...</p>
                </div>
                <div class="bg-white p-6 rounded-lg shadow-md text-center">
                    <h3 class="text-lg font-semibold">Active Tools</h3>
                    <p class="text-4xl font-bold text-purple-600" id="activeTools">Loading...</p>
                </div>
            </div>
            
            <!-- Recent Activity -->
            <div class="mt-8 bg-white p-6 rounded-lg shadow-md">
                <h2 class="text-xl font-semibold mb-4">Recent Activity</h2>
                <div class="overflow-x-auto">
                    <table class="w-full border-collapse border border-gray-300 text-left">
                        <thead class="bg-gray-200">
                            <tr>
                                <th class="p-3 border">Time</th>
                                <th class="p-3 border">Action</th>
                                <th class="p-3 border">User</th>
                                <th class="p-3 border">Details</th>
                            </tr>
                        </thead>
                        <tbody id="recentActivity" class="divide-y divide-gray-300">
                            <!-- Activity rows will be populated dynamically -->
                        </tbody>
                    </table>
                </div>
            </div>
        </main>
    </div>

    <script>
        async function updateDashboard() {
            try {
                const response = await fetch('api/dashboard_stats.php');
                const data = await response.json();
                
                document.getElementById('totalUsers').textContent = data.totalUsers;
                document.getElementById('monthlyRevenue').textContent = '$' + data.monthlyRevenue;
                document.getElementById('activeTools').textContent = data.activeTools;
                
                const activityHtml = data.recentActivity.map(activity => `
                    <tr>
                        <td class="p-3 border">${activity.time}</td>
                        <td class="p-3 border">${activity.action}</td>
                        <td class="p-3 border">${activity.user}</td>
                        <td class="p-3 border">${activity.details}</td>
                    </tr>
                `).join('');
                
                document.getElementById('recentActivity').innerHTML = activityHtml;
            } catch (error) {
                console.error('Error updating dashboard:', error);
            }
        }

        updateDashboard();
        setInterval(updateDashboard, 300000);
    </script>
</body>
</html>
