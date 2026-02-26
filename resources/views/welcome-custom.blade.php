<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Analyst Saferwealth</title>
    
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700&display=swap" rel="stylesheet" />
    
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <style>
        :root {
            --background: 40 25% 98%;
            --foreground: 175 35% 25%;
            --card: 0 0% 100%;
            --card-foreground: 175 35% 25%;
            --popover: 0 0% 100%;
            --popover-foreground: 175 35% 25%;
            --primary: 175 45% 32%;
            --primary-foreground: 0 0% 100%;
            --secondary: 170 35% 75%;
            --secondary-foreground: 175 45% 22%;
            --muted: 170 15% 94%;
            --muted-foreground: 175 20% 45%;
            --accent: 175 50% 40%;
            --accent-foreground: 0 0% 100%;
            --destructive: 0 84% 60%;
            --destructive-foreground: 0 0% 100%;
            --border: 170 20% 88%;
            --input: 170 20% 88%;
            --ring: 175 45% 32%;
            --radius: .5rem;
            --hero-gradient: linear-gradient(135deg, hsl(170 40% 75%) 0%, hsl(175 45% 85%) 100%);
            --card-shadow: 0 4px 24px -4px hsl(175 30% 25% / .08);
            --card-shadow-hover: 0 12px 40px -8px hsl(175 30% 25% / .15);
            --glow-teal: 0 0 40px -10px hsl(175 45% 40% / .35);
            --font-display: "Cormorant Garamond", Georgia, serif;
            --font-body: "Inter", system-ui, sans-serif;
            --sidebar-background: 0 0% 98%;
            --sidebar-foreground: 240 5.3% 26.1%;
            --sidebar-primary: 240 5.9% 10%;
            --sidebar-primary-foreground: 0 0% 98%;
            --sidebar-accent: 240 4.8% 95.9%;
            --sidebar-accent-foreground: 240 5.9% 10%;
            --sidebar-border: 220 13% 91%;
            --sidebar-ring: 175 45% 32%;
        }
        body {
            font-family: var(--font-body, 'Inter', sans-serif);
            background: hsl(var(--background));
            color: hsl(var(--foreground));
        }
        .hero-gradient {
    background: var(--hero-gradient);
}
.text-foreground {
    color: hsl(var(--foreground));
}
.text-gradient
 {
    -webkit-text-fill-color: transparent;
    background: linear-gradient(135deg, rgb(49, 129, 123), rgb(57, 172, 153)) text;
}
.text-foreground\/90 {
    color: hsl(var(--foreground) / 0.9);
}
.bg-primary\/10 {
    background-color: hsl(var(--primary) / 0.1);
}
.text-foreground\/80 {
    color: hsl(var(--foreground) / 0.8);
}
.bg-primary {
    background-color: hsl(var(--primary));
}
.text-muted-foreground {
    color: #5c8a86;
}
.bg-secondary\/20 {
    background-color: hsl(var(--secondary) / 0.2);
}
.text-gradient {
    background: linear-gradient(135deg, #31817b, #39ac99);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
}
        .sticky-header {
            transition: background 0.3s, box-shadow 0.3s, border-bottom 0.3s;
            background: transparent;
        }
        .sticky-header.scrolled {
            background: #fff;
            box-shadow: 0 2px 8px 0 rgba(16, 42, 37, 0.04);
            border-bottom: 1px solid #e0ecea;
            backdrop-filter: blur(8px);
        }
        .from-primary {
    --tw-gradient-from: hsl(var(--primary)) var(--tw-gradient-from-position);
    --tw-gradient-to: hsl(var(--primary) / 0) var(--tw-gradient-to-position);
    --tw-gradient-stops: var(--tw-gradient-from), var(--tw-gradient-to);
}
.container {
    padding: 0 20px;
}
@media (max-width: 767px) {
.container {
    padding: 0 10px;
}
}
    </style>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var header = document.querySelector('.sticky-header');
            function onScroll() {
                if (window.scrollY > 0) {
                    header.classList.add('scrolled');
                } else {
                    header.classList.remove('scrolled');
                }
            }
            window.addEventListener('scroll', onScroll);
            onScroll();
        });
    </script>
</head>
<body class="bg-gradient-to-br from-blue-50 via-white to-indigo-50 bk-body">
        <!-- Header Section (Blade version of React Header) -->
        <header class="sticky-header fixed top-0 left-0 right-0 z-50 transition-all duration-300" x-data="{ isMobileMenuOpen: false, isScrolled: false }" x-init="window.addEventListener('scroll', () => { isScrolled = window.scrollY > 10 })">
                    <div class="container mx-auto px-6">
                        <div class="flex items-center justify-between h-16 lg:h-20">
                            <!-- Logo -->
                            <a href="/" class="flex items-center gap-2">
                                <div class="font-display text-xl font-bold transition-colors" :class="isScrolled ? 'text-gray-900' : 'text-gray-900'">
                                    SaferWealth
                                </div>
                            </a>

                            <!-- Desktop Nav -->
                            <nav class="hidden md:flex items-center gap-8">
                                <a href="#how-it-works" class="text-sm font-medium transition-colors hover:opacity-80" :class="isScrolled ? 'text-gray-900' : 'text-gray-700'">How It Works</a>
                                <a href="#benefits" class="text-sm font-medium transition-colors hover:opacity-80" :class="isScrolled ? 'text-gray-900' : 'text-gray-700'">Benefits</a>
                                <a href="#faq" class="text-sm font-medium transition-colors hover:opacity-80" :class="isScrolled ? 'text-gray-900' : 'text-gray-700'">FAQ</a>
                            </nav>

                            <!-- CTA Desktop -->
                            <div class="hidden md:flex items-center gap-3">
                                @auth
                                    <a href="{{ route('dashboard') }}" class="px-4 py-2.5 border border-[#2d7670]/30 text-[#2d7670] rounded-lg font-medium hover:bg-[#2d7670]/5 transition-all duration-200">Go to Dashboard</a>
                                @else
                                    @if (Route::has('login'))
                                        <a href="{{ route('login') }}" class="px-4 py-2.5 border border-[#2d7670]/30 text-[#2d7670] rounded-lg font-medium hover:bg-[#2d7670]/5 transition-all duration-200">Log in</a>
                                    @endif
                                    @if (Route::has('register'))
                                        <a href="{{ route('register') }}" class="px-4 py-2.5 border border-[#2d7670]/30 text-[#2d7670] rounded-lg font-medium hover:bg-[#2d7670]/5 transition-all duration-200">Sign up</a>
                                    @endif
                                @endauth
                                @auth
                                    <a href="{{ route('dashboard') }}" class="px-6 py-2.5 bg-primary text-white rounded-lg font-medium hover:opacity-90 transition-all duration-200">Check Eligibility</a>
                                @else
                                    <a href="{{ route('login') }}" class="px-6 py-2.5 bg-primary text-white rounded-lg font-medium hover:opacity-90 transition-all duration-200">Check Eligibility</a>
                                @endauth
                            </div>

                            <!-- Mobile Menu Toggle -->
                            <button class="md:hidden p-2" @click="isMobileMenuOpen = !isMobileMenuOpen" aria-label="Toggle mobile menu">
                                <span x-show="!isMobileMenuOpen">
                                    <svg class="w-6 h-6 text-gray-900" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" /></svg>
                                </span>
                                <span x-show="isMobileMenuOpen">
                                    <svg class="w-6 h-6 text-gray-900" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                                </span>
                            </button>
                        </div>
                    </div>

                    <!-- Mobile Menu -->
                    <div x-show="isMobileMenuOpen" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 h-0" x-transition:enter-end="opacity-100 h-auto" x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100 h-auto" x-transition:leave-end="opacity-0 h-0" class="md:hidden bg-white border-b border-gray-200 overflow-hidden">
                        <nav class="container mx-auto py-6 flex flex-col gap-4">
                            <a href="#how-it-works" class="text-gray-900 font-medium py-2" @click="isMobileMenuOpen = false">How It Works</a>
                            <a href="#benefits" class="text-gray-900 font-medium py-2" @click="isMobileMenuOpen = false">Benefits</a>
                            <a href="#faq" class="text-gray-900 font-medium py-2" @click="isMobileMenuOpen = false">FAQ</a>
                            @auth
                                <a href="{{ route('dashboard') }}" class="mt-2 px-6 py-3 border border-[#2d7670]/30 text-[#2d7670] rounded-lg font-semibold text-center" @click="isMobileMenuOpen = false">Go to Dashboard</a>
                            @else
                                @if (Route::has('login'))
                                    <a href="{{ route('login') }}" class="mt-2 px-6 py-3 border border-[#2d7670]/30 text-[#2d7670] rounded-lg font-semibold text-center" @click="isMobileMenuOpen = false">Log in</a>
                                @endif
                                @if (Route::has('register'))
                                    <a href="{{ route('register') }}" class="mt-2 px-6 py-3 border border-[#2d7670]/30 text-[#2d7670] rounded-lg font-semibold text-center" @click="isMobileMenuOpen = false">Sign up</a>
                                @endif
                            @endauth
                            @auth
                                <a href="{{ route('dashboard') }}" class="mt-2 px-6 py-3 bg-primary text-white rounded-lg font-semibold text-center" @click="isMobileMenuOpen = false">Check Eligibility</a>
                            @else
                                <a href="{{ route('login') }}" class="mt-2 px-6 py-3 bg-primary text-white rounded-lg font-semibold text-center" @click="isMobileMenuOpen = false">Check Eligibility</a>
                            @endauth
                        </nav>
                    </div>
        </header>

        <!-- Hero Section (Blade version of React Hero) -->
        <section class="hero-gradient relative overflow-hidden min-h-[90vh] flex items-center">
            <!-- Subtle pattern overlay -->
            <div class="absolute inset-0 opacity-5 pointer-events-none">
                <div class="absolute inset-0" style="background-image:radial-gradient(circle at 2px 2px, hsl(175 45% 32%) 1px, transparent 0);background-size:40px 40px;"></div>
            </div>
            <!-- Decorative elements -->
            <div class="absolute top-20 right-10 w-72 h-72 bg-primary/10 rounded-full blur-3xl pointer-events-none"></div>
            <div class="absolute bottom-20 left-10 w-96 h-96 bg-green-400/10 rounded-full blur-3xl pointer-events-none"></div>
            <div class="container relative z-10 py-20 lg:py-32" style="margin: 0 auto">
                <div class="max-w-4xl mx-auto text-center">
                    <!-- Trust badge -->
                    <div class="inline-flex items-center gap-2 bg-primary/10 backdrop-blur-sm rounded-full px-4 py-2 mb-8">
                        <!-- Shield icon -->
                        <svg class="w-4 h-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
                        <span class="text-sm text-gray/90">Trusted by Canadian Business Owners</span>
                    </div>
                    <!-- Heading -->
                    <h1 class="font-display text-4xl md:text-5xl lg:text-6xl font-bold leading-tight mb-6">
                        Find Out If Your Business <span class="text-gradient">Qualifies for Funding</span> in Days — Not Weeks
                    </h1>
                    <!-- Subheading -->
                    <p class="text-lg md:text-xl  text-foreground/80 max-w-2xl mx-auto mb-10">
                        Our secure platform analyzes your financial documents and gives you a clear answer on loan eligibility — so you can stop guessing and start planning.
                    </p>
                    <!-- CTA Buttons -->
                    <div class="flex flex-col sm:flex-row gap-4 justify-center mb-12">
                        @auth
                            <a href="{{ route('dashboard') }}" class="px-8 py-4 bg-white/90 border border-border text-foreground rounded-lg font-semibold text-lg flex items-center justify-center gap-2 hover:bg-white transition">Go to Dashboard</a>
                        @else
                            @if (Route::has('login'))
                                <a href="{{ route('login') }}" class="px-8 py-4 bg-white/90 border border-border text-foreground rounded-lg font-semibold text-lg flex items-center justify-center gap-2 hover:bg-white transition">Log in</a>
                            @endif
                            @if (Route::has('register'))
                                <a href="{{ route('register') }}" class="px-8 py-4 bg-white/90 border border-border text-foreground rounded-lg font-semibold text-lg flex items-center justify-center gap-2 hover:bg-white transition">Create Account</a>
                            @endif
                        @endauth
                        @auth
                            <a href="{{ route('dashboard') }}" class="group bg-primary px-8 py-4 text-white rounded-lg font-semibold text-lg flex items-center justify-center gap-2 hover:opacity-90 transition-all duration-200 transform hover:-translate-y-1">
                                Check My Eligibility Now
                                <svg class="w-5 h-5 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 12h14m-7-7l7 7-7 7"/></svg>
                            </a>
                        @else
                            <a href="{{ route('login') }}" class="group bg-primary px-8 py-4 text-white rounded-lg font-semibold text-lg flex items-center justify-center gap-2 hover:opacity-90 transition-all duration-200 transform hover:-translate-y-1">
                                Check My Eligibility Now
                                <svg class="w-5 h-5 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 12h14m-7-7l7 7-7 7"/></svg>
                            </a>
                        @endauth
                        <a href="#how-it-works" class="px-8 py-4 bg-white/90 border border-border text-foreground rounded-lg font-semibold text-lg flex items-center justify-center gap-2 hover:bg-white transition">See How It Works</a>
                    </div>
                    <!-- Features List -->
                    <div class="flex flex-wrap justify-center gap-6 text-gray-700/70 text-sm">
                        <div class="flex items-center gap-2">
                            <svg class="w-4 h-4 text-[#2d7670]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4"/></svg>
                            <span>No credit check required</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <svg class="w-4 h-4 text-[#2d7670]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6l4 2"/></svg>
                            <span>Results in 48 hours</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <svg class="w-4 h-4 text-[#2d7670]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
                            <span>Bank-level security</span>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Pain Points Section -->
        <section class="py-20 lg:py-28 bg-gray-100">
            <div class="container mx-auto">
                <div class="text-center max-w-3xl mx-auto mb-16">
                    <h2 class="font-display text-3xl md:text-4xl lg:text-5xl font-bold text-[#2d7670] mb-6">
                        We Know What You're Going Through
                    </h2>
                    <p class="text-lg text-[#2d7670]">
                        Getting business funding shouldn't feel like a battle. But for too many Canadian entrepreneurs, that's exactly what it is.
                    </p>
                </div>
                <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-6">
                    <!-- Pain Point 1 -->
                    <div class="bg-white rounded-xl p-6 shadow border border-gray-200">
                        <div class="w-12 h-12 rounded-lg bg-red-100 flex items-center justify-center mb-4">
                            <!-- Clock icon -->
                            <svg class="w-6 h-6 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6l4 2"/></svg>
                        </div>
                        <h3 class="font-display text-xl font-semibold text-foreground mb-2">Weeks of Waiting</h3>
                        <p class="text-muted-foreground">Banks take forever to respond. You're left in limbo while your business needs capital now.</p>
                    </div>
                    <!-- Pain Point 2 -->
                    <div class="bg-white rounded-xl p-6 shadow border border-gray-200">
                        <div class="w-12 h-12 rounded-lg bg-red-100 flex items-center justify-center mb-4">
                            <!-- FileQuestion icon -->
                            <svg class="w-6 h-6 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v.01"/></svg>
                        </div>
                        <h3 class="font-display text-xl font-semibold text-foreground mb-2">Endless Paperwork</h3>
                        <p class="text-muted-foreground">The same documents requested over and over. Tax returns, bank statements, projections — again and again.</p>
                    </div>
                    <!-- Pain Point 3 -->
                    <div class="bg-white rounded-xl p-6 shadow border border-gray-200">
                        <div class="w-12 h-12 rounded-lg bg-red-100 flex items-center justify-center mb-4">
                            <!-- XCircle icon -->
                            <svg class="w-6 h-6 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                        </div>
                        <h3 class="font-display text-xl font-semibold text-foreground mb-2">No Clear Answer</h3>
                        <p class="text-muted-foreground">You don't know if you qualify until you've already invested weeks of effort.</p>
                    </div>
                    <!-- Pain Point 4 -->
                    <div class="bg-white rounded-xl p-6 shadow border border-gray-200">
                        <div class="w-12 h-12 rounded-lg bg-red-100 flex items-center justify-center mb-4">
                            <!-- AlertCircle icon -->
                            <svg class="w-6 h-6 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01"/></svg>
                        </div>
                        <h3 class="font-display text-xl font-semibold text-foreground mb-2">Fear of Rejection</h3>
                        <p class="text-muted-foreground">The anxiety of not knowing eats away at you. What if all this work is for nothing?</p>
                    </div>
                    <!-- Pain Point 5 -->
                    <div class="bg-white rounded-xl p-6 shadow border border-gray-200">
                        <div class="w-12 h-12 rounded-lg bg-red-100 flex items-center justify-center mb-4">
                            <!-- TrendingDown icon -->
                            <svg class="w-6 h-6 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 7v6a2 2 0 01-2 2H7l-4 4"/></svg>
                        </div>
                        <h3 class="font-display text-xl font-semibold text-foreground mb-2">Missed Opportunities</h3>
                        <p class="text-muted-foreground">While you wait, that perfect location, equipment, or expansion opportunity slips away.</p>
                    </div>
                    <!-- Pain Point 6 -->
                    <div class="bg-white rounded-xl p-6 shadow border border-gray-200">
                        <div class="w-12 h-12 rounded-lg bg-red-100 flex items-center justify-center mb-4">
                            <!-- Frown icon -->
                            <svg class="w-6 h-6 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 15s1.5-2 4-2 4 2 4 2"/></svg>
                        </div>
                        <h3 class="font-display text-xl font-semibold text-foreground mb-2">Stress & Uncertainty</h3>
                        <p class="text-muted-foreground">Cash flow pressure builds. Every day without an answer feels like a step backward.</p>
                    </div>
                                </div>
                <p class="text-center text-lg text-foreground font-medium mt-12">
                    Sound familiar? There's a better way.
                </p>
            </div>
        </section>

        <!-- Traditional Problems Section -->
        <section class="py-20 lg:py-28 bg-white">
            <div class="container mx-auto">
                <div class="grid lg:grid-cols-2 gap-12 lg:gap-20 items-center">
                    <div>
                        <h2 class="font-display text-3xl md:text-4xl lg:text-5xl font-bold text-foreground mb-6">
                            Why Traditional Banks Leave You Waiting
                        </h2>
                        <p class="text-lg text-muted-foreground mb-8">
                            The traditional lending process was built decades ago. It wasn't designed for today's fast-moving business world — or for entrepreneurs who need answers now.
                        </p>
                        <div class="space-y-4">
                            <div class="flex items-center gap-3">
                                <div class="w-6 h-6 rounded-full bg-red-100 flex items-center justify-center flex-shrink-0">
                                    <svg class="w-4 h-4 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                </div>
                                <span class="text-muted-foreground">Manual reviews that take weeks</span>
                            </div>
                            <div class="flex items-center gap-3">
                                <div class="w-6 h-6 rounded-full bg-red-100 flex items-center justify-center flex-shrink-0">
                                    <svg class="w-4 h-4 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v.01"/></svg>
                                </div>
                                <span class="text-muted-foreground">Documents requested multiple times</span>
                            </div>
                            <div class="flex items-center gap-3">
                                <div class="w-6 h-6 rounded-full bg-red-100 flex items-center justify-center flex-shrink-0">
                                    <svg class="w-4 h-4 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                </div>
                                <span class="text-muted-foreground">No explanation when rejected</span>
                            </div>
                            <div class="flex items-center gap-3">
                                <div class="w-6 h-6 rounded-full bg-red-100 flex items-center justify-center flex-shrink-0">
                                    <svg class="w-4 h-4 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                </div>
                                <span class="text-muted-foreground">One-size-fits-all approach</span>
                            </div>
                            <div class="flex items-center gap-3">
                                <div class="w-6 h-6 rounded-full bg-red-100 flex items-center justify-center flex-shrink-0">
                                    <svg class="w-4 h-4 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                </div>
                                <span class="text-muted-foreground">No pre-qualification process</span>
                            </div>
                            <div class="flex items-center gap-3">
                                <div class="w-6 h-6 rounded-full bg-red-100 flex items-center justify-center flex-shrink-0">
                                    <svg class="w-4 h-4 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                </div>
                                <span class="text-muted-foreground">Outdated, paper-heavy systems</span>
                            </div>
                        </div>
                    </div>
                    <div class="relative">
                        <div class="bg-gradient-to-br from-primary/10 to-white rounded-2xl p-8 lg:p-12 border border-primary/20">
                            <div class="space-y-6">
                                <div class="flex items-center gap-4">
                                    <div class="w-16 h-16 rounded-full bg-primary/20 flex items-center justify-center" >
                                        <svg class="w-8 h-8 text-[#2d7670]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 3v2.25M14.25 3v2.25M4.5 8.25h15M6.75 6h10.5A2.25 2.25 0 0119.5 8.25v9A2.25 2.25 0 0117.25 19.5H6.75A2.25 2.25 0 014.5 17.25v-9A2.25 2.25 0 016.75 6zM9 12.75l1.5 1.5 3.75-3.75"/></svg>
                                    </div>
                                    <div class="space-y-2 flex-1">
                                        <p class="text-sm font-semibold text-foreground">AI Document Review</p>
                                        <p class="text-xs text-muted-foreground">Secure automated analysis + eligibility checks</p>
                                    </div>
                                </div>
                                <div class="grid grid-cols-3 gap-2 text-center text-xs">
                                    <div class="bg-white/80 border border-border rounded-md py-2 text-foreground">Upload</div>
                                    <div class="bg-primary/15 border border-primary/30 rounded-md py-2 text-[#2d7670] font-medium">AI Analysis</div>
                                    <div class="bg-white/80 border border-border rounded-md py-2 text-foreground">Result</div>
                                </div>
                                <div class="flex items-center justify-center py-8">
                                    <div class="text-center">
                                        <div class="inline-flex items-center gap-2 text-[#2d7670]">
                                            <div class="w-2.5 h-2.5 rounded-full bg-[#2d7670] animate-pulse" ></div>
                                            <span class="text-sm font-medium">AI review in progress...</span>
                                        </div>
                                        <p class="text-xs text-muted-foreground mt-2">Estimated time: 24-48 hours</p>
                                    </div>
                                </div>
                                <div class="h-2 bg-white rounded-full overflow-hidden border border-primary/20">
                                    <div class="h-full w-2/3 bg-primary rounded-full" ></div>
                                </div>
                            </div>
                        </div>
                        <!-- Decorative label -->
                        <div class="absolute -top-3 -right-3 bg-primary text-white text-xs font-semibold px-3 py-1.5 rounded-full shadow">
                            AI Process
                        </div>
                    </div>
                </div>
            </div>
        </section>
                <!-- Solution Section -->
                <section class="py-20  lg:py-28 bg-primary text-white relative overflow-hidden">
                    <!-- Decorative elements -->
                    <div class="absolute top-0 right-0 w-96 h-96 bg-white/5 rounded-full blur-3xl pointer-events-none"></div>
                    <div class="absolute bottom-0 left-0 w-80 h-80 bg-green-200/10 rounded-full blur-3xl pointer-events-none"></div>
                    <div class="container bg-primary relative z-10" style="margin: 0 auto">
                        <div class="text-center max-w-3xl mx-auto mb-16">
                            <div class="inline-flex items-center gap-2 bg-white/10 rounded-full px-4 py-2 mb-6">
                                <!-- Zap icon -->
                                <svg class="w-4 h-4 bg-secondary/20" fill="none" stroke="currentColor" viewBox="0 0 24 24"><polygon points="13 2 3 14 12 14 11 22 21 10 12 10 13 2"/></svg>
                                <span class="text-sm">A Smarter Approach</span>
                            </div>
                            <h2 class="font-display text-3xl md:text-4xl lg:text-5xl font-bold mb-6">
                                The Modern Way to Know <span class="text-gradient">Where You Stand</span>
                            </h2>
                            <p class="text-lg text-white/80">
                                Our platform combines secure technology with intelligent analysis to give you clarity on your loan eligibility — quickly, privately, and accurately.
                            </p>
                        </div>
                        <div class="grid md:grid-cols-2 gap-6 lg:gap-8 mb-12">
                            <!-- Feature 1 -->
                            <div class="bg-white/5 backdrop-blur-sm border border-white/10 rounded-xl p-6 lg:p-8">
                                <div class="w-12 h-12 rounded-lg flex items-center justify-center mb-4">
                                    <!-- Lock icon -->
                                    <svg class="w-6 h-6 bg-secondary/20" fill="none" stroke="currentColor" viewBox="0 0 24 24"><rect x="3" y="11" width="18" height="11" rx="2"/><path d="M7 11V7a5 5 0 0110 0v4"/></svg>
                                </div>
                                <h3 class="font-display text-xl font-semibold mb-2">Secure Document Upload</h3>
                                <p class="text-white/70">Bank-level encryption protects your sensitive financial documents.</p>
                            </div>
                            <!-- Feature 2 -->
                            <div class="bg-white/5 backdrop-blur-sm border border-white/10 rounded-xl p-6 lg:p-8">
                                <div class="w-12 h-12 rounded-lg flex items-center justify-center mb-4">
                                    <!-- Zap icon -->
                                    <svg class="w-6 h-6 bg-secondary/20" fill="none" stroke="currentColor" viewBox="0 0 24 24"><polygon points="13 2 3 14 12 14 11 22 21 10 12 10 13 2"/></svg>
                                </div>
                                <h3 class="font-display text-xl font-semibold mb-2">AI-Assisted Analysis</h3>
                                <p class="text-white/70">Smart technology reviews your financials in hours, not weeks.</p>
                            </div>
                            <!-- Feature 3 -->
                            <div class="bg-white/5 backdrop-blur-sm border border-white/10 rounded-xl p-6 lg:p-8">
                                <div class="w-12 h-12 rounded-lg flex items-center justify-center mb-4">
                                    <!-- FileCheck icon -->
                                    <svg class="w-6 h-6 bg-secondary/20" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4"/><rect x="3" y="4" width="18" height="18" rx="2"/></svg>
                                </div>
                                <h3 class="font-display text-xl font-semibold mb-2">Missing Document Detection</h3>
                                <p class="text-white/70">We'll tell you exactly what's needed — no surprises later.</p>
                            </div>
                            <!-- Feature 4 -->
                            <div class="bg-white/5 backdrop-blur-sm border border-white/10 rounded-xl p-6 lg:p-8">
                                <div class="w-12 h-12 rounded-lg flex items-center justify-center mb-4">
                                    <!-- MessageCircle icon -->
                                    <svg class="w-6 h-6 bg-secondary/20" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M21 15a2 2 0 01-2 2H7l-4 4V5a2 2 0 012-2h14a2 2 0 012 2z"/></svg>
                                </div>
                                <h3 class="font-display text-xl font-semibold mb-2">Clear Eligibility Feedback</h3>
                                <p class="text-white/70">Get a straightforward answer on where you stand.</p>
                            </div>
                        </div>
                        <div class="text-center">
                            <div class="inline-flex items-center gap-2 text-white/70 text-sm mb-6">
                                <!-- Shield icon -->
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
                                <span>Your data is never shared publicly. Canadian-based service.</span>
                            </div>
                            <div>
                                <a href="#assessment" class="inline-flex items-center justify-center gap-2 whitespace-nowrap rounded-lg font-semibold ring-offset-background transition-all duration-200 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:pointer-events-none disabled:opacity-50 [&_svg]:pointer-events-none [&_svg]:size-4 [&_svg]:shrink-0 font-body bg-primary text-primary-foreground hover:bg-primary/90 glow-amber shadow-lg hover:shadow-xl transform hover:-translate-y-0.5 h-14 px-10 text-lg">
                                    Start My Free Assessment
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4"/></svg>
                                </a>
                            </div>
                        </div>
                    </div>
                </section>

               <!-- Benefits Section -->
                <section id="benefits" class="py-20 lg:py-28 bg-white scroll-mt-24">
                    <div class="container mx-auto">
                        <div class="text-center max-w-3xl mx-auto mb-16">
                            <h2 class="font-display text-3xl md:text-4xl lg:text-5xl font-bold text-foreground mb-6">What You'll Gain</h2>
                            <p class="text-lg text-muted-foreground">This isn't just about checking eligibility — it's about getting your time and confidence back.</p>
                        </div>
                        <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-8">
                            <!-- Benefit 1 -->
                            <div class="text-center">
                                <div class="w-16 h-16 rounded-2xl bg-gradient-to-br from-primary/20 to-primary/5 flex items-center justify-center mx-auto mb-4">
                                    <!-- Clock icon -->
                                    <svg class="w-7 h-7 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6l4 2"/></svg>
                                </div>
                                <h3 class="font-display text-xl font-semibold text-gray-900 mb-2">Know Where You Stand in Days</h3>
                                <p class="text-gray-600">No more weeks of uncertainty. Get clarity on your eligibility fast.</p>
                            </div>
                            <!-- Benefit 2 -->
                            <div class="text-center">
                                <div class="w-16 h-16 rounded-2xl bg-gradient-to-br from-primary/20 to-primary/5 flex items-center justify-center mx-auto mb-4">
                                    <!-- Target icon -->
                                    <svg class="w-7 h-7 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><circle cx="12" cy="12" r="6"/><circle cx="12" cy="12" r="2"/></svg>
                                </div>
                                <h3 class="font-display text-xl font-semibold text-gray-900 mb-2">Stop Guessing</h3>
                                <p class="text-gray-600">Clear feedback means you can make confident business decisions.</p>
                            </div>
                            <!-- Benefit 3 -->
                            <div class="text-center">
                                <div class="w-16 h-16 rounded-2xl bg-gradient-to-br from-primary/20 to-primary/5 flex items-center justify-center mx-auto mb-4">
                                    <!-- RefreshCw icon -->
                                    <svg class="w-7 h-7 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                                </div>
                                <h3 class="font-display text-xl font-semibold text-gray-900 mb-2">Save Weeks of Back-and-Forth</h3>
                                <p class="text-gray-600">We identify what's missing upfront — no repeated document requests.</p>
                            </div>
                            <!-- Benefit 4 -->
                            <div class="text-center">
                                <div class="w-16 h-16 rounded-2xl bg-gradient-to-br from-primary/20 to-primary/5 flex items-center justify-center mx-auto mb-4">
                                    <!-- FileX icon -->
                                    <svg class="w-7 h-7 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 6L6 18M6 6l12 12"/></svg>
                                </div>
                                <h3 class="font-display text-xl font-semibold text-gray-900 mb-2">Avoid Unnecessary Applications</h3>
                                <p class="text-gray-600">Only apply where you're likely to succeed.</p>
                            </div>
                            <!-- Benefit 5 -->
                            <div class="text-center">
                                <div class="w-16 h-16 rounded-2xl bg-gradient-to-br from-primary/20 to-primary/5 flex items-center justify-center mx-auto mb-4">
                                    <!-- ShieldCheck icon -->
                                    <svg class="w-7 h-7 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4"/></svg>
                                </div>
                                <h3 class="font-display text-xl font-semibold text-gray-900 mb-2">Prepare Stronger Applications</h3>
                                <p class="text-gray-600">Understand exactly what lenders are looking for.</p>
                            </div>
                            <!-- Benefit 6 -->
                            <div class="text-center">
                                <div class="w-16 h-16 rounded-2xl bg-gradient-to-br from-primary/20 to-primary/5 flex items-center justify-center mx-auto mb-4">
                                    <!-- Brain icon -->
                                    <svg class="w-7 h-7 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 7a5 5 0 00-10 0v10a5 5 0 0010 0V7z"/></svg>
                                </div>
                                <h3 class="font-display text-xl font-semibold text-gray-900 mb-2">Reduce Stress</h3>
                                <p class="text-gray-600">Replace uncertainty with a clear path forward.</p>
                            </div>
                        </div>
                        <div class="mt-16 text-center">
                            <div class="inline-flex items-center gap-3 bg-green-100 text-green-700 px-6 py-3 rounded-full">
                                <!-- ThumbsUp icon -->
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 9V5a3 3 0 00-6 0v4m0 0H5a2 2 0 00-2 2v7a2 2 0 002 2h14a2 2 0 002-2v-7a2 2 0 00-2-2h-3z"/></svg>
                                <span class="font-medium">Make confident business decisions</span>
                            </div>
                        </div>
                    </div>
                </section>
  
                <!-- Who Is It For Section -->
                <section class="py-20 lg:py-28 bg-gray-100">
                    <div class="container mx-auto">
                        <div class="text-center max-w-3xl mx-auto mb-16">
                            <h2 class="font-display text-3xl md:text-4xl lg:text-5xl font-bold text-foreground mb-6">Is This Right For You?</h2>
                            <p class="text-lg text-muted-foreground">Our eligibility assessment is designed for Canadian business owners and entrepreneurs at every stage.</p>
                        </div>
                        <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-6">
                            <!-- Audience 1 -->
                            <div class="bg-white rounded-xl p-6 shadow border border-gray-200 flex items-start gap-4">
                                <div class="w-12 h-12 rounded-lg bg-primary/10 flex items-center justify-center flex-shrink-0">
                                    <!-- Store icon -->
                                    <svg class="w-6 h-6 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24"><rect x="3" y="7" width="18" height="13" rx="2"/><path d="M16 3v4M8 3v4"/></svg>
                                </div>
                                <div>
                                    <h3 class="font-display text-lg font-semibold text-foreground mb-1">Franchise Buyers</h3>
                                    <p class="text-sm text-muted-foreground">Securing funding for your franchise opportunity</p>
                                </div>
                            </div>
                            <!-- Audience 2 -->
                            <div class="bg-white rounded-xl p-6 shadow border border-gray-200 flex items-start gap-4">
                                <div class="w-12 h-12 rounded-lg bg-primary/10 flex items-center justify-center flex-shrink-0">
                                    <!-- Building2 icon -->
                                    <svg class="w-6 h-6 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24"><rect x="3" y="7" width="18" height="13" rx="2"/><rect x="7" y="3" width="10" height="4" rx="2"/></svg>
                                </div>
                                <div>
                                    <h3 class="font-display text-lg font-semibold text-foreground mb-1">Existing Business Owners</h3>
                                    <p class="text-sm text-muted-foreground">Looking for growth capital or refinancing</p>
                                </div>
                            </div>
                            <!-- Audience 3 -->
                            <div class="bg-white rounded-xl p-6 shadow border border-gray-200 flex items-start gap-4">
                                <div class="w-12 h-12 rounded-lg bg-primary/10 flex items-center justify-center flex-shrink-0">
                                    <!-- Rocket icon -->
                                    <svg class="w-6 h-6 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M4 20l1.5-1.5M19.5 4.5L20 4m-7.5 7.5l7.5-7.5M4 20l7.5-7.5"/><circle cx="12" cy="12" r="2"/></svg>
                                </div>
                                <div>
                                    <h3 class="font-display text-lg font-semibold text-foreground mb-1">Entrepreneurs</h3>
                                    <p class="text-sm text-muted-foreground">Launching or scaling your business venture</p>
                                </div>
                            </div>
                            <!-- Audience 4 -->
                            <div class="bg-white rounded-xl p-6 shadow border border-gray-200 flex items-start gap-4">
                                <div class="w-12 h-12 rounded-lg bg-primary/10 flex items-center justify-center flex-shrink-0">
                                    <!-- TrendingUp icon -->
                                    <svg class="w-6 h-6 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M3 17l6-6 4 4 8-8"/></svg>
                                </div>
                                <div>
                                    <h3 class="font-display text-lg font-semibold text-foreground mb-1">Expanding Businesses</h3>
                                    <p class="text-sm text-muted-foreground">Ready to take your business to the next level</p>
                                </div>
                            </div>
                            <!-- Audience 5 -->
                            <div class="bg-white rounded-xl p-6 shadow border border-gray-200 flex items-start gap-4">
                                <div class="w-12 h-12 rounded-lg bg-primary/10 flex items-center justify-center flex-shrink-0">
                                    <!-- XCircle icon -->
                                    <svg class="w-6 h-6 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                </div>
                                <div>
                                    <h3 class="font-display text-lg font-semibold text-foreground mb-1">Bank-Declined Applicants</h3>
                                    <p class="text-sm text-muted-foreground">Turned down by traditional lenders</p>
                                </div>
                            </div>
                            <!-- Audience 6 -->
                            <div class="bg-white rounded-xl p-6 shadow border border-gray-200 flex items-start gap-4">
                                <div class="w-12 h-12 rounded-lg bg-primary/10 flex items-center justify-center flex-shrink-0">
                                    <!-- Users icon -->
                                    <svg class="w-6 h-6 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24"><circle cx="9" cy="7" r="4"/><circle cx="17" cy="17" r="4"/><path d="M17 13a4 4 0 00-8 0"/></svg>
                                </div>
                                <div>
                                    <h3 class="font-display text-lg font-semibold text-foreground mb-1">Business Buyers</h3>
                                    <p class="text-sm text-muted-foreground">Acquiring an existing business</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>
  
      <!-- How It Works Section -->
                <section id="how-it-works" class="py-20 lg:py-28 bg-white scroll-mt-24">
                    <div class="container mx-auto">
                        <div class="text-center max-w-3xl mx-auto mb-16">
                            <h2 class="font-display text-3xl md:text-4xl lg:text-5xl font-bold text-foreground mb-6">How It Works</h2>
                            <p class="text-lg text-muted-foreground">Four simple steps to clarity. No commitment required.</p>
                        </div>
                        <div class="max-w-4xl mx-auto">
                            <!-- Step 1 -->
                            <div class="relative">
                                <div class="flex gap-6 mb-8">
                                    <div class="flex-shrink-0">
                                        <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-primary to-primary/70 flex items-center justify-center shadow-lg">
                                            <!-- Upload icon -->
                                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M4 17v2a2 2 0 002 2h12a2 2 0 002-2v-2"/><polyline points="7 9 12 4 17 9"/><line x1="12" y1="4" x2="12" y2="16"/></svg>
                                        </div>
                                    </div>
                                    <div class="flex-1 pb-8 border-b border-gray-200">
                                        <div class="flex items-center gap-3 mb-2">
                                            <span class="text-xs font-bold text-primary uppercase tracking-wider">Step 01</span>
                                        </div>
                                        <h3 class="font-display text-xl font-semibold text-foreground mb-2">Upload Your Documents</h3>
                                        <p class="text-gray-600">Securely submit your financial documents through our encrypted platform. Bank statements, tax returns, and business financials.</p>
                                    </div>
                                </div>
                                <!-- Connector line -->
                                <div class="absolute left-6 top-20 w-0.5 h-16 bg-gradient-to-b from-primary to-primary/70 hidden md:block"></div>
                            </div>
                            <!-- Step 2 -->
                            <div class="relative">
                                <div class="flex gap-6 mb-8">
                                    <div class="flex-shrink-0">
                                        <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-primary to-primary/70 flex items-center justify-center shadow-lg">
                                            <!-- Search icon -->
                                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
                                        </div>
                                    </div>
                                    <div class="flex-1 pb-8 border-b border-gray-200">
                                        <div class="flex items-center gap-3 mb-2">
                                            <span class="text-xs font-bold text-primary uppercase tracking-wider">Step 02</span>
                                        </div>
                                        <h3 class="font-display text-xl font-semibold text-foreground mb-2">We Analyze Your Financials</h3>
                                        <p class="text-gray-600">Our AI-assisted system reviews your documents for loan eligibility indicators and identifies any missing information.</p>
                                    </div>
                                </div>
                                <div class="absolute left-6 top-20 w-0.5 h-16 bg-gradient-to-b from-primary to-primary/70 hidden md:block"></div>
                            </div>
                            <!-- Step 3 -->
                            <div class="relative">
                                <div class="flex gap-6 mb-8">
                                    <div class="flex-shrink-0">
                                        <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-primary to-primary/70 flex items-center justify-center shadow-lg">
                                            <!-- FileCheck icon -->
                                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4"/><rect x="3" y="4" width="18" height="18" rx="2"/></svg>
                                        </div>
                                    </div>
                                    <div class="flex-1 pb-8 border-b border-gray-200">
                                        <div class="flex items-center gap-3 mb-2">
                                            <span class="text-xs font-bold text-primary uppercase tracking-wider">Step 03</span>
                                        </div>
                                        <h3 class="font-display text-xl font-semibold text-foreground mb-2">Receive Clear Feedback</h3>
                                        <p class="text-gray-600">Get a straightforward assessment of your eligibility status along with specific recommendations.</p>
                                    </div>
                                </div>
                                <div class="absolute left-6 top-20 w-0.5 h-16 bg-gradient-to-b from-blue-600 to-gray-200 hidden md:block"></div>
                            </div>
                            <!-- Step 4 -->
                            <div class="relative">
                                <div class="flex gap-6 mb-8">
                                    <div class="flex-shrink-0">
                                        <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-primary to-primary/70 flex items-center justify-center shadow-lg">
                                            <!-- Calendar icon -->
                                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/></svg>
                                        </div>
                                    </div>
                                    <div class="flex-1 pb-8 border-b border-gray-200 last:border-b-0">
                                        <div class="flex items-center gap-3 mb-2">
                                            <span class="text-xs font-bold text-primary uppercase tracking-wider">Step 04</span>
                                        </div>
                                        <h3 class="font-display text-xl font-semibold text-foreground mb-2">Book Your Consultation</h3>
                                        <p class="text-gray-600">If you qualify, schedule a call with our team to discuss your funding options and next steps.</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="text-center mt-12">
                            <a href="#assessment" class="inline-flex items-center justify-center gap-2 whitespace-nowrap rounded-lg font-semibold ring-offset-background text-white transition-all duration-200 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:pointer-events-none disabled:opacity-50 [&_svg]:pointer-events-none [&_svg]:size-4 [&_svg]:shrink-0 font-body bg-primary text-primary-foreground hover:bg-primary/90 glow-amber shadow-lg hover:shadow-xl transform hover:-translate-y-0.5 h-14 px-10 text-lg group">
                                Start My Free Assessment
                                <svg class="w-5 h-5 text-white group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 12h14m-7-7l7 7-7 7"/></svg>
                            </a>
                        </div>
                    </div>
                </section>
  
                        <!-- Trust Section -->
                        <section class="py-20 lg:py-28 bg-primary text-white relative overflow-hidden">
                            <!-- Background pattern -->
                            <div class="absolute inset-0 opacity-5 pointer-events-none">
                                <div class="absolute inset-0" style="background-image:radial-gradient(circle at 2px 2px, currentColor 1px, transparent 0);background-size:32px 32px;"></div>
                            </div>
                            <div class="container relative z-10" style="margin: 0 auto">
                                <div class="text-center max-w-3xl mx-auto mb-16">
                                    <div class="inline-flex items-center gap-2 bg-white/10 rounded-full px-4 py-2 mb-6">
                                        <!-- Shield icon -->
                                        <svg class="w-4 h-4 bg-secondary/20" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
                                        <span class="text-sm">Your Security Matters</span>
                                    </div>
                                    <h2 class="font-display text-3xl md:text-4xl lg:text-5xl font-bold mb-6">Built on Trust & Security</h2>
                                    <p class="text-lg text-white/80">We understand you're sharing sensitive financial information. That's why we've built our platform with security as the foundation.</p>
                                </div>
                                <div class="grid md:grid-cols-2 gap-6 lg:gap-8 max-w-4xl mx-auto">
                                    <!-- Trust Point 1 -->
                                    <div class="flex items-start gap-4 bg-white/5 backdrop-blur-sm rounded-xl p-6 border border-white/10">
                                        <div class="w-10 h-10 rounded-lg flex items-center justify-center flex-shrink-0">
                                            <!-- Lock icon -->
                                            <svg class="w-5 h-5 bg-secondary/20" fill="none" stroke="currentColor" viewBox="0 0 24 24"><rect x="3" y="11" width="18" height="11" rx="2"/><path d="M7 11V7a5 5 0 0110 0v4"/></svg>
                                        </div>
                                        <div>
                                            <h3 class="font-display text-lg font-semibold mb-1">Bank-Level Encryption</h3>
                                            <p class="text-sm text-white/70">256-bit SSL encryption protects all your data in transit and at rest.</p>
                                        </div>
                                    </div>
                                    <!-- Trust Point 2 -->
                                    <div class="flex items-start gap-4 bg-white/5 backdrop-blur-sm rounded-xl p-6 border border-white/10">
                                        <div class="w-10 h-10 rounded-lg flex items-center justify-center flex-shrink-0">
                                            <!-- Server icon -->
                                            <svg class="w-5 h-5 bg-secondary/20" fill="none" stroke="currentColor" viewBox="0 0 24 24"><rect x="2" y="7" width="20" height="8" rx="2"/><rect x="2" y="15" width="20" height="6" rx="2"/><path d="M6 11h.01M6 19h.01"/></svg>
                                        </div>
                                        <div>
                                            <h3 class="font-display text-lg font-semibold mb-1">Secure Canadian Servers</h3>
                                            <p class="text-sm text-white/70">Your documents are stored on secure servers within Canada.</p>
                                        </div>
                                    </div>
                                    <!-- Trust Point 3 -->
                                    <div class="flex items-start gap-4 bg-white/5 backdrop-blur-sm rounded-xl p-6 border border-white/10">
                                        <div class="w-10 h-10 rounded-lg flex items-center justify-center flex-shrink-0">
                                            <!-- Eye icon -->
                                            <svg class="w-5 h-5 bg-secondary/20" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8S1 12 1 12z"/><circle cx="12" cy="12" r="3"/></svg>
                                        </div>
                                        <div>
                                            <h3 class="font-display text-lg font-semibold mb-1">We Never Sell Your Data</h3>
                                            <p class="text-sm text-white/70">Your information is used only for eligibility assessment. Period.</p>
                                        </div>
                                    </div>
                                    <!-- Trust Point 4 -->
                                    <div class="flex items-start gap-4 bg-white/5 backdrop-blur-sm rounded-xl p-6 border border-white/10">
                                        <div class="w-10 h-10 rounded-lg flex items-center justify-center flex-shrink-0">
                                            <!-- Users icon -->
                                            <svg class="w-5 h-5 bg-secondary/20" fill="none" stroke="currentColor" viewBox="0 0 24 24"><circle cx="9" cy="7" r="4"/><circle cx="17" cy="17" r="4"/><path d="M17 13a4 4 0 00-8 0"/></svg>
                                        </div>
                                        <div>
                                            <h3 class="font-display text-lg font-semibold mb-1">Human Review Available</h3>
                                            <p class="text-sm text-white/70">Real experts review complex cases to ensure accuracy.</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="mt-12 flex items-center justify-center gap-6 flex-wrap">
                                    <div class="flex items-center gap-2 text-white/60">
                                        <!-- MapPin icon -->
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 22s8-4 8-10a8 8 0 10-16 0c0 6 8 10 8 10z"/><circle cx="12" cy="12" r="3"/></svg>
                                        <span class="text-sm">Canadian-Based Service</span>
                                    </div>
                                    <div class="w-px h-4 bg-white/20 hidden sm:block"></div>
                                    <div class="flex items-center gap-2 text-white/60">
                                        <!-- Shield icon -->
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
                                        <span class="text-sm">PIPEDA Compliant</span>
                                    </div>
                                </div>
                            </div>
                        </section>

  <!-- FAQ Section -->
                <section id="faq" class="py-20 lg:py-28 bg-gray-100 scroll-mt-24">
                    <div class="container mx-auto">
                        <div class="text-center max-w-3xl mx-auto mb-16">
                            <h2 class="font-display text-3xl md:text-4xl lg:text-5xl font-bold text-foreground mb-6">Common Questions</h2>
                            <p class="text-lg text-primary">Everything you need to know before getting started.</p>
                        </div>
                        <div class="max-w-3xl mx-auto">
                            <div class="space-y-4">
                                <!-- FAQ 1 -->
                                <details class="bg-white rounded-xl border border-gray-200 px-6 overflow-hidden shadow">
                                    <summary class="text-left font-display font-semibold text-muted-foreground py-5 cursor-pointer">Is this a loan application?</summary>
                                    <div class="text-gray-600 pb-5">No. This is an eligibility assessment only. We analyze your documents to determine if you're likely to qualify for business financing. If you are, we'll guide you to the next steps. There's no commitment to proceed.</div>
                                </details>
                                <!-- FAQ 2 -->
                                <details class="bg-white rounded-xl border border-gray-200 px-6 overflow-hidden shadow">
                                    <summary class="text-left font-display font-semibold text-muted-foreground py-5 cursor-pointer">Will this affect my credit score?</summary>
                                    <div class="text-gray-600 pb-5">No. Our eligibility assessment does not perform a credit check and will not affect your credit score. We only review the documents you provide.</div>
                                </details>
                                <!-- FAQ 3 -->
                                <details class="bg-white rounded-xl border border-gray-200 px-6 overflow-hidden shadow">
                                    <summary class="text-left font-display font-semibold text-muted-foreground py-5 cursor-pointer">How long does the assessment take?</summary>
                                    <div class="text-gray-600 pb-5">Most assessments are completed within 48 hours of submitting all required documents. Complex cases may take slightly longer, but you'll always be kept informed of your status.</div>
                                </details>
                                <!-- FAQ 4 -->
                                <details class="bg-white rounded-xl border border-gray-200 px-6 overflow-hidden shadow">
                                    <summary class="text-left font-display font-semibold text-muted-foreground py-5 cursor-pointer">Is my data secure?</summary>
                                    <div class="text-gray-600 pb-5">Absolutely. We use bank-level 256-bit SSL encryption, store your data on secure Canadian servers, and never share your information with third parties. Your privacy is our priority.</div>
                                </details>
                                <!-- FAQ 5 -->
                                <details class="bg-white rounded-xl border border-gray-200 px-6 overflow-hidden shadow">
                                    <summary class="text-left font-display font-semibold text-muted-foreground py-5 cursor-pointer">What if I don't qualify?</summary>
                                    <div class="text-gray-600 pb-5">If our assessment indicates you may not qualify for traditional financing, we'll provide clear feedback on why and suggestions for what you might do to improve your eligibility in the future.</div>
                                </details>
                                <!-- FAQ 6 -->
                                <details class="bg-white rounded-xl border border-gray-200 px-6 overflow-hidden shadow">
                                    <summary class="text-left font-display font-semibold text-muted-foreground py-5 cursor-pointer">Do I have to commit to anything?</summary>
                                    <div class="text-gray-600 pb-5">No. The eligibility assessment is completely free with no strings attached. You're under no obligation to proceed with a loan application after receiving your results.</div>
                                </details>
                                <!-- FAQ 7 -->
                                <details class="bg-white rounded-xl border border-gray-200 px-6 overflow-hidden shadow">
                                    <summary class="text-left font-display font-semibold text-muted-foreground py-5 cursor-pointer">What documents do I need to upload?</summary>
                                    <div class="text-gray-600 pb-5">Typically, we need recent bank statements (3-6 months), your most recent tax returns, and financial statements if available. Our system will guide you through exactly what's needed and identify any missing documents.</div>
                                </details>
                            </div>
                        </div>
                    </div>
                </section>
                <!-- Final CTA Section -->
                <section class="py-20 lg:py-28 bg-white">
                    <div class="container mx-auto">
                        <div class="max-w-4xl mx-auto text-center">
                            <h2 class="font-display text-3xl md:text-4xl lg:text-5xl font-bold text-primary mb-6">Stop Waiting. Start Knowing.</h2>
                            <p class="text-lg md:text-xl text-primary mb-10 max-w-2xl mx-auto">Find out if your business qualifies for funding today. No credit check, no commitment, no waiting weeks for answers.</p>
                            <div class="flex flex-col sm:flex-row gap-4 justify-center mb-10">
                                <a href="#eligibility" class="group inline-flex items-center bg-primary gap-2 px-8 py-4 text-primary-foreground text-white rounded-lg font-semibold text-lg hover:shadow-xl transition-all duration-200 transform hover:-translate-y-1">
                                    Start My Eligibility Check
                                    <svg class="w-5 h-5 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 12h14m-7-7l7 7-7 7"/></svg>
                                </a>
                            </div>
                            <div class="flex flex-wrap justify-center gap-6 text-sm text-gray-600">
                                <div class="flex items-center gap-2">
                                    <svg class="w-4 h-4 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4"/></svg>
                                    <span>Free assessment</span>
                                </div>
                                <div class="flex items-center gap-2">
                                    <svg class="w-4 h-4 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4"/></svg>
                                    <span>Results in 48 hours</span>
                                </div>
                                <div class="flex items-center gap-2">
                                    <svg class="w-4 h-4 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4"/></svg>
                                    <span>100% secure</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>

                                <!-- Footer Section -->
                <footer class="bg-primary text-white py-12 lg:py-16">
                    <div class="container mx-auto">
                        <div class="grid md:grid-cols-3 gap-8 lg:gap-12">
                            <!-- Brand -->
                            <div>
                                <div class="font-display text-xl font-bold mb-4">SaferWealth</div>
                                <p class="text-white/70 text-sm mb-4">Helping Canadian business owners determine their loan eligibility faster with secure, AI-assisted financial analysis.</p>
                                <div class="flex items-center gap-2 text-sm text-white/60">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
                                    <span>PIPEDA Compliant</span>
                                </div>
                            </div>
                            <!-- Quick Links -->
                            <div>
                                <h4 class="font-semibold mb-4">Quick Links</h4>
                                <ul class="space-y-2 text-sm">
                                    <li><a href="#how-it-works" class="text-white/70 hover:text-white transition-colors">How It Works</a></li>
                                    <li><a href="#benefits" class="text-white/70 hover:text-white transition-colors">Benefits</a></li>
                                    <li><a href="#faq" class="text-white/70 hover:text-white transition-colors">FAQ</a></li>
                                    <li><a href="#" class="text-white/70 hover:text-white transition-colors">Privacy Policy</a></li>
                                    <li><a href="#" class="text-white/70 hover:text-white transition-colors">Terms of Service</a></li>
                                </ul>
                            </div>
                            <!-- Contact -->
                            <div>
                                <h4 class="font-semibold mb-4">Contact</h4>
                                <ul class="space-y-3 text-sm">
                                    <li class="flex items-center gap-2 text-white/70">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 22s8-4 8-10a8 8 0 10-16 0c0 6 8 10 8 10z"/><circle cx="12" cy="12" r="3"/></svg>
                                        <span>Toronto, Ontario, Canada</span>
                                    </li>
                                    <li class="flex items-center gap-2 text-white/70">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4h16v16H4z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M22 4L12 14.01 2 4"/></svg>
                                        <a href="mailto:hello@saferwealth.com" class="hover:text-white transition-colors">hello@saferwealth.com</a>
                                    </li>
                                </ul>
                            </div>
                        </div>
                        <div class="border-t border-white/10 mt-10 pt-8 text-center text-sm text-white/50">
                            <p>© {{ date('Y') }} SaferWealth. All rights reserved.</p>
                            <p class="mt-2">This is an eligibility assessment service only. We do not guarantee loan approval.</p>
                        </div>
                    </div>
                </footer>


</body>
</html>
