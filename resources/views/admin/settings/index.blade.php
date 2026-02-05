<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-3">
            <svg class="w-8 h-8 text-gray-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
            </svg>
            <div>
                <h2 class="font-semibold text-2xl text-gray-900 leading-tight">Integration Settings</h2>
                <p class="text-sm text-gray-600 mt-0.5">Configure AWS services and API credentials</p>
            </div>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
            @if ($message = Session::get('success'))
                <div class="mb-6 p-4 bg-green-50 border-l-4 border-green-500 text-green-800 rounded-lg flex items-start gap-3 shadow-sm">
                    <svg class="w-5 h-5 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                    </svg>
                    <div>
                        <p class="font-semibold">Success!</p>
                        <p class="text-sm">{{ $message }}</p>
                    </div>
                </div>
            @endif

            <!-- Storage Settings Card -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden mb-6">
                <div class="px-6 py-4 bg-gradient-to-r from-blue-50 to-indigo-50 border-b border-gray-200">
                    <div class="flex items-center gap-3">
                        <div class="p-2 bg-blue-100 rounded-lg">
                            <svg class="w-5 h-5 text-blue-600" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M4 3a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V5a2 2 0 00-2-2H4zm12 12H4l4.5-9 3 6 2.5-4 4 8z"></path>
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900">File Storage Configuration</h3>
                            <p class="text-sm text-gray-600">Choose where to store uploaded documents</p>
                        </div>
                    </div>
                </div>
                <div class="p-6">
                    <form action="{{ route('admin.settings.updateStorage') }}" method="POST" class="space-y-4">
                        @csrf
                        @method('patch')

                        <div class="space-y-3">
                            <div class="flex items-center p-4 border border-gray-200 rounded-lg hover:bg-gray-50 transition cursor-pointer">
                                <input type="radio" id="local_storage" name="use_local_storage" value="1" 
                                    {{ env('USE_LOCAL_STORAGE', true) ? 'checked' : '' }}
                                    class="w-4 h-4 text-blue-600 border-gray-300">
                                <label for="local_storage" class="ml-3 cursor-pointer flex-1">
                                    <p class="font-semibold text-gray-900">Local Storage</p>
                                    <p class="text-sm text-gray-600">Store files on your server (storage/app directory)</p>
                                </label>
                            </div>

                            <div class="flex items-center p-4 border border-gray-200 rounded-lg hover:bg-gray-50 transition cursor-pointer">
                                <input type="radio" id="s3_storage" name="use_local_storage" value="0" 
                                    {{ !env('USE_LOCAL_STORAGE', true) ? 'checked' : '' }}
                                    class="w-4 h-4 text-blue-600 border-gray-300">
                                <label for="s3_storage" class="ml-3 cursor-pointer flex-1">
                                    <p class="font-semibold text-gray-900">Amazon S3</p>
                                    <p class="text-sm text-gray-600">Store files on AWS S3 (requires S3 settings configured below)</p>
                                </label>
                            </div>
                        </div>

                        <div class="flex justify-end pt-4 border-t border-gray-200">
                            <button type="submit" class="inline-flex items-center gap-2 bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 text-white px-6 py-2.5 rounded-lg font-semibold shadow-md hover:shadow-lg transition-all">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                </svg>
                                Save Storage Settings
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- S3 Settings Card -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden mb-6">
                <div class="px-6 py-4 bg-gradient-to-r from-orange-50 to-amber-50 border-b border-gray-200">
                    <div class="flex items-center gap-3">
                        <div class="p-2 bg-orange-100 rounded-lg">
                            <svg class="w-5 h-5 text-orange-600" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M4 3a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V5a2 2 0 00-2-2H4zm12 12H4l4.5-9 3 6 2.5-4 4 8z"></path>
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900">Amazon S3 Settings</h3>
                            <p class="text-sm text-gray-600">Configure AWS S3 bucket for document storage</p>
                        </div>
                    </div>
                </div>
                <div class="p-6">
                    <form action="{{ route('admin.settings.updateS3') }}" method="POST" class="space-y-4">
                        @csrf
                        @method('patch')

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label for="aws_access_key_id" class="block text-sm font-semibold text-gray-900 mb-2">
                                    AWS Access Key ID <span class="text-red-600">*</span>
                                </label>
                                <input type="text" id="aws_access_key_id" name="aws_access_key_id" 
                                    value="{{ $settings['s3']['aws_access_key_id'] ?? '' }}"
                                    placeholder="AKIAIOSFODNN7EXAMPLE"
                                    class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-orange-500 text-gray-900 transition-all" required>
                                @error('aws_access_key_id')
                                    <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="aws_secret_access_key" class="block text-sm font-semibold text-gray-900 mb-2">
                                    AWS Secret Access Key <span class="text-red-600">*</span>
                                </label>
                                <div class="relative">
                                    <input type="password" id="aws_secret_access_key" name="aws_secret_access_key" 
                                        value="{{ $settings['s3']['aws_secret_access_key'] ?? '' }}"
                                        placeholder="Your secret access key"
                                        class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-orange-500 text-gray-900 transition-all" required>
                                    <button type="button" onclick="togglePassword(this)" class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-500 hover:text-gray-700">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                        </svg>
                                    </button>
                                </div>
                                @error('aws_secret_access_key')
                                    <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="aws_default_region" class="block text-sm font-semibold text-gray-900 mb-2">
                                    AWS Region <span class="text-red-600">*</span>
                                </label>
                                <input type="text" id="aws_default_region" name="aws_default_region" 
                                    value="{{ $settings['s3']['aws_default_region'] ?? '' }}"
                                    placeholder="us-east-2"
                                    class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-orange-500 text-gray-900 transition-all" required>
                                @error('aws_default_region')
                                    <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="aws_bucket" class="block text-sm font-semibold text-gray-900 mb-2">
                                    S3 Bucket Name <span class="text-red-600">*</span>
                                </label>
                                <input type="text" id="aws_bucket" name="aws_bucket" 
                                    value="{{ $settings['s3']['aws_bucket'] ?? '' }}"
                                    placeholder="my-bucket-name"
                                    class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-orange-500 text-gray-900 transition-all" required>
                                @error('aws_bucket')
                                    <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="aws_kms_key_id" class="block text-sm font-semibold text-gray-900 mb-2">
                                    KMS Key ID <span class="text-gray-600">(Optional)</span>
                                </label>
                                <input type="text" id="aws_kms_key_id" name="aws_kms_key_id" 
                                    value="{{ $settings['s3']['aws_kms_key_id'] ?? '' }}"
                                    placeholder="arn:aws:kms:region:account:key/id"
                                    class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-orange-500 text-gray-900 transition-all">
                                @error('aws_kms_key_id')
                                    <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                                @enderror
                                <p class="text-xs text-gray-500 mt-1">For server-side encryption with AWS KMS</p>
                            </div>
                        </div>

                        <div class="flex justify-end pt-4 border-t border-gray-200">
                            <button type="submit" class="inline-flex items-center gap-2 bg-gradient-to-r from-orange-600 to-amber-600 hover:from-orange-700 hover:to-amber-700 text-white px-6 py-2.5 rounded-lg font-semibold shadow-md hover:shadow-lg transition-all">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                </svg>
                                Save S3 Settings
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Textract Settings Card -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden mb-6">
                <div class="px-6 py-4 bg-gradient-to-r from-purple-50 to-pink-50 border-b border-gray-200">
                    <div class="flex items-center gap-3">
                        <div class="p-2 bg-purple-100 rounded-lg">
                            <svg class="w-5 h-5 text-purple-600" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M4 3a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V5a2 2 0 00-2-2H4zm12 12H4l4.5-9 3 6 2.5-4 4 8z"></path>
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900">AWS Textract Settings</h3>
                            <p class="text-sm text-gray-600">Configure document text extraction service</p>
                        </div>
                    </div>
                </div>
                <div class="p-6">
                    <form action="{{ route('admin.settings.updateTextract') }}" method="POST" class="space-y-4">
                        @csrf
                        @method('patch')

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label for="textract_region" class="block text-sm font-semibold text-gray-900 mb-2">
                                    Textract Region <span class="text-red-600">*</span>
                                </label>
                                <input type="text" id="textract_region" name="textract_region" 
                                    value="{{ $settings['textract']['textract_region'] ?? '' }}"
                                    placeholder="us-east-2"
                                    class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500 text-gray-900 transition-all" required>
                                @error('textract_region')
                                    <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                                @enderror
                                <p class="text-xs text-gray-500 mt-1">AWS region where Textract is available</p>
                            </div>

                            <div>
                                <label for="textract_bucket" class="block text-sm font-semibold text-gray-900 mb-2">
                                    S3 Bucket for Processing <span class="text-red-600">*</span>
                                </label>
                                <input type="text" id="textract_bucket" name="textract_bucket" 
                                    value="{{ $settings['textract']['textract_bucket'] ?? '' }}"
                                    placeholder="aidocumentanalyses"
                                    class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500 text-gray-900 transition-all" required>
                                @error('textract_bucket')
                                    <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                                @enderror
                                <p class="text-xs text-gray-500 mt-1">S3 bucket for storing documents to process</p>
                            </div>
                        </div>

                        <div class="flex justify-end pt-4 border-t border-gray-200">
                            <button type="submit" class="inline-flex items-center gap-2 bg-gradient-to-r from-purple-600 to-pink-600 hover:from-purple-700 hover:to-pink-700 text-white px-6 py-2.5 rounded-lg font-semibold shadow-md hover:shadow-lg transition-all">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                </svg>
                                Save Textract Settings
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- SES Settings Card -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden mb-6">
                <div class="px-6 py-4 bg-gradient-to-r from-blue-50 to-cyan-50 border-b border-gray-200">
                    <div class="flex items-center gap-3">
                        <div class="p-2 bg-blue-100 rounded-lg">
                            <svg class="w-5 h-5 text-blue-600" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M2.003 5.884L10 9.882l7.997-3.998A2 2 0 0016 4H4a2 2 0 00-1.997 1.884z"></path>
                                <path d="M18 8.118l-8 4-8-4V14a2 2 0 002 2h12a2 2 0 002-2V8.118z"></path>
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900">Amazon SES Settings</h3>
                            <p class="text-sm text-gray-600">Configure AWS SES for sending emails</p>
                        </div>
                    </div>
                </div>
                <div class="p-6">
                    <form action="{{ route('admin.settings.updateSES') }}" method="POST" class="space-y-4">
                        @csrf
                        @method('patch')

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label for="aws_ses_key" class="block text-sm font-semibold text-gray-900 mb-2">
                                    AWS SES Key <span class="text-red-600">*</span>
                                </label>
                                <input type="text" id="aws_ses_key" name="aws_ses_key" 
                                    value="{{ $settings['ses']['aws_ses_key'] ?? '' }}"
                                    placeholder="AKIAIOSFODNN7EXAMPLE"
                                    class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-gray-900 transition-all" required>
                                @error('aws_ses_key')
                                    <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="aws_ses_secret" class="block text-sm font-semibold text-gray-900 mb-2">
                                    AWS SES Secret <span class="text-red-600">*</span>
                                </label>
                                <div class="relative">
                                    <input type="password" id="aws_ses_secret" name="aws_ses_secret" 
                                        value="{{ $settings['ses']['aws_ses_secret'] ?? '' }}"
                                        placeholder="Your secret"
                                        class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-gray-900 transition-all" required>
                                    <button type="button" onclick="togglePassword(this)" class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-500 hover:text-gray-700">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                        </svg>
                                    </button>
                                </div>
                                @error('aws_ses_secret')
                                    <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="aws_ses_region" class="block text-sm font-semibold text-gray-900 mb-2">
                                    AWS SES Region <span class="text-red-600">*</span>
                                </label>
                                <input type="text" id="aws_ses_region" name="aws_ses_region" 
                                    value="{{ $settings['ses']['aws_ses_region'] ?? '' }}"
                                    placeholder="us-east-2"
                                    class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-gray-900 transition-all" required>
                                @error('aws_ses_region')
                                    <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="aws_ses_from_email" class="block text-sm font-semibold text-gray-900 mb-2">
                                    From Email Address <span class="text-red-600">*</span>
                                </label>
                                <input type="email" id="aws_ses_from_email" name="aws_ses_from_email" 
                                    value="{{ $settings['ses']['aws_ses_from_email'] ?? '' }}"
                                    placeholder="noreply@example.com"
                                    class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-gray-900 transition-all" required>
                                @error('aws_ses_from_email')
                                    <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <div class="flex justify-end pt-4 border-t border-gray-200">
                            <button type="submit" class="inline-flex items-center gap-2 bg-gradient-to-r from-blue-600 to-cyan-600 hover:from-blue-700 hover:to-cyan-700 text-white px-6 py-2.5 rounded-lg font-semibold shadow-md hover:shadow-lg transition-all">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                </svg>
                                Save SES Settings
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Bedrock Settings Card -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                <div class="px-6 py-4 bg-gradient-to-r from-purple-50 to-pink-50 border-b border-gray-200">
                    <div class="flex items-center gap-3">
                        <div class="p-2 bg-purple-100 rounded-lg">
                            <svg class="w-5 h-5 text-purple-600" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M11 3a1 1 0 10-2 0v1a1 1 0 102 0V3zM15.657 5.757a1 1 0 00-1.414-1.414l-.707.707a1 1 0 001.414 1.414l.707-.707zM18 10a1 1 0 01-1 1h-1a1 1 0 110-2h1a1 1 0 011 1zM15.657 14.243a1 1 0 001.414-1.414l-.707-.707a1 1 0 00-1.414 1.414l.707.707zM11 17a1 1 0 102 0v-1a1 1 0 10-2 0v1zM5.757 15.657a1 1 0 00-1.414-1.414l-.707.707a1 1 0 001.414 1.414l.707-.707zM2 10a1 1 0 011 1v1a1 1 0 11-2 0v-1a1 1 0 011-1zM5.757 5.757a1 1 0 000-1.414L5.05 3.636a1 1 0 00-1.414 1.414l.707.707z"></path>
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900">Bedrock Settings</h3>
                            <p class="text-sm text-gray-600">Configure AWS Bedrock for AI document analysis</p>
                        </div>
                    </div>
                </div>
                <div class="p-6">
                    <form action="{{ route('admin.settings.updateBedrock') }}" method="POST" class="space-y-4">
                        @csrf
                        @method('patch')

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label for="bedrock_region" class="block text-sm font-semibold text-gray-900 mb-2">
                                    Bedrock Region <span class="text-red-600">*</span>
                                </label>
                                <input type="text" id="bedrock_region" name="bedrock_region" 
                                    value="{{ $settings['bedrock']['bedrock_region'] ?? '' }}"
                                    placeholder="us-east-2"
                                    class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500 text-gray-900 transition-all" required>
                                @error('bedrock_region')
                                    <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                                @enderror
                                <p class="text-xs text-gray-500 mt-1">Bedrock is available in specific regions</p>
                            </div>

                            <div>
                                <label for="bedrock_model_id" class="block text-sm font-semibold text-gray-900 mb-2">
                                    Model ID <span class="text-red-600">*</span>
                                </label>
                                <input type="text" id="bedrock_model_id" name="bedrock_model_id" 
                                    value="{{ $settings['bedrock']['bedrock_model_id'] ?? '' }}"
                                    placeholder="anthropic.claude-3-sonnet-20240229-v1:0"
                                    class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500 text-gray-900 transition-all" required>
                                @error('bedrock_model_id')
                                    <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="md:col-span-2">
                                <label for="bedrock_knowledge_base_id" class="block text-sm font-semibold text-gray-900 mb-2">
                                    Knowledge Base ID <span class="text-gray-600">(Optional)</span>
                                </label>
                                <input type="text" id="bedrock_knowledge_base_id" name="bedrock_knowledge_base_id" 
                                    value="{{ $settings['bedrock']['bedrock_knowledge_base_id'] ?? '' }}"
                                    placeholder="Leave empty for now, will be set after creating Knowledge Base"
                                    class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500 text-gray-900 transition-all">
                                @error('bedrock_knowledge_base_id')
                                    <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                                @enderror
                                <p class="text-xs text-gray-500 mt-1">Create in AWS Bedrock console, then add ID here</p>
                            </div>
                        </div>

                        <div class="flex justify-end pt-4 border-t border-gray-200">
                            <button type="submit" class="inline-flex items-center gap-2 bg-gradient-to-r from-purple-600 to-pink-600 hover:from-purple-700 hover:to-pink-700 text-white px-6 py-2.5 rounded-lg font-semibold shadow-md hover:shadow-lg transition-all">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                </svg>
                                Save Bedrock Settings
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        function togglePassword(button) {
            const input = button.previousElementSibling;
            input.type = input.type === 'password' ? 'text' : 'password';
        }
    </script>
</x-app-layout>
