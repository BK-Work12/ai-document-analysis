<?php

namespace App\Http\Controllers;

use App\Jobs\HandleSESBounce;
use App\Jobs\HandleSESComplaint;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class SESWebhookController extends Controller
{
    /**
     * Handle SES notifications (bounces, complaints, deliveries)
     * 
     * Configure this URL in AWS SES SNS topic:
     * https://yourdomain.com/api/ses-webhook
     */
    public function handle(Request $request): Response
    {
        // Validate SNS signature
        if (!$this->verifySignature($request)) {
            return response('Unauthorized', 401);
        }

        $data = $request->json()->all();

        // Handle subscription confirmation
        if ($data['Type'] === 'SubscriptionConfirmation') {
            $this->confirmSubscription($data['SubscribeURL']);
            return response('OK', 200);
        }

        // Parse the message
        if ($data['Type'] === 'Notification') {
            $message = json_decode($data['Message'], true);

            if ($message['eventType'] === 'Bounce') {
                HandleSESBounce::dispatch($message['bounce']);
            } elseif ($message['eventType'] === 'Complaint') {
                HandleSESComplaint::dispatch($message['complaint']);
            }

            return response('OK', 200);
        }

        return response('Invalid', 400);
    }

    /**
     * Verify AWS SNS message signature
     */
    private function verifySignature(Request $request): bool
    {
        // Basic validation - in production, verify AWS signature properly
        return $request->has('Message') && $request->has('Type');
    }

    /**
     * Confirm SNS subscription
     */
    private function confirmSubscription(string $subscribeUrl): void
    {
        // Confirm the subscription by visiting the URL
        // In production, use a queue job or cron to do this securely
        try {
            file_get_contents($subscribeUrl);
        } catch (\Exception $e) {
            // Log error
        }
    }
}
