<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Analyst Saferwealth</title>
    
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700&display=swap" rel="stylesheet" />
    
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body { font-family: 'Inter', sans-serif; }
    </style>
</head>
<body class="bg-gradient-to-br from-blue-50 via-white to-indigo-50">
    <!-- Navigation -->
    <nav class="fixed w-full bg-white/80 backdrop-blur-md shadow-sm z-50">
        <div class="max-w-7xl mx-auto px-6 py-4">
            <div class="flex justify-between items-center">
                <div class="flex items-center space-x-3">
                    <div class="w-10 h-10 bg-gradient-to-br from-blue-600 to-indigo-600 rounded-lg flex items-center justify-center">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                    </div>
                    <span class="text-xl font-bold bg-gradient-to-r from-blue-600 to-indigo-600 bg-clip-text text-transparent">Analyst Saferwealth</span>
                </div>
                
                @auth
                    <a href="{{ auth()->user()->isAdmin() ? route('admin.dashboard') : route('dashboard') }}" 
                       class="px-6 py-2.5 bg-gradient-to-r from-blue-600 to-indigo-600 text-white rounded-lg font-medium hover:shadow-lg transition-all duration-200">
                        Dashboard
                    </a>
                @else
                    <div class="flex items-center space-x-4">
                        <a href="{{ route('login') }}" class="text-gray-700 hover:text-blue-600 font-medium transition">Login</a>
                        @if (Route::has('register'))
                            <a href="{{ route('register') }}" class="px-6 py-2.5 bg-gradient-to-r from-blue-600 to-indigo-600 text-white rounded-lg font-medium hover:shadow-lg transition-all duration-200">
                                Get Started
                            </a>
                        @endif
                    </div>
                @endauth
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="pt-32 pb-20 px-6">
        <div class="max-w-7xl mx-auto">
            <div class="text-center max-w-4xl mx-auto">
                <div class="inline-flex items-center px-4 py-2 bg-blue-100 rounded-full mb-6">
                    <svg class="w-5 h-5 text-blue-600 mr-2" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M2.166 4.999A11.954 11.954 0 0010 1.944 11.954 11.954 0 0017.834 5c.11.65.166 1.32.166 2.001 0 5.225-3.34 9.67-8 11.317C5.34 16.67 2 12.225 2 7c0-.682.057-1.35.166-2.001zm11.541 3.708a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                    </svg>
                    <span class="text-sm font-medium text-blue-600">Secure & Compliant</span>
                </div>
                
                <h1 class="text-5xl md:text-6xl font-bold text-gray-900 mb-6 leading-tight">
                    Secure AI Document<br/>
                    <span class="bg-gradient-to-r from-blue-600 to-indigo-600 bg-clip-text text-transparent">Intake & Analysis</span>
                </h1>
                
                <p class="text-xl text-gray-600 mb-10 max-w-2xl mx-auto">
                    Enterprise-grade document management with AWS-powered AI analysis. Automated workflows, intelligent processing, and zero manual follow-ups.
                </p>
                
                @guest
                    <div class="flex flex-col sm:flex-row gap-4 justify-center">
                        <a href="{{ route('register') }}" class="px-8 py-4 bg-gradient-to-r from-blue-600 to-indigo-600 text-white rounded-lg font-semibold hover:shadow-xl transition-all duration-200 transform hover:-translate-y-1">
                            Start Free Trial
                        </a>
                        <a href="{{ route('login') }}" class="px-8 py-4 bg-white text-gray-700 rounded-lg font-semibold border-2 border-gray-200 hover:border-blue-600 hover:text-blue-600 transition">
                            Sign In
                        </a>
                    </div>
                @endguest
            </div>
        </div>
    </section>

    <!-- Features Section -->
    <section class="py-20 px-6 bg-white">
        <div class="max-w-7xl mx-auto">
            <div class="text-center mb-16">
                <h2 class="text-3xl md:text-4xl font-bold text-gray-900 mb-4">
                    Everything You Need for Document Management
                </h2>
                <p class="text-lg text-gray-600 max-w-2xl mx-auto">
                    Built on AWS infrastructure with enterprise security and AI-powered automation
                </p>
            </div>
            
            <div class="grid md:grid-cols-3 gap-8">
                <!-- Feature 1 -->
                <div class="bg-gradient-to-br from-blue-50 to-indigo-50 rounded-2xl p-8 border border-blue-100 hover:shadow-xl transition-all duration-300">
                    <div class="w-14 h-14 bg-gradient-to-br from-blue-600 to-indigo-600 rounded-xl flex items-center justify-center mb-6">
                        <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-3">Secure Storage</h3>
                    <p class="text-gray-600 leading-relaxed">AWS S3 with KMS encryption, private buckets, and IAM role-based access control. Your documents are protected at rest and in transit.</p>
                </div>

                <!-- Feature 2 -->
                <div class="bg-gradient-to-br from-purple-50 to-pink-50 rounded-2xl p-8 border border-purple-100 hover:shadow-xl transition-all duration-300">
                    <div class="w-14 h-14 bg-gradient-to-br from-purple-600 to-pink-600 rounded-xl flex items-center justify-center mb-6">
                        <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"></path>
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-3">AI Analysis</h3>
                    <p class="text-gray-600 leading-relaxed">Powered by AWS Bedrock RAG. Query your documents with natural language and get instant, context-aware answers from your corpus.</p>
                </div>

                <!-- Feature 3 -->
                <div class="bg-gradient-to-br from-green-50 to-emerald-50 rounded-2xl p-8 border border-green-100 hover:shadow-xl transition-all duration-300">
                    <div class="w-14 h-14 bg-gradient-to-br from-green-600 to-emerald-600 rounded-xl flex items-center justify-center mb-6">
                        <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-3">Auto Reminders</h3>
                    <p class="text-gray-600 leading-relaxed">Zero manual follow-ups. Automated email reminders via SES for missing documents, corrections, and profile updates.</p>
                </div>

                <!-- Feature 4 -->
                <div class="bg-gradient-to-br from-orange-50 to-red-50 rounded-2xl p-8 border border-orange-100 hover:shadow-xl transition-all duration-300">
                    <div class="w-14 h-14 bg-gradient-to-br from-orange-600 to-red-600 rounded-xl flex items-center justify-center mb-6">
                        <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-3">Auto Workflows</h3>
                    <p class="text-gray-600 leading-relaxed">Event-driven architecture with Laravel queues. Automatic status detection, versioning, and review workflows.</p>
                </div>

                <!-- Feature 5 -->
                <div class="bg-gradient-to-br from-cyan-50 to-blue-50 rounded-2xl p-8 border border-cyan-100 hover:shadow-xl transition-all duration-300">
                    <div class="w-14 h-14 bg-gradient-to-br from-cyan-600 to-blue-600 rounded-xl flex items-center justify-center mb-6">
                        <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-3">Compliance Ready</h3>
                    <p class="text-gray-600 leading-relaxed">Full audit trails, CloudTrail integration, role-based access, and email verification. Built for enterprise compliance.</p>
                </div>

                <!-- Feature 6 -->
                <div class="bg-gradient-to-br from-yellow-50 to-orange-50 rounded-2xl p-8 border border-yellow-100 hover:shadow-xl transition-all duration-300">
                    <div class="w-14 h-14 bg-gradient-to-br from-yellow-600 to-orange-600 rounded-xl flex items-center justify-center mb-6">
                        <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-3">Fast & Scalable</h3>
                    <p class="text-gray-600 leading-relaxed">Laravel 11 backend with SQS queues. Scales effortlessly from MVP to thousands of users on AWS infrastructure.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- How It Works Section -->
    <section class="py-20 px-6">
        <div class="max-w-7xl mx-auto">
            <div class="text-center mb-16">
                <h2 class="text-3xl md:text-4xl font-bold text-gray-900 mb-4">How It Works</h2>
                <p class="text-lg text-gray-600 max-w-2xl mx-auto">
                    A seamless workflow from document submission to intelligent analysis
                </p>
            </div>

            <div class="grid md:grid-cols-4 gap-6">
                <!-- Step 1 -->
                <div class="relative">
                    <div class="flex flex-col items-center">
                        <div class="w-16 h-16 bg-gradient-to-br from-blue-600 to-indigo-600 rounded-full flex items-center justify-center text-white text-2xl font-bold mb-4">1</div>
                        <h3 class="text-lg font-semibold text-gray-900 mb-2 text-center">Upload Documents</h3>
                        <p class="text-gray-600 text-sm text-center">Users submit documents through the secure portal in supported formats</p>
                    </div>
                    @if ($loop ?? false && !$loop->last)
                        <div class="hidden md:block absolute top-8 left-1/2 w-24 h-1 bg-gradient-to-r from-blue-600 to-transparent"></div>
                    @endif
                </div>

                <!-- Step 2 -->
                <div class="relative">
                    <div class="flex flex-col items-center">
                        <div class="w-16 h-16 bg-gradient-to-br from-purple-600 to-pink-600 rounded-full flex items-center justify-center text-white text-2xl font-bold mb-4">2</div>
                        <h3 class="text-lg font-semibold text-gray-900 mb-2 text-center">AI Analysis</h3>
                        <p class="text-gray-600 text-sm text-center">Bedrock RAG analyzes documents for completeness and accuracy</p>
                    </div>
                </div>

                <!-- Step 3 -->
                <div class="relative">
                    <div class="flex flex-col items-center">
                        <div class="w-16 h-16 bg-gradient-to-br from-green-600 to-emerald-600 rounded-full flex items-center justify-center text-white text-2xl font-bold mb-4">3</div>
                        <h3 class="text-lg font-semibold text-gray-900 mb-2 text-center">Admin Review</h3>
                        <p class="text-gray-600 text-sm text-center">Flag issues, request corrections, or approve documents</p>
                    </div>
                </div>

                <!-- Step 4 -->
                <div class="flex flex-col items-center">
                    <div class="w-16 h-16 bg-gradient-to-br from-orange-600 to-red-600 rounded-full flex items-center justify-center text-white text-2xl font-bold mb-4">4</div>
                    <h3 class="text-lg font-semibold text-gray-900 mb-2 text-center">User Notified</h3>
                    <p class="text-gray-600 text-sm text-center">Automated emails with next steps or approval confirmation</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Use Cases Section -->
    <section class="py-20 px-6 bg-white">
        <div class="max-w-7xl mx-auto">
            <div class="text-center mb-16">
                <h2 class="text-3xl md:text-4xl font-bold text-gray-900 mb-4">Perfect For</h2>
                <p class="text-lg text-gray-600 max-w-2xl mx-auto">
                    Designed for organizations handling document-heavy workflows
                </p>
            </div>

            <div class="grid md:grid-cols-3 gap-8">
                <!-- Use Case 1 -->
                <div class="bg-gradient-to-br from-blue-50 to-cyan-50 rounded-2xl p-8 border border-blue-100">
                    <div class="text-4xl mb-4">🏦</div>
                    <h3 class="text-xl font-bold text-gray-900 mb-3">Financial Services</h3>
                    <p class="text-gray-600 mb-4">KYC/AML verification, loan applications, income verification, identity documents</p>
                    <ul class="text-sm text-gray-600 space-y-2">
                        <li>✓ Automated document verification</li>
                        <li>✓ Fraud detection capabilities</li>
                        <li>✓ Audit trail compliance</li>
                    </ul>
                </div>

                <!-- Use Case 2 -->
                <div class="bg-gradient-to-br from-purple-50 to-pink-50 rounded-2xl p-8 border border-purple-100">
                    <div class="text-4xl mb-4">🏥</div>
                    <h3 class="text-xl font-bold text-gray-900 mb-3">Healthcare</h3>
                    <p class="text-gray-600 mb-4">Patient intake forms, insurance documents, medical records, license verification</p>
                    <ul class="text-sm text-gray-600 space-y-2">
                        <li>✓ HIPAA-compliant storage</li>
                        <li>✓ Secure document handling</li>
                        <li>✓ Automated verification</li>
                    </ul>
                </div>

                <!-- Use Case 3 -->
                <div class="bg-gradient-to-br from-green-50 to-emerald-50 rounded-2xl p-8 border border-green-100">
                    <div class="text-4xl mb-4">📋</div>
                    <h3 class="text-xl font-bold text-gray-900 mb-3">Government & Compliance</h3>
                    <p class="text-gray-600 mb-4">Permit applications, certification processing, background checks, benefits verification</p>
                    <ul class="text-sm text-gray-600 space-y-2">
                        <li>✓ Complete audit trails</li>
                        <li>✓ Regulatory compliance</li>
                        <li>✓ Document versioning</li>
                    </ul>
                </div>
            </div>
        </div>
    </section>

    <!-- Tech Stack Section -->
    <section class="py-20 px-6">
        <div class="max-w-7xl mx-auto">
            <div class="text-center mb-16">
                <h2 class="text-3xl md:text-4xl font-bold text-gray-900 mb-4">Built with Modern Technology</h2>
                <p class="text-lg text-gray-600 max-w-2xl mx-auto">
                    Enterprise-grade technology stack for reliability and scalability
                </p>
            </div>

            <div class="grid md:grid-cols-4 gap-6">
                <!-- Backend -->
                <div class="bg-white rounded-2xl p-6 border border-gray-200 hover:shadow-lg transition-all">
                    <div class="w-12 h-12 bg-red-100 rounded-lg flex items-center justify-center mb-4">
                        <svg class="w-6 h-6 text-red-600" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M23.48 2.52L12 20.04 0 2.52h4.44l7.56 11.76 7.56-11.76h4.44zm-2.04 0h-3.36l-7.56 11.88L3.36 2.52H0l11.04 17.52 11.04-17.52h-1.2z"></path>
                        </svg>
                    </div>
                    <h3 class="font-semibold text-gray-900 mb-2">Laravel 11</h3>
                    <p class="text-sm text-gray-600">PHP framework with elegant syntax and powerful features for rapid development</p>
                </div>

                <!-- Database -->
                <div class="bg-white rounded-2xl p-6 border border-gray-200 hover:shadow-lg transition-all">
                    <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center mb-4">
                        <svg class="w-6 h-6 text-blue-600" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm0 18c-4.41 0-8-3.59-8-8s3.59-8 8-8 8 3.59 8 8-3.59 8-8 8zm3.5-9c.83 0 1.5-.67 1.5-1.5S16.33 8 15.5 8 14 8.67 14 9.5s.67 1.5 1.5 1.5zm-7 0c.83 0 1.5-.67 1.5-1.5S9.33 8 8.5 8 7 8.67 7 9.5 7.67 11 8.5 11zm3.5 6.5c2.33 0 4.31-1.46 5.11-3.5H6.89c.8 2.04 2.78 3.5 5.11 3.5z"></path>
                        </svg>
                    </div>
                    <h3 class="font-semibold text-gray-900 mb-2">AWS RDS</h3>
                    <p class="text-sm text-gray-600">Managed relational database with automated backups and high availability</p>
                </div>

                <!-- Storage -->
                <div class="bg-white rounded-2xl p-6 border border-gray-200 hover:shadow-lg transition-all">
                    <div class="w-12 h-12 bg-orange-100 rounded-lg flex items-center justify-center mb-4">
                        <svg class="w-6 h-6 text-orange-600" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M21 19V5c0-1.1-.9-2-2-2H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2zM8.5 13.5l2.5 3.01L14.5 12l4.5 6H5l3.5-4.5z"></path>
                        </svg>
                    </div>
                    <h3 class="font-semibold text-gray-900 mb-2">AWS S3</h3>
                    <p class="text-sm text-gray-600">Secure, scalable cloud storage with encryption and compliance features</p>
                </div>

                <!-- AI -->
                <div class="bg-white rounded-2xl p-6 border border-gray-200 hover:shadow-lg transition-all">
                    <div class="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center mb-4">
                        <svg class="w-6 h-6 text-purple-600" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z"></path>
                        </svg>
                    </div>
                    <h3 class="font-semibold text-gray-900 mb-2">AWS Bedrock</h3>
                    <p class="text-sm text-gray-600">Claude AI with Retrieval-Augmented Generation for intelligent analysis</p>
                </div>
            </div>
        </div>
    </section>

    <!-- CTA Section -->
    <section class="py-20 px-6 bg-gradient-to-br from-blue-600 to-indigo-600">
        <div class="max-w-4xl mx-auto text-center text-white">
            <h2 class="text-3xl md:text-4xl font-bold mb-6">Ready to Transform Your Document Workflow?</h2>
            <p class="text-xl mb-8 text-blue-100">
                Join organizations saving time and reducing errors with AI-powered document management
            </p>
            @guest
                <div class="flex flex-col sm:flex-row gap-4 justify-center">
                    <a href="{{ route('register') }}" class="px-8 py-4 bg-white text-blue-600 rounded-lg font-semibold hover:bg-blue-50 transition-all duration-200 transform hover:-translate-y-1">
                        Start Your Free Trial
                    </a>
                    <a href="{{ route('login') }}" class="px-8 py-4 bg-blue-700 text-white rounded-lg font-semibold border-2 border-white hover:bg-blue-800 transition">
                        Sign In to Account
                    </a>
                </div>
            @else
                <a href="{{ auth()->user()->isAdmin() ? route('admin.dashboard') : route('dashboard') }}" class="inline-block px-8 py-4 bg-white text-blue-600 rounded-lg font-semibold hover:bg-blue-50 transition-all duration-200 transform hover:-translate-y-1">
                    Go to Dashboard
                </a>
            @endguest
            <p class="text-sm text-blue-200 mt-8">No credit card required · Enterprise support available</p>
        </div>
    </section>

    <!-- Features Grid Section -->
    <section class="py-20 px-6 bg-white">
        <div class="max-w-7xl mx-auto">
            <div class="text-center mb-16">
                <h2 class="text-3xl md:text-4xl font-bold text-gray-900 mb-4">Admin Control Features</h2>
                <p class="text-lg text-gray-600 max-w-2xl mx-auto">
                    Powerful tools for document verification and compliance tracking
                </p>
            </div>

            <div class="grid md:grid-cols-3 gap-8">
                <!-- Feature -->
                <div class="bg-gradient-to-br from-red-50 to-pink-50 rounded-2xl p-8 border border-red-100">
                    <div class="text-4xl mb-4">🚩</div>
                    <h3 class="text-xl font-bold text-gray-900 mb-3">Flag Issues</h3>
                    <p class="text-gray-600">Mark documents with specific issues - fraud, quality, missing fields - with severity levels and full tracking</p>
                </div>

                <!-- Feature -->
                <div class="bg-gradient-to-br from-yellow-50 to-orange-50 rounded-2xl p-8 border border-yellow-100">
                    <div class="text-4xl mb-4">📤</div>
                    <h3 class="text-xl font-bold text-gray-900 mb-3">Request Re-uploads</h3>
                    <p class="text-gray-600">Request improved versions with custom instructions and deadlines. Track submissions and auto-notify users</p>
                </div>

                <!-- Feature -->
                <div class="bg-gradient-to-br from-cyan-50 to-blue-50 rounded-2xl p-8 border border-cyan-100">
                    <div class="text-4xl mb-4">📋</div>
                    <h3 class="text-xl font-bold text-gray-900 mb-3">Track History</h3>
                    <p class="text-gray-600">Complete immutable audit trail of every action - uploads, reviews, flags, corrections. Full compliance ready</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-gray-900 text-gray-400 py-12 px-6">
        <div class="max-w-7xl mx-auto text-center">
            <div class="flex items-center justify-center space-x-3 mb-4">
                <div class="w-8 h-8 bg-gradient-to-br from-blue-600 to-indigo-600 rounded-lg flex items-center justify-center">
                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                </div>
                <span class="text-lg font-bold text-white">Analyst Saferwealth</span>
            </div>
            <p class="text-sm mb-4">Secure AI Document Intake & Analysis Platform</p>
            <p class="text-xs">Built with Laravel 11 · AWS S3 · AWS Bedrock · Secure by Design</p>
            <p class="text-xs mt-2">© 2026 Analyst Saferwealth. All rights reserved.</p>
        </div>
    </footer>
</body>
</html>
