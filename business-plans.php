<?php
session_start();
require_once 'includes/db_connect.php';
?>
<!DOCTYPE html>
<html lang="en" class="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Business Plans - Sidestacker</title>
    <link rel="icon" type="image/svg+xml" href="favicon.svg">
    <link rel="icon" type="image/x-icon" href="favicon.ico">
    <link href="https://fonts.googleapis.com/css2?family=Clash+Display:wght@400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <script async src="https://pagead2.googlesyndication.com/pagead/js/adsbygoogle.js?client=ca-pub-YOUR_PUBLISHER_ID" crossorigin="anonymous"></script>
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    colors: {
                        primary: '#0070F3',
                        accent: '#7928CA',
                    },
                    fontFamily: {
                        clash: ['Clash Display', 'sans-serif'],
                    },
                },
            },
        }
    </script>
</head>
<body class="bg-gray-50 dark:bg-gray-900">
    <?php include 'includes/header.php'; ?>

    <!-- Top Advertisement -->
    <div class="max-w-6xl mx-auto px-4 mt-8">
        <div class="w-full h-24 bg-gray-100 dark:bg-gray-800 rounded-lg flex items-center justify-center">
            <span class="text-gray-400">Advertisement Space</span>
        </div>
    </div>

    <div class="mt-12 max-w-6xl mx-auto px-4">
        <div class="text-center mb-12">
            <h1 class="text-4xl font-clash font-bold mb-4 bg-gradient-to-r from-primary to-accent bg-clip-text text-transparent">
                Business Plans
            </h1>
            <p class="text-gray-600 dark:text-gray-400 text-lg">
                Choose the perfect plan for your business
            </p>
        </div>

        <!-- Example Business Plans Section -->
        <div class="mb-16">
            <h2 class="text-2xl font-clash font-bold mb-6">Example Business Plans</h2>
            
            <!-- Free Plans -->
            <div class="mb-8">
                <h3 class="text-xl font-clash font-semibold mb-4 text-primary">Free Templates</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="bg-white dark:bg-gray-800 p-6 rounded-xl shadow-sm">
                        <h4 class="font-bold mb-2">Coffee Shop Business Plan</h4>
                        <p class="text-gray-600 dark:text-gray-400 mb-4">Basic template for starting a small coffee shop business.</p>
                        <a href="#" class="text-primary hover:underline">Download Template</a>
                    </div>
                    <div class="bg-white dark:bg-gray-800 p-6 rounded-xl shadow-sm">
                        <h4 class="font-bold mb-2">Food Truck Business Plan</h4>
                        <p class="text-gray-600 dark:text-gray-400 mb-4">Simple template for mobile food business startup.</p>
                        <a href="#" class="text-primary hover:underline">Download Template</a>
                    </div>
                </div>
            </div>

            <!-- Side Advertisement -->
            <div class="my-8">
                <div class="w-full h-32 bg-gray-100 dark:bg-gray-800 rounded-lg flex items-center justify-center">
                    <span class="text-gray-400">Advertisement Space</span>
                </div>
            </div>

            <!-- Premium Plans -->
            <div class="mb-8">
                <h3 class="text-xl font-clash font-semibold mb-4 text-accent">Premium Templates</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="bg-white dark:bg-gray-800 p-6 rounded-xl shadow-sm border border-accent">
                        <div class="flex justify-between items-start mb-2">
                            <h4 class="font-bold">Tech Startup Business Plan</h4>
                            <span class="text-sm bg-accent text-white px-2 py-1 rounded">Premium</span>
                        </div>
                        <p class="text-gray-600 dark:text-gray-400 mb-4">Comprehensive template with financial projections and market analysis.</p>
                        <button class="text-accent hover:underline">Unlock Premium</button>
                    </div>
                    <div class="bg-white dark:bg-gray-800 p-6 rounded-xl shadow-sm border border-accent">
                        <div class="flex justify-between items-start mb-2">
                            <h4 class="font-bold">E-commerce Business Plan</h4>
                            <span class="text-sm bg-accent text-white px-2 py-1 rounded">Premium</span>
                        </div>
                        <p class="text-gray-600 dark:text-gray-400 mb-4">Detailed template for online retail business with marketing strategies.</p>
                        <button class="text-accent hover:underline">Unlock Premium</button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Pricing Plans -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <!-- Starter Plan -->
            <div class="bg-white dark:bg-gray-800 p-8 rounded-xl shadow-sm hover:shadow-md transition-shadow">
                <h3 class="text-xl font-clash font-bold text-gray-900 dark:text-white">Starter</h3>
                <p class="text-4xl font-bold mt-4">$29<span class="text-lg font-normal text-gray-600 dark:text-gray-400">/mo</span></p>
                <ul class="mt-6 space-y-4">
                    <li class="flex items-center text-gray-600 dark:text-gray-300">
                        <svg class="w-5 h-5 mr-2 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                        Basic Analytics
                    </li>
                    <li class="flex items-center text-gray-600 dark:text-gray-300">
                        <svg class="w-5 h-5 mr-2 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                        5 Projects
                    </li>
                    <li class="flex items-center text-gray-600 dark:text-gray-300">
                        <svg class="w-5 h-5 mr-2 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                        Basic Support
                    </li>
                </ul>
                <button class="w-full mt-8 py-3 px-4 bg-gray-100 text-gray-900 rounded-xl hover:bg-gray-200 transition-colors dark:bg-gray-700 dark:text-white dark:hover:bg-gray-600">
                    Get Started
                </button>
            </div>
            
            <!-- Pro Plan -->
            <div class="bg-white dark:bg-gray-800 p-8 rounded-xl shadow-md border-2 border-primary relative transform scale-105">
                <span class="absolute top-0 left-1/2 transform -translate-x-1/2 -translate-y-1/2 bg-primary text-white px-4 py-1 rounded-full text-sm">Most Popular</span>
                <h3 class="text-xl font-clash font-bold text-gray-900 dark:text-white">Professional</h3>
                <p class="text-4xl font-bold mt-4">$99<span class="text-lg font-normal text-gray-600 dark:text-gray-400">/mo</span></p>
                <ul class="mt-6 space-y-4">
                    <li class="flex items-center text-gray-600 dark:text-gray-300">
                        <svg class="w-5 h-5 mr-2 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                        Advanced Analytics
                    </li>
                    <li class="flex items-center text-gray-600 dark:text-gray-300">
                        <svg class="w-5 h-5 mr-2 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                        Unlimited Projects
                    </li>
                    <li class="flex items-center text-gray-600 dark:text-gray-300">
                        <svg class="w-5 h-5 mr-2 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                        Priority Support
                    </li>
                </ul>
                <button class="w-full mt-8 py-3 px-4 bg-primary text-white rounded-xl hover:bg-opacity-90 transition-colors">
                    Get Started
                </button>
            </div>
            
            <!-- Enterprise Plan -->
            <div class="bg-white dark:bg-gray-800 p-8 rounded-xl shadow-sm hover:shadow-md transition-shadow">
                <h3 class="text-xl font-clash font-bold text-gray-900 dark:text-white">Enterprise</h3>
                <p class="text-4xl font-bold mt-4">$299<span class="text-lg font-normal text-gray-600 dark:text-gray-400">/mo</span></p>
                <ul class="mt-6 space-y-4">
                    <li class="flex items-center text-gray-600 dark:text-gray-300">
                        <svg class="w-5 h-5 mr-2 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                        Custom Solutions
                    </li>
                    <li class="flex items-center text-gray-600 dark:text-gray-300">
                        <svg class="w-5 h-5 mr-2 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                        Dedicated Support
                    </li>
                    <li class="flex items-center text-gray-600 dark:text-gray-300">
                        <svg class="w-5 h-5 mr-2 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                        SLA Guarantee
                    </li>
                </ul>
                <button class="w-full mt-8 py-3 px-4 bg-gray-100 text-gray-900 rounded-xl hover:bg-gray-200 transition-colors dark:bg-gray-700 dark:text-white dark:hover:bg-gray-600">
                    Contact Sales
                </button>
            </div>
        </div>
    </div>

    <!-- Bottom Advertisement -->
    <div class="max-w-6xl mx-auto px-4 my-12">
        <div class="w-full h-24 bg-gray-100 dark:bg-gray-800 rounded-lg flex items-center justify-center">
            <span class="text-gray-400">Advertisement Space</span>
        </div>
    </div>

    <?php include 'includes/footer.php'; ?>

    <script>
        // Theme toggle functionality
        const themeToggle = document.getElementById('theme-toggle');
        themeToggle.addEventListener('click', () => {
            document.documentElement.classList.toggle('dark');
        });
    </script>
</body>
</html>
