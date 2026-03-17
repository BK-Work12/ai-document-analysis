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
                <div class="px-6 py-4 bg-gradient-to-r from-teal-50 to-emerald-50 border-b border-gray-200">
                    <div class="flex items-center gap-3">
                        <div class="p-2 bg-teal-100 rounded-lg">
                            <svg class="w-5 h-5 text-teal-600" fill="currentColor" viewBox="0 0 20 20">
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
                                    class="w-4 h-4 text-teal-600 border-gray-300">
                                <label for="local_storage" class="ml-3 cursor-pointer flex-1">
                                    <p class="font-semibold text-gray-900">Local Storage</p>
                                    <p class="text-sm text-gray-600">Store files on your server (storage/app directory)</p>
                                </label>
                            </div>

                            <div class="flex items-center p-4 border border-gray-200 rounded-lg hover:bg-gray-50 transition cursor-pointer">
                                <input type="radio" id="s3_storage" name="use_local_storage" value="0" 
                                    {{ !env('USE_LOCAL_STORAGE', true) ? 'checked' : '' }}
                                    class="w-4 h-4 text-teal-600 border-gray-300">
                                <label for="s3_storage" class="ml-3 cursor-pointer flex-1">
                                    <p class="font-semibold text-gray-900">Amazon S3</p>
                                    <p class="text-sm text-gray-600">Store files on AWS S3 (requires S3 settings configured below)</p>
                                </label>
                            </div>
                        </div>

                        <div class="flex justify-end pt-4 border-t border-gray-200">
                            <button type="submit" class="inline-flex items-center gap-2 bg-gradient-to-r from-teal-600 to-emerald-600 hover:from-teal-700 hover:to-emerald-700 text-white px-6 py-2.5 rounded-lg font-semibold shadow-md hover:shadow-lg transition-all">
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

            <!-- OCR Limits Settings Card -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden mb-6">
                <div class="px-6 py-4 bg-gradient-to-r from-rose-50 to-orange-50 border-b border-gray-200">
                    <div class="flex items-center gap-3">
                        <div class="p-2 bg-rose-100 rounded-lg">
                            <svg class="w-5 h-5 text-rose-600" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4zm2 6a1 1 0 011-1h6a1 1 0 110 2H7a1 1 0 01-1-1zm1 3a1 1 0 100 2h6a1 1 0 100-2H7z" clip-rule="evenodd"></path>
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900">OCR Processing Limits</h3>
                            <p class="text-sm text-gray-600">Control OCR behavior and file size limits for Textract processing</p>
                        </div>
                    </div>
                </div>
                <div class="p-6">
                    <form action="{{ route('admin.settings.updateOcr') }}" method="POST" class="space-y-4">
                        @csrf
                        @method('patch')

                        <div class="space-y-4">
                            <!-- OCR Enabled Toggle -->
                            <div class="flex items-center justify-between p-4 border border-gray-200 rounded-lg hover:bg-gray-50 transition">
                                <div>
                                    <p class="font-semibold text-gray-900">Enable OCR Processing</p>
                                    <p class="text-sm text-gray-600">When enabled, documents will be processed through AWS Textract for text extraction</p>
                                </div>
                                <div class="flex items-center gap-2">
                                    <input type="hidden" name="ocr_enabled" value="0">
                                    <label class="inline-flex items-center cursor-pointer">
                                        <input type="checkbox" name="ocr_enabled" value="1" 
                                            {{ ($settings['ocr']['ocr_enabled'] ?? true) ? 'checked' : '' }}
                                            class="w-5 h-5 text-rose-600 bg-gray-100 border-gray-300 rounded focus:ring-rose-500 focus:ring-2">
                                        <span class="ml-2 text-sm font-medium text-gray-700">
                                            {{ ($settings['ocr']['ocr_enabled'] ?? true) ? 'Enabled' : 'Disabled' }}
                                        </span>
                                    </label>
                                </div>
                            </div>

                            <!-- Max File Size -->
                            <div>
                                <label for="ocr_max_file_size_mb" class="block text-sm font-semibold text-gray-900 mb-2">
                                    Maximum File Size for OCR (MB) <span class="text-red-600">*</span>
                                </label>
                                <input type="number" id="ocr_max_file_size_mb" name="ocr_max_file_size_mb" 
                                    value="{{ $settings['ocr']['ocr_max_file_size_mb'] ?? 10 }}"
                                    min="1" max="100" step="1"
                                    class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-rose-500 focus:border-rose-500 text-gray-900 transition-all" required>
                                @error('ocr_max_file_size_mb')
                                    <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                                @enderror
                                <p class="text-xs text-gray-500 mt-1">Files larger than this will skip OCR and require manual Bedrock analysis. Textract sync limit is 5MB.</p>
                            </div>

                            <!-- Large File Bedrock Manual Toggle -->
                            <div class="flex items-center justify-between p-4 border border-gray-200 rounded-lg hover:bg-gray-50 transition">
                                <div>
                                    <p class="font-semibold text-gray-900">Manual Bedrock Analysis for Large Files</p>
                                    <p class="text-sm text-gray-600">When enabled, files exceeding the size limit will be flagged for manual review and Bedrock analysis instead of automatic OCR</p>
                                </div>
                                <div class="flex items-center gap-2">
                                    <input type="hidden" name="large_file_bedrock_manual" value="0">
                                    <label class="inline-flex items-center cursor-pointer">
                                        <input type="checkbox" name="large_file_bedrock_manual" value="1" 
                                            {{ ($settings['ocr']['large_file_bedrock_manual'] ?? true) ? 'checked' : '' }}
                                            class="w-5 h-5 text-rose-600 bg-gray-100 border-gray-300 rounded focus:ring-rose-500 focus:ring-2">
                                        <span class="ml-2 text-sm font-medium text-gray-700">
                                            {{ ($settings['ocr']['large_file_bedrock_manual'] ?? true) ? 'Enabled' : 'Disabled' }}
                                        </span>
                                    </label>
                                </div>
                            </div>

                            <div class="p-4 bg-amber-50 border border-amber-200 rounded-lg">
                                <div class="flex items-start gap-3">
                                    <svg class="w-5 h-5 text-amber-600 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                                    </svg>
                                    <div>
                                        <p class="font-semibold text-amber-800">How it works</p>
                                        <ul class="text-sm text-amber-700 mt-1 list-disc list-inside space-y-1">
                                            <li>Files within the size limit: Automatic OCR via Textract, then Bedrock analysis</li>
                                            <li>Files exceeding size limit: Skip OCR, flag as "pending_manual_review" for direct Bedrock analysis</li>
                                            <li>OCR disabled: All files skip Textract and go to Bedrock directly</li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="flex justify-end pt-4 border-t border-gray-200">
                            <button type="submit" style="background-color: #e11d48; color: white; padding: 10px 24px; border-radius: 8px; font-weight: 600; display: inline-flex; align-items: center; gap: 8px; border: none; cursor: pointer;">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width: 20px; height: 20px;">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                </svg>
                                Save OCR Settings
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- SES Settings Card -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden mb-6">
                <div class="px-6 py-4 bg-gradient-to-r from-teal-50 to-cyan-50 border-b border-gray-200">
                    <div class="flex items-center gap-3">
                        <div class="p-2 bg-teal-100 rounded-lg">
                            <svg class="w-5 h-5 text-teal-600" fill="currentColor" viewBox="0 0 20 20">
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
                                    class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-teal-500 focus:border-teal-500 text-gray-900 transition-all" required>
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
                                        class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-teal-500 focus:border-teal-500 text-gray-900 transition-all" required>
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
                                    class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-teal-500 focus:border-teal-500 text-gray-900 transition-all" required>
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
                                    class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-teal-500 focus:border-teal-500 text-gray-900 transition-all" required>
                                @error('aws_ses_from_email')
                                    <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <div class="flex justify-end pt-4 border-t border-gray-200">
                            <button type="submit" class="inline-flex items-center gap-2 bg-gradient-to-r from-teal-600 to-cyan-600 hover:from-teal-700 hover:to-cyan-700 text-white px-6 py-2.5 rounded-lg font-semibold shadow-md hover:shadow-lg transition-all">
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
            <!-- Google OAuth Settings Card -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden mb-6">
                <div class="px-6 py-4 bg-gradient-to-r from-emerald-50 to-teal-50 border-b border-gray-200">
                    <div class="flex items-center gap-3">
                        <div class="p-2 bg-emerald-100 rounded-lg">
                            <svg class="w-5 h-5 text-emerald-600" fill="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <title>Google</title>
                                <path d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z" fill="#4285F4"/>
                                <path d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z" fill="#34A853"/>
                                <path d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z" fill="#FBBC05"/>
                                <path d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z" fill="#EA4335"/>
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900">Google Sign-in Settings</h3>
                            <p class="text-sm text-gray-600">Configure Google OAuth credentials for client authentication</p>
                        </div>
                    </div>
                </div>
                <div class="p-6">
                    <form action="{{ route('admin.settings.updateGoogle') }}" method="POST" class="space-y-4">
                        @csrf
                        @method('patch')

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label for="google_client_id" class="block text-sm font-semibold text-gray-900 mb-2">
                                    Google Client ID <span class="text-red-600">*</span>
                                </label>
                                <input type="text" id="google_client_id" name="google_client_id" 
                                    value="{{ $settings['google']['client_id'] ?? '' }}"
                                    placeholder="Your Google Client ID"
                                    class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 text-gray-900 transition-all" required>
                                @error('google_client_id')
                                    <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="google_client_secret" class="block text-sm font-semibold text-gray-900 mb-2">
                                    Google Client Secret <span class="text-red-600">*</span>
                                </label>
                                <div class="relative">
                                    <input type="password" id="google_client_secret" name="google_client_secret" 
                                        value="{{ $settings['google']['client_secret'] ?? '' }}"
                                        placeholder="Your Google Client Secret"
                                        class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 text-gray-900 transition-all" required>
                                    <button type="button" onclick="togglePassword(this)" class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-500 hover:text-gray-700">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                        </svg>
                                    </button>
                                </div>
                                @error('google_client_secret')
                                    <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="mt-2 text-sm text-gray-600 p-3 bg-gray-50 rounded border border-gray-200">
                            <strong>Authorized redirect URI:</strong> <code>{{ env('APP_URL') }}/auth/google/callback</code>
                        </div>

                        <div class="flex justify-end pt-4 border-t border-gray-200">
                            <button type="submit" class="inline-flex items-center gap-2 bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-700 hover:to-teal-700 text-white px-6 py-2.5 rounded-lg font-semibold shadow-md hover:shadow-lg transition-all">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                </svg>
                                Save Google Settings
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
