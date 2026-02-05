<!DOCTYPE html>
<html lang="en" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hossam Eissa Task</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        @keyframes gradientShift {
            0% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
            100% { background-position: 0% 50%; }
        }
        
        @keyframes fadeInUp {
            from { opacity: 0; transform: translateY(30px); }
            to { opacity: 1; transform: translateY(0); }
        }
        
        @keyframes float {
            0%, 100% { transform: translateY(0px); }
            50% { transform: translateY(-10px); }
        }
        
        .gradient-bg {
            background: linear-gradient(-45deg, #1e1b4b, #312e81, #4c1d95, #581c87);
            background-size: 400% 400%;
            animation: gradientShift 15s ease infinite;
        }
        
        .fade-in-up { animation: fadeInUp 0.8s ease-out forwards; }
        .float { animation: float 3s ease-in-out infinite; }
        
        .code-block {
            background: linear-gradient(135deg, #1e293b 0%, #0f172a 100%);
            color: #e2e8f0;
            padding: 1.25rem;
            border-radius: 0.75rem;
            overflow-x: auto;
            border: 1px solid rgba(99, 102, 241, 0.2);
            transition: all 0.3s ease;
            position: relative;
        }
        
        .code-block:hover {
            border-color: rgba(99, 102, 241, 0.5);
            transform: translateY(-2px);
        }
        
        .nav-link {
            position: relative;
            transition: color 0.3s ease;
        }
        
        .nav-link::after {
            content: '';
            position: absolute;
            bottom: -4px;
            left: 0;
            width: 0;
            height: 2px;
            background: linear-gradient(90deg, #6366f1, #8b5cf6);
            transition: width 0.3s ease;
        }
        
        .nav-link:hover::after { width: 100%; }
        
        .card {
            backdrop-filter: blur(10px);
            background: rgba(30, 41, 59, 0.5);
            border: 1px solid rgba(99, 102, 241, 0.2);
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
        }
        
        .card:hover {
            transform: translateY(-5px);
            border-color: rgba(99, 102, 241, 0.5);
            background: rgba(30, 41, 59, 0.7);
        }
        
        .section {
            opacity: 0;
            transform: translateY(20px);
            transition: all 0.6s ease;
        }
        
        .section.visible {
            opacity: 1;
            transform: translateY(0);
        }
        
        .glass-effect {
            background: rgba(15, 23, 42, 0.7);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(99, 102, 241, 0.1);
        }
    </style>
</head>
<body class="bg-gradient-to-br from-gray-900 via-slate-900 to-gray-900 text-gray-100 min-h-screen">
    
    <header class="gradient-bg text-white py-16 shadow-2xl relative overflow-hidden">
        <div class="absolute inset-0 opacity-20">
            <div class="absolute w-96 h-96 bg-purple-500 rounded-full blur-3xl -top-20 -left-20 float"></div>
            <div class="absolute w-96 h-96 bg-indigo-500 rounded-full blur-3xl -bottom-20 -right-20 float" style="animation-delay: 1s"></div>
        </div>
        <div class="container mx-auto px-6 relative z-10 fade-in-up">
            <h1 class="text-5xl md:text-6xl font-bold mb-4 bg-clip-text text-transparent bg-gradient-to-r from-white to-purple-200">
                Hossam Eissa Task
            </h1>
            <p class="text-purple-200 text-xl md:text-2xl font-light">
                RESTful API with automatic dependency handling & role-based access control
            </p>
            <div class="mt-8 flex gap-4 flex-wrap">
                <div class="px-4 py-2 glass-effect rounded-lg hover:scale-105 transition-transform">
                    <span class="text-purple-300 text-sm">Laravel 12</span>
                </div>
                <div class="px-4 py-2 glass-effect rounded-lg hover:scale-105 transition-transform">
                    <span class="text-purple-300 text-sm">PHP 8.2+</span>
                </div>
                <div class="px-4 py-2 glass-effect rounded-lg hover:scale-105 transition-transform">
                    <span class="text-purple-300 text-sm">Docker Ready</span>
                </div>
            </div>
        </div>
    </header>

    <nav class="glass-effect sticky top-0 z-50 backdrop-blur-md border-b border-indigo-500/20">
        <div class="container mx-auto px-6 py-4">
            <div class="flex flex-wrap gap-6 text-sm">
                <a href="#features" class="nav-link text-gray-300 hover:text-indigo-400 font-medium">Features</a>
                <a href="#setup" class="nav-link text-gray-300 hover:text-indigo-400 font-medium">Setup</a>
                <a href="#docker" class="nav-link text-gray-300 hover:text-indigo-400 font-medium">Docker</a>
                <a href="#manual-setup" class="nav-link text-gray-300 hover:text-indigo-400 font-medium">Manual Setup</a>
                <a href="#test-users" class="nav-link text-gray-300 hover:text-indigo-400 font-medium">Test Users</a>
                <a href="#quick-start" class="nav-link text-gray-300 hover:text-indigo-400 font-medium">Quick Start</a>
                <a href="#api-docs" class="nav-link text-gray-300 hover:text-indigo-400 font-medium">API Docs</a>
                <a href="#troubleshooting" class="nav-link text-gray-300 hover:text-indigo-400 font-medium">Troubleshooting</a>
            </div>
        </div>
    </nav>

    <main class="container mx-auto px-6 py-12 max-w-7xl">
        
        <section id="features" class="section mb-20">
            <h2 class="text-4xl font-bold mb-8 text-transparent bg-clip-text bg-gradient-to-r from-indigo-400 to-purple-400">Features</h2>
            <div class="grid md:grid-cols-2 gap-6">
                <div class="card p-6 rounded-xl group">
                    <div class="flex items-start">
                        <span class="text-green-400 mr-4 text-3xl group-hover:scale-110 transition-transform">✅</span>
                        <div>
                            <h3 class="font-semibold text-xl text-white mb-2">Automatic Dependency Assignment</h3>
                            <p class="text-gray-400 text-sm">When assigning a task, all its dependencies are automatically assigned to the same user</p>
                        </div>
                    </div>
                </div>
                <div class="card p-6 rounded-xl group">
                    <div class="flex items-start">
                        <span class="text-green-400 mr-4 text-3xl group-hover:scale-110 transition-transform">✅</span>
                        <div>
                            <h3 class="font-semibold text-xl text-white mb-2">Hierarchical Due Date Validation</h3>
                            <p class="text-gray-400 text-sm">Dependencies must have earlier or equal due dates</p>
                        </div>
                    </div>
                </div>
                <div class="card p-6 rounded-xl group">
                    <div class="flex items-start">
                        <span class="text-green-400 mr-4 text-3xl group-hover:scale-110 transition-transform">✅</span>
                        <div>
                            <h3 class="font-semibold text-xl text-white mb-2">Circular Dependency Prevention</h3>
                            <p class="text-gray-400 text-sm">System detects and blocks circular dependency chains</p>
                        </div>
                    </div>
                </div>
                <div class="card p-6 rounded-xl group">
                    <div class="flex items-start">
                        <span class="text-green-400 mr-4 text-3xl group-hover:scale-110 transition-transform">✅</span>
                        <div>
                            <h3 class="font-semibold text-xl text-white mb-2">Role-Based Access Control</h3>
                            <p class="text-gray-400 text-sm">Managers and users have different permissions</p>
                        </div>
                    </div>
                </div>
                <div class="card p-6 rounded-xl group">
                    <div class="flex items-start">
                        <span class="text-green-400 mr-4 text-3xl group-hover:scale-110 transition-transform">✅</span>
                        <div>
                            <h3 class="font-semibold text-xl text-white mb-2">Advanced Filtering</h3>
                            <p class="text-gray-400 text-sm">Search, filter, sort on all task endpoints</p>
                        </div>
                    </div>
                </div>
                <div class="card p-6 rounded-xl group">
                    <div class="flex items-start">
                        <span class="text-green-400 mr-4 text-3xl group-hover:scale-110 transition-transform">✅</span>
                        <div>
                            <h3 class="font-semibold text-xl text-white mb-2">Scheduled Overdue Detection</h3>
                            <p class="text-gray-400 text-sm">Daily command marks overdue tasks as delayed</p>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <section id="setup" class="section mb-20">
            <h2 class="text-4xl font-bold mb-8 text-transparent bg-clip-text bg-gradient-to-r from-indigo-400 to-purple-400">Installation & Setup</h2>
            
            <div class="card p-6 mb-6 rounded-xl border-2 border-blue-500/50">
                <h3 class="font-semibold text-blue-300 mb-3 text-lg flex items-center">
                    <span class="text-2xl mr-2">📦</span> Prerequisites
                </h3>
                <p class="text-gray-300 mb-3">This project uses Docker for containerization. Make sure you have:</p>
                <ul class="text-sm text-gray-300 space-y-2 ml-6">
                    <li class="flex items-start">
                        <span class="text-blue-400 mr-2">•</span>
                        <span>Docker and Docker Compose installed on your system</span>
                    </li>
                    <li class="flex items-start">
                        <span class="text-blue-400 mr-2">•</span>
                        <span>Navigate to the project directory before running any commands</span>
                    </li>
                </ul>
            </div>

            <div class="glass-effect border-l-4 border-indigo-500 p-6 mb-6 rounded-r-xl hover:shadow-xl hover:shadow-indigo-500/20 transition-all">
                <h3 class="font-semibold text-indigo-300 mb-3 text-xl">⚡ Quick Setup (Automated)</h3>
                <p class="text-gray-300 mb-4">Navigate to the project directory and run the automated setup script:</p>
                <pre class="code-block text-sm">cd /path/to/project
./docker-setup.sh</pre>
                <p class="text-indigo-300 text-sm mt-4 flex items-center">
                    <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                    </svg>
                    Your application will be ready at <strong class="text-white">http://localhost:8000</strong>
                </p>
            </div>

            <div class="card p-6 rounded-xl">
                <h3 class="font-semibold text-white mb-4 text-lg">🚀 The script will automatically:</h3>
                <div class="grid md:grid-cols-2 gap-3">
                    <div class="flex items-center text-gray-300 hover:text-white transition-colors">
                        <span class="w-2 h-2 bg-indigo-500 rounded-full mr-3"></span>
                        Create .env file
                    </div>
                    <div class="flex items-center text-gray-300 hover:text-white transition-colors">
                        <span class="w-2 h-2 bg-indigo-500 rounded-full mr-3"></span>
                        Build Docker containers
                    </div>
                    <div class="flex items-center text-gray-300 hover:text-white transition-colors">
                        <span class="w-2 h-2 bg-indigo-500 rounded-full mr-3"></span>
                        Install dependencies
                    </div>
                    <div class="flex items-center text-gray-300 hover:text-white transition-colors">
                        <span class="w-2 h-2 bg-indigo-500 rounded-full mr-3"></span>
                        Generate app key
                    </div>
                    <div class="flex items-center text-gray-300 hover:text-white transition-colors">
                        <span class="w-2 h-2 bg-indigo-500 rounded-full mr-3"></span>
                        Run migrations
                    </div>
                    <div class="flex items-center text-gray-300 hover:text-white transition-colors">
                        <span class="w-2 h-2 bg-indigo-500 rounded-full mr-3"></span>
                        Set permissions
                    </div>
                </div>
            </div>
        </section>

        <section id="docker" class="section mb-20">
            <h2 class="text-4xl font-bold mb-8 text-transparent bg-clip-text bg-gradient-to-r from-indigo-400 to-purple-400">Docker Commands</h2>
            
            <div class="grid md:grid-cols-2 gap-6">
                <div class="card p-6 rounded-xl">
                    <h3 class="font-semibold text-white mb-4 text-lg flex items-center">
                        <span class="text-2xl mr-2">🐳</span> Managing Containers
                    </h3>
                    <pre class="code-block text-xs"># Start containers
docker-compose up -d

# Stop containers
docker-compose down

# View logs
docker-compose logs -f app

# Restart
docker-compose restart</pre>
                </div>

                <div class="card p-6 rounded-xl">
                    <h3 class="font-semibold text-white mb-4 text-lg flex items-center">
                        <span class="text-2xl mr-2">⚙️</span> Artisan Commands
                    </h3>
                    <pre class="code-block text-xs"># Run artisan
docker-compose exec app php artisan [command]

# Examples:
docker-compose exec app php artisan migrate
docker-compose exec app php artisan db:seed
docker-compose exec app php artisan cache:clear</pre>
                </div>
            </div>
        </section>

        <section id="manual-setup" class="section mb-20">
            <h2 class="text-4xl font-bold mb-8 text-transparent bg-clip-text bg-gradient-to-r from-indigo-400 to-purple-400">Manual Setup (Without Docker)</h2>
            
            <div class="card p-6 mb-6 rounded-xl border-2 border-orange-500/50">
                <h3 class="font-semibold text-orange-300 mb-3 text-lg flex items-center">
                    <span class="text-2xl mr-2">⚙️</span> System Requirements
                </h3>
                <div class="text-sm text-gray-300 space-y-2">
                    <p class="flex items-center"><span class="text-orange-400 mr-2">•</span> PHP 8.2 or higher</p>
                    <p class="flex items-center"><span class="text-orange-400 mr-2">•</span> Composer</p>
                    <p class="flex items-center"><span class="text-orange-400 mr-2">•</span> MySQL 8.0+ or MariaDB 10.3+</p>
                    <p class="flex items-center"><span class="text-orange-400 mr-2">•</span> Git</p>
                </div>
            </div>

            <div class="space-y-6">
                <div class="card p-6 rounded-xl">
                    <h3 class="font-semibold text-white mb-4 text-lg flex items-center">
                        <span class="bg-indigo-600 text-white rounded-full w-8 h-8 flex items-center justify-center mr-3 text-sm">1</span>
                        Clone the Repository
                    </h3>
                    <pre class="code-block text-sm relative group">git clone <span class="text-indigo-300">&lt;repository-url&gt;</span>
cd SoftxpertTask
<button onclick="copyCode(this)" class="absolute top-2 right-2 bg-indigo-600 hover:bg-indigo-700 text-white px-3 py-1 rounded text-xs opacity-0 group-hover:opacity-100 transition-opacity">Copy</button></pre>
                </div>

                <div class="card p-6 rounded-xl">
                    <h3 class="font-semibold text-white mb-4 text-lg flex items-center">
                        <span class="bg-indigo-600 text-white rounded-full w-8 h-8 flex items-center justify-center mr-3 text-sm">2</span>
                        Install PHP Dependencies
                    </h3>
                    <pre class="code-block text-sm relative group">composer install
<button onclick="copyCode(this)" class="absolute top-2 right-2 bg-indigo-600 hover:bg-indigo-700 text-white px-3 py-1 rounded text-xs opacity-0 group-hover:opacity-100 transition-opacity">Copy</button></pre>
                </div>

                <div class="card p-6 rounded-xl">
                    <h3 class="font-semibold text-white mb-4 text-lg flex items-center">
                        <span class="bg-indigo-600 text-white rounded-full w-8 h-8 flex items-center justify-center mr-3 text-sm">3</span>
                        Configure Environment
                    </h3>
                    <pre class="code-block text-sm relative group">cp .env.example .env
<button onclick="copyCode(this)" class="absolute top-2 right-2 bg-indigo-600 hover:bg-indigo-700 text-white px-3 py-1 rounded text-xs opacity-0 group-hover:opacity-100 transition-opacity">Copy</button></pre>
                    <p class="text-gray-400 text-sm mt-4 mb-3">Edit .env file and configure your database:</p>
                    <pre class="code-block text-sm relative group"><span class="text-purple-400">DB_CONNECTION</span>=mysql
<span class="text-purple-400">DB_HOST</span>=127.0.0.1
<span class="text-purple-400">DB_PORT</span>=3306
<span class="text-purple-400">DB_DATABASE</span>=task_management
<span class="text-purple-400">DB_USERNAME</span>=root
<span class="text-purple-400">DB_PASSWORD</span>=your_password
<button onclick="copyCode(this)" class="absolute top-2 right-2 bg-indigo-600 hover:bg-indigo-700 text-white px-3 py-1 rounded text-xs opacity-0 group-hover:opacity-100 transition-opacity">Copy</button></pre>
                </div>

                <div class="card p-6 rounded-xl">
                    <h3 class="font-semibold text-white mb-4 text-lg flex items-center">
                        <span class="bg-indigo-600 text-white rounded-full w-8 h-8 flex items-center justify-center mr-3 text-sm">4</span>
                        Create Database
                    </h3>
                    <p class="text-gray-400 text-sm mb-3">Create a MySQL database named <span class="text-indigo-300 font-mono">task_management</span>:</p>
                    <pre class="code-block text-sm relative group">mysql -u root -p
CREATE DATABASE task_management;
EXIT;
<button onclick="copyCode(this)" class="absolute top-2 right-2 bg-indigo-600 hover:bg-indigo-700 text-white px-3 py-1 rounded text-xs opacity-0 group-hover:opacity-100 transition-opacity">Copy</button></pre>
                </div>

                <div class="card p-6 rounded-xl">
                    <h3 class="font-semibold text-white mb-4 text-lg flex items-center">
                        <span class="bg-indigo-600 text-white rounded-full w-8 h-8 flex items-center justify-center mr-3 text-sm">5</span>
                        Generate Application Key
                    </h3>
                    <pre class="code-block text-sm relative group">php artisan key:generate
<button onclick="copyCode(this)" class="absolute top-2 right-2 bg-indigo-600 hover:bg-indigo-700 text-white px-3 py-1 rounded text-xs opacity-0 group-hover:opacity-100 transition-opacity">Copy</button></pre>
                </div>

                <div class="card p-6 rounded-xl">
                    <h3 class="font-semibold text-white mb-4 text-lg flex items-center">
                        <span class="bg-indigo-600 text-white rounded-full w-8 h-8 flex items-center justify-center mr-3 text-sm">6</span>
                        Run Migrations and Seeders
                    </h3>
                    <pre class="code-block text-sm relative group">php artisan migrate --seed
<button onclick="copyCode(this)" class="absolute top-2 right-2 bg-indigo-600 hover:bg-indigo-700 text-white px-3 py-1 rounded text-xs opacity-0 group-hover:opacity-100 transition-opacity">Copy</button></pre>
                    <p class="text-gray-400 text-sm mt-3">This will create all tables and seed test users</p>
                </div>

                <div class="card p-6 rounded-xl">
                    <h3 class="font-semibold text-white mb-4 text-lg flex items-center">
                        <span class="bg-indigo-600 text-white rounded-full w-8 h-8 flex items-center justify-center mr-3 text-sm">7</span>
                        Create Storage Link
                    </h3>
                    <pre class="code-block text-sm relative group">php artisan storage:link
<button onclick="copyCode(this)" class="absolute top-2 right-2 bg-indigo-600 hover:bg-indigo-700 text-white px-3 py-1 rounded text-xs opacity-0 group-hover:opacity-100 transition-opacity">Copy</button></pre>
                </div>

                <div class="card p-6 rounded-xl">
                    <h3 class="font-semibold text-white mb-4 text-lg flex items-center">
                        <span class="bg-indigo-600 text-white rounded-full w-8 h-8 flex items-center justify-center mr-3 text-sm">8</span>
                        Start Development Server
                    </h3>
                    <pre class="code-block text-sm relative group">php artisan serve
<button onclick="copyCode(this)" class="absolute top-2 right-2 bg-indigo-600 hover:bg-indigo-700 text-white px-3 py-1 rounded text-xs opacity-0 group-hover:opacity-100 transition-opacity">Copy</button></pre>
                    <p class="text-indigo-300 text-sm mt-4 flex items-center">
                        <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                        </svg>
                        Your application will be available at <strong class="text-white">http://localhost:8000</strong>
                    </p>
                </div>
            </div>
        </section>

        <section id="test-users" class="section mb-20">
            <h2 class="text-4xl font-bold mb-8 text-transparent bg-clip-text bg-gradient-to-r from-indigo-400 to-purple-400">Test Users</h2>
            
            <div class="grid md:grid-cols-2 gap-6">
                <div class="card bg-gradient-to-br from-purple-900/40 to-purple-800/40 p-8 rounded-xl border-2 border-purple-500/50 hover:border-purple-400">
                    <h3 class="font-bold text-purple-200 mb-4 text-2xl flex items-center">
                        <span class="text-4xl mr-3">👨‍💼</span> Manager
                    </h3>
                    <div class="space-y-3">
                        <p class="text-purple-100 flex items-center">
                            <span class="text-purple-400 mr-2">📧</span>
                            <strong class="mr-2">Email:</strong> manager@admin.com
                        </p>
                        <p class="text-purple-100 flex items-center">
                            <span class="text-purple-400 mr-2">🔑</span>
                            <strong class="mr-2">Password:</strong> 12345678
                        </p>
                        <div class="mt-4 p-3 bg-purple-950/50 rounded-lg border border-purple-500/30">
                            <p class="text-sm text-purple-200">Full access: create, update, delete, assign, view all tasks</p>
                        </div>
                    </div>
                </div>

                <div class="card bg-gradient-to-br from-emerald-900/40 to-emerald-800/40 p-8 rounded-xl border-2 border-emerald-500/50 hover:border-emerald-400">
                    <h3 class="font-bold text-emerald-200 mb-4 text-2xl flex items-center">
                        <span class="text-4xl mr-3">👤</span> User
                    </h3>
                    <div class="space-y-3">
                        <p class="text-emerald-100 flex items-center">
                            <span class="text-emerald-400 mr-2">📧</span>
                            <strong class="mr-2">Email:</strong> user@admin.com
                        </p>
                        <p class="text-emerald-100 flex items-center">
                            <span class="text-emerald-400 mr-2">🔑</span>
                            <strong class="mr-2">Password:</strong> 12345678
                        </p>
                        <div class="mt-4 p-3 bg-emerald-950/50 rounded-lg border border-emerald-500/30">
                            <p class="text-sm text-emerald-200">View assigned tasks, update status</p>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <section id="quick-start" class="section mb-20">
            <h2 class="text-4xl font-bold mb-8 text-transparent bg-clip-text bg-gradient-to-r from-indigo-400 to-purple-400">Quick Start</h2>
            
            <div class="space-y-6">
                <div class="card p-6 rounded-xl">
                    <h3 class="font-semibold text-white mb-3 text-lg">1️⃣ Login as Manager</h3>
                    <pre class="code-block text-xs">POST http://localhost:8000/api/login

{
  "email": "manager@admin.com",
  "password": "12345678",
  "device_name": "Postman"
}</pre>
                </div>

                <div class="card p-6 rounded-xl">
                    <h3 class="font-semibold text-white mb-3 text-lg">2️⃣ View All Tasks</h3>
                    <pre class="code-block text-xs">GET http://localhost:8000/api/tasks
Authorization: Bearer {token}</pre>
                    <p class="text-gray-400 text-sm mt-3">
                        <strong class="text-indigo-300">Query params:</strong> search, status, due_date_from, due_date_to, sort_by, sort_order, per_page
                    </p>
                </div>

                <div class="card p-6 rounded-xl">
                    <h3 class="font-semibold text-white mb-3 text-lg">3️⃣ Create Task with Dependencies</h3>
                    <pre class="code-block text-xs">POST http://localhost:8000/api/tasks
Authorization: Bearer {token}

{
  "title": "Complete Documentation",
  "description": "Write comprehensive docs",
  "due_date": "2026-02-20",
  "dependency_ids": [1, 2]
}</pre>
                </div>

                <div class="card p-6 rounded-xl">
                    <h3 class="font-semibold text-white mb-3 text-lg">4️⃣ Assign Task</h3>
                    <pre class="code-block text-xs">POST http://localhost:8000/api/tasks/1/assign
Authorization: Bearer {token}

{
  "assignee_id": 2
}</pre>
                </div>
            </div>
        </section>

        <section id="api-docs" class="section mb-20">
            <h2 class="text-4xl font-bold mb-8 text-transparent bg-clip-text bg-gradient-to-r from-indigo-400 to-purple-400">API Documentation</h2>
            
            <div class="card bg-gradient-to-br from-orange-900/40 to-amber-900/40 p-8 rounded-xl border-2 border-orange-500/50 mb-8 hover:shadow-2xl hover:shadow-orange-500/30">
                <h3 class="font-bold text-orange-200 mb-3 text-2xl flex items-center">
                    <span class="text-3xl mr-3">📚</span> Complete API Docs
                </h3>
                <p class="text-orange-100 mb-5">Full documentation with examples in Postman:</p>
                <a href="https://documenter.getpostman.com/view/25142654/2sBXc8nhpU" target="_blank" 
                   class="inline-flex items-center bg-gradient-to-r from-orange-600 to-amber-600 hover:from-orange-500 hover:to-amber-500 text-white font-semibold px-8 py-4 rounded-xl transform hover:scale-105 transition-all">
                    View Postman Docs 
                    <svg class="w-5 h-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/>
                    </svg>
                </a>
            </div>

            <div class="grid md:grid-cols-3 gap-6">
                <div class="card p-6 rounded-xl">
                    <h4 class="font-semibold text-indigo-300 mb-4 text-lg flex items-center">
                        <span class="text-2xl mr-2">🔐</span> Authentication
                    </h4>
                    <ul class="text-sm text-gray-300 space-y-2">
                        <li class="flex items-center hover:text-indigo-400 transition-colors cursor-pointer">
                            <span class="text-green-500 mr-2 font-mono">POST</span> /api/register
                        </li>
                        <li class="flex items-center hover:text-indigo-400 transition-colors cursor-pointer">
                            <span class="text-green-500 mr-2 font-mono">POST</span> /api/login
                        </li>
                        <li class="flex items-center hover:text-indigo-400 transition-colors cursor-pointer">
                            <span class="text-green-500 mr-2 font-mono">POST</span> /api/logout
                        </li>
                    </ul>
                </div>

                <div class="card p-6 rounded-xl">
                    <h4 class="font-semibold text-indigo-300 mb-4 text-lg flex items-center">
                        <span class="text-2xl mr-2">📋</span> Tasks
                    </h4>
                    <ul class="text-sm text-gray-300 space-y-2">
                        <li class="flex items-center hover:text-indigo-400 transition-colors cursor-pointer">
                            <span class="text-blue-500 mr-2 font-mono">GET</span> /api/tasks
                        </li>
                        <li class="flex items-center hover:text-indigo-400 transition-colors cursor-pointer">
                            <span class="text-green-500 mr-2 font-mono">POST</span> /api/tasks
                        </li>
                        <li class="flex items-center hover:text-indigo-400 transition-colors cursor-pointer">
                            <span class="text-blue-500 mr-2 font-mono">GET</span> /api/tasks/{id}
                        </li>
                        <li class="flex items-center hover:text-indigo-400 transition-colors cursor-pointer">
                            <span class="text-yellow-500 mr-2 font-mono">PUT</span> /api/tasks/{id}
                        </li>
                        <li class="flex items-center hover:text-indigo-400 transition-colors cursor-pointer">
                            <span class="text-red-500 mr-2 font-mono">DEL</span> /api/tasks/{id}
                        </li>
                        <li class="flex items-center hover:text-indigo-400 transition-colors cursor-pointer">
                            <span class="text-green-500 mr-2 font-mono">POST</span> /api/tasks/{id}/assign
                        </li>
                        <li class="flex items-center hover:text-indigo-400 transition-colors cursor-pointer">
                            <span class="text-purple-500 mr-2 font-mono">PATCH</span> /api/tasks/{id}/status
                        </li>
                    </ul>
                </div>

                <div class="card p-6 rounded-xl">
                    <h4 class="font-semibold text-indigo-300 mb-4 text-lg flex items-center">
                        <span class="text-2xl mr-2">👤</span> Profile
                    </h4>
                    <ul class="text-sm text-gray-300 space-y-2">
                        <li class="flex items-center hover:text-indigo-400 transition-colors cursor-pointer">
                            <span class="text-blue-500 mr-2 font-mono">GET</span> /api/me
                        </li>
                        <li class="flex items-center hover:text-indigo-400 transition-colors cursor-pointer">
                            <span class="text-yellow-500 mr-2 font-mono">PUT</span> /api/update-profile
                        </li>
                        <li class="flex items-center hover:text-indigo-400 transition-colors cursor-pointer">
                            <span class="text-green-500 mr-2 font-mono">POST</span> /api/change-password
                        </li>
                    </ul>
                </div>
            </div>
        </section>

        <section id="troubleshooting" class="section mb-20">
            <h2 class="text-4xl font-bold mb-8 text-transparent bg-clip-text bg-gradient-to-r from-indigo-400 to-purple-400">Troubleshooting</h2>
            
            <div class="space-y-6">
                <div class="card p-6 rounded-xl hover:shadow-xl hover:shadow-red-500/10">
                    <h3 class="font-semibold text-white mb-3 flex items-center">
                        <span class="text-red-500 mr-2 text-2xl">⚠️</span> Port already in use
                    </h3>
                    <p class="text-gray-400 text-sm mb-3">Change ports in docker-compose.yml:</p>
                    <pre class="code-block text-xs">ports:
  - "8001:80"  # nginx
  - "8081:80"  # phpmyadmin</pre>
                </div>

                <div class="card p-6 rounded-xl hover:shadow-xl hover:shadow-yellow-500/10">
                    <h3 class="font-semibold text-white mb-3 flex items-center">
                        <span class="text-yellow-500 mr-2 text-2xl">🔒</span> Permission issues
                    </h3>
                    <pre class="code-block text-xs">docker-compose exec app chmod -R 775 storage bootstrap/cache
docker-compose exec app chown -R www-data:www-data storage bootstrap/cache</pre>
                </div>

                <div class="card p-6 rounded-xl hover:shadow-xl hover:shadow-orange-500/10">
                    <h3 class="font-semibold text-white mb-3 flex items-center">
                        <span class="text-orange-500 mr-2 text-2xl">🔌</span> Database connection refused
                    </h3>
                    <ul class="text-sm text-gray-300 space-y-2">
                        <li class="flex items-start">
                            <span class="text-indigo-400 mr-2">•</span>
                            <span>Check container: <code class="bg-slate-800 px-2 py-1 rounded text-xs">docker-compose ps</code></span>
                        </li>
                        <li class="flex items-start">
                            <span class="text-indigo-400 mr-2">•</span>
                            <span>Verify .env DB_HOST is "db" (not 127.0.0.1)</span>
                        </li>
                        <li class="flex items-start">
                            <span class="text-indigo-400 mr-2">•</span>
                            <span>Wait 10-20s after starting for MySQL init</span>
                        </li>
                    </ul>
                </div>
            </div>
        </section>

    </main>

    <footer class="glass-effect border-t border-indigo-500/20 py-8">
        <div class="container mx-auto px-6 text-center">
            <div class="mb-4">
                <div class="inline-flex items-center space-x-2 text-indigo-400">
                    <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M10.894 2.553a1 1 0 00-1.788 0l-7 14a1 1 0 001.169 1.409l5-1.429A1 1 0 009 15.571V11a1 1 0 112 0v4.571a1 1 0 00.725.962l5 1.428a1 1 0 001.17-1.408l-7-14z"/>
                    </svg>
                    <span class="text-lg font-semibold">Task Management System API</span>
                </div>
            </div>
            <p class="text-sm text-gray-400">Technical Assessment Project</p>
            <p class="text-xs text-gray-500 mt-2">Last Updated: February 2026</p>
        </div>
    </footer>

    <script>
        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) entry.target.classList.add('visible');
            });
        }, { threshold: 0.1, rootMargin: '0px 0px -100px 0px' });

        document.querySelectorAll('.section').forEach(section => observer.observe(section));

        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                e.preventDefault();
                const target = document.querySelector(this.getAttribute('href'));
                if (target) {
                    window.scrollTo({
                        top: target.getBoundingClientRect().top + window.pageYOffset - 80,
                        behavior: 'smooth'
                    });
                }
            });
        });

        document.querySelectorAll('.code-block').forEach(block => {
            const button = document.createElement('button');
            button.innerHTML = '📋';
            button.className = 'absolute top-2 right-2 px-3 py-1 bg-indigo-600 hover:bg-indigo-500 rounded text-white text-xs transition-all opacity-70 hover:opacity-100';
            button.onclick = () => {
                navigator.clipboard.writeText(block.textContent);
                button.innerHTML = '✅';
                setTimeout(() => button.innerHTML = '📋', 2000);
            };
            block.appendChild(button);
        });
    </script>
</body>
</html>
