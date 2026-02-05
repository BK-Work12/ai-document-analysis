<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;

class SettingsController extends Controller
{
    public function index()
    {
        $settings = [
            'storage' => [
                'use_local_storage' => env('USE_LOCAL_STORAGE', true),
            ],
            's3' => [
                'aws_access_key_id' => $this->getEnvValue('AWS_ACCESS_KEY_ID'),
                'aws_secret_access_key' => $this->getEnvValue('AWS_SECRET_ACCESS_KEY'),
                'aws_default_region' => $this->getEnvValue('AWS_DEFAULT_REGION'),
                'aws_bucket' => $this->getEnvValue('AWS_BUCKET'),
                'aws_kms_key_id' => $this->getEnvValue('AWS_KMS_KEY_ID'),
            ],
            'textract' => [
                'textract_region' => $this->getEnvValue('TEXTRACT_REGION'),
                'textract_bucket' => $this->getEnvValue('TEXTRACT_BUCKET'),
            ],
            'bedrock' => [
                'bedrock_region' => $this->getEnvValue('BEDROCK_REGION'),
                'bedrock_model_id' => $this->getEnvValue('BEDROCK_MODEL_ID'),
                'bedrock_knowledge_base_id' => $this->getEnvValue('BEDROCK_KNOWLEDGE_BASE_ID'),
            ],
            'ses' => [
                'aws_ses_key' => $this->getEnvValue('AWS_SES_KEY'),
                'aws_ses_secret' => $this->getEnvValue('AWS_SES_SECRET'),
                'aws_ses_region' => $this->getEnvValue('AWS_SES_REGION'),
                'aws_ses_from_email' => $this->getEnvValue('MAIL_FROM_ADDRESS'),
            ],
        ];

        return view('admin.settings.index', compact('settings'));
    }

    private function getEnvValue($key)
    {
        $envFile = base_path('.env');
        if (!file_exists($envFile)) {
            return '';
        }

        $content = file_get_contents($envFile);
        $pattern = '/^' . preg_quote($key) . '=(.*)$/m';
        
        if (preg_match($pattern, $content, $matches)) {
            $value = $matches[1];
            // Remove quotes if present
            return trim($value, '"\'');
        }

        return '';
    }

    public function updateStorage(Request $request)
    {
        $validated = $request->validate([
            'use_local_storage' => 'boolean',
        ]);

        $this->updateEnvFile([
            'USE_LOCAL_STORAGE' => $validated['use_local_storage'] ? 'true' : 'false',
        ]);

        if ($request->wantsJson()) {
            return response()->json(['success' => true, 'message' => 'Storage settings updated successfully!']);
        }

        return redirect()->route('admin.settings.index')->with('success', 'Storage settings updated successfully!');
    }

    public function updateS3(Request $request)
    {
        $validated = $request->validate([
            'aws_access_key_id' => 'required|string',
            'aws_secret_access_key' => 'required|string',
            'aws_default_region' => 'required|string',
            'aws_bucket' => 'required|string',
        ]);

        $this->updateEnvFile([
            'AWS_ACCESS_KEY_ID' => $validated['aws_access_key_id'],
            'AWS_SECRET_ACCESS_KEY' => $validated['aws_secret_access_key'],
            'AWS_DEFAULT_REGION' => $validated['aws_default_region'],
            'AWS_BUCKET' => $validated['aws_bucket'],
        ]);

        if ($request->wantsJson()) {
            return response()->json(['success' => true, 'message' => 'S3 settings updated successfully!']);
        }

        return redirect()->route('admin.settings.index')->with('success', 'S3 settings updated successfully!');
    }

    public function updateSES(Request $request)
    {
        $validated = $request->validate([
            'aws_ses_key' => 'required|string',
            'aws_ses_secret' => 'required|string',
            'aws_ses_region' => 'required|string',
            'aws_ses_from_email' => 'required|email',
        ]);

        $this->updateEnvFile([
            'AWS_SES_KEY' => $validated['aws_ses_key'],
            'AWS_SES_SECRET' => $validated['aws_ses_secret'],
            'AWS_SES_REGION' => $validated['aws_ses_region'],
            'MAIL_FROM_ADDRESS' => $validated['aws_ses_from_email'],
        ]);

        if ($request->wantsJson()) {
            return response()->json(['success' => true, 'message' => 'SES settings updated successfully!']);
        }

        return redirect()->route('admin.settings.index')->with('success', 'SES settings updated successfully!');
    }

    public function updateBedrock(Request $request)
    {
        $validated = $request->validate([
            'bedrock_region' => 'required|string',
            'bedrock_model_id' => 'required|string',
            'bedrock_knowledge_base_id' => 'nullable|string',
        ]);

        $this->updateEnvFile([
            'BEDROCK_REGION' => $validated['bedrock_region'],
            'BEDROCK_MODEL_ID' => $validated['bedrock_model_id'],
            'BEDROCK_KNOWLEDGE_BASE_ID' => $validated['bedrock_knowledge_base_id'] ?? '',
        ]);

        if ($request->wantsJson()) {
            return response()->json(['success' => true, 'message' => 'Bedrock settings updated successfully!']);
        }

        return redirect()->route('admin.settings.index')->with('success', 'Bedrock settings updated successfully!');
    }

    public function updateTextract(Request $request)
    {
        $validated = $request->validate([
            'textract_region' => 'required|string',
            'textract_bucket' => 'required|string',
        ]);

        $this->updateEnvFile([
            'TEXTRACT_REGION' => $validated['textract_region'],
            'TEXTRACT_BUCKET' => $validated['textract_bucket'],
        ]);

        if ($request->wantsJson()) {
            return response()->json(['success' => true, 'message' => 'Textract settings updated successfully!']);
        }

        return redirect()->route('admin.settings.index')->with('success', 'Textract settings updated successfully!');
    }

    private function updateEnvFile(array $values)
    {
        $envFile = base_path('.env');
        
        if (!file_exists($envFile)) {
            // Create the file if it doesn't exist
            file_put_contents($envFile, '');
        }

        $content = file_get_contents($envFile);

        foreach ($values as $key => $value) {
            // Escape special characters but preserve the value
            $escapedValue = $value;
            
            // Check if value contains special characters that need quoting
            if (preg_match('/["\s$&]/', $value)) {
                $escapedValue = '"' . str_replace('"', '\\"', $value) . '"';
            } else {
                $escapedValue = '"' . $value . '"';
            }
            
            $pattern = '/^' . preg_quote($key) . '=(.*)$/m';
            
            if (preg_match($pattern, $content)) {
                // Replace existing key
                $content = preg_replace($pattern, "{$key}={$escapedValue}", $content);
            } else {
                // Add new key
                $content .= "\n{$key}={$escapedValue}";
            }
        }

        // Write the file
        if (!file_put_contents($envFile, $content)) {
            throw new \Exception('Unable to write to .env file. Check file permissions.');
        }
        
        // Clear config cache to pick up new values
        \Artisan::call('config:clear');
    }
}
