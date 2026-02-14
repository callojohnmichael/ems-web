<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Validator;

class ApiIntegrationController extends Controller
{
    /**
     * Display API integration landing page
     */
    public function index(): JsonResponse
    {
        return response()->json([
            'success' => true,
            'message' => 'Welcome to Event Management System API',
            'version' => '1.0.0',
            'endpoints' => [
                'documentation' => route('api.docs'),
                'quick_start' => route('api.quick-start'),
                'authentication' => route('api.auth-guide'),
                'examples' => route('api.examples'),
                'sdk' => route('api.sdk'),
                'webhooks' => route('api.webhooks'),
                'support' => route('api.support'),
            ],
            'base_url' => url('/api'),
            'status' => 'operational'
        ]);
    }

    /**
     * Quick start guide
     */
    public function quickStart(): JsonResponse
    {
        return response()->json([
            'success' => true,
            'title' => 'Quick Start Guide',
            'steps' => [
                [
                    'step' => 1,
                    'title' => 'Get API Access',
                    'description' => 'Register your application to get API credentials',
                    'action' => 'POST /api/integration/register',
                    'example' => [
                        'app_name' => 'My Event App',
                        'description' => 'Application for event management',
                        'contact_email' => 'developer@example.com',
                        'website' => 'https://myapp.com'
                    ]
                ],
                [
                    'step' => 2,
                    'title' => 'Generate API Token',
                    'description' => 'Create a personal access token for authentication',
                    'action' => 'POST /api/integration/token',
                    'example' => [
                        'app_id' => 'your_app_id',
                        'app_secret' => 'your_app_secret'
                    ]
                ],
                [
                    'step' => 3,
                    'title' => 'Make Your First Request',
                    'description' => 'Test your connection by fetching events',
                    'action' => 'GET /api/events',
                    'headers' => [
                        'Authorization: Bearer your_token_here',
                        'Content-Type: application/json',
                        'Accept: application/json'
                    ]
                ],
                [
                    'step' => 4,
                    'title' => 'Create Your First Event',
                    'description' => 'Create an event using the API',
                    'action' => 'POST /api/events',
                    'example' => [
                        'title' => 'My First API Event',
                        'description' => 'Created via API',
                        'start_at' => '2024-03-01T09:00:00Z',
                        'end_at' => '2024-03-01T17:00:00Z',
                        'venue_id' => 1,
                        'number_of_participants' => 50
                    ]
                ]
            ],
            'next_steps' => [
                'Read the full documentation',
                'Try the interactive examples',
                'Set up webhooks for real-time updates',
                'Join our developer community'
            ]
        ]);
    }

    /**
     * Authentication guide
     */
    public function authGuide(): JsonResponse
    {
        return response()->json([
            'success' => true,
            'title' => 'Authentication Guide',
            'methods' => [
                'bearer_token' => [
                    'name' => 'Bearer Token Authentication',
                    'description' => 'Include your API token in the Authorization header',
                    'how_to' => [
                        '1. Generate an API token from your dashboard',
                        '2. Include the token in every API request',
                        '3. Use the format: Authorization: Bearer {token}'
                    ],
                    'example' => [
                        'curl -X GET "http://ems-web.test/api/events" \
  -H "Authorization: Bearer your_token_here" \
  -H "Content-Type: application/json"'
                    ],
                    'security_notes' => [
                        'Keep your token secret and secure',
                        'Never expose tokens in client-side code',
                        'Regenerate tokens if compromised',
                        'Tokens expire after 1 year'
                    ]
                ]
            ],
            'token_management' => [
                'generate' => 'POST /api/integration/token',
                'refresh' => 'POST /api/integration/token/refresh',
                'revoke' => 'DELETE /api/integration/token',
                'list' => 'GET /api/integration/tokens'
            ]
        ]);
    }

    /**
     * Code examples
     */
    public function examples(): JsonResponse
    {
        return response()->json([
            'success' => true,
            'title' => 'Code Examples',
            'languages' => [
                'javascript' => [
                    'fetch_events' => [
                        'description' => 'Fetch all events',
                        'code' => 'const fetchEvents = async () => {
  const response = await fetch("http://ems-web.test/api/events", {
    method: "GET",
    headers: {
      "Authorization": "Bearer YOUR_TOKEN",
      "Content-Type": "application/json"
    }
  });
  
  const data = await response.json();
  console.log(data);
};'
                    ],
                    'create_event' => [
                        'description' => 'Create a new event',
                        'code' => 'const createEvent = async (eventData) => {
  const response = await fetch("http://ems-web.test/api/events", {
    method: "POST",
    headers: {
      "Authorization": "Bearer YOUR_TOKEN",
      "Content-Type": "application/json"
    },
    body: JSON.stringify(eventData)
  });
  
  const data = await response.json();
  return data;
};'
                    ]
                ],
                'php' => [
                    'fetch_events' => [
                        'description' => 'Fetch all events using Guzzle',
                        'code' => '<?php
use GuzzleHttp\Client;

$client = new Client();
$response = $client->get("http://ems-web.test/api/events", [
    "headers" => [
        "Authorization" => "Bearer YOUR_TOKEN",
        "Content-Type" => "application/json"
    ]
]);

$events = json_decode($response->getBody(), true);
print_r($events);'
                    ],
                    'create_event' => [
                        'description' => 'Create a new event',
                        'code' => '<?php
use GuzzleHttp\Client;

$client = new Client();
$response = $client->post("http://ems-web.test/api/events", [
    "headers" => [
        "Authorization" => "Bearer YOUR_TOKEN",
        "Content-Type" => "application/json"
    ],
    "json" => [
        "title" => "New Event",
        "description" => "Event description",
        "start_at" => "2024-03-01T09:00:00Z",
        "end_at" => "2024-03-01T17:00:00Z",
        "venue_id" => 1,
        "number_of_participants" => 50
    ]
]);

$result = json_decode($response->getBody(), true);
print_r($result);'
                    ]
                ],
                'python' => [
                    'fetch_events' => [
                        'description' => 'Fetch all events using requests',
                        'code' => 'import requests

def fetch_events():
    headers = {
        "Authorization": "Bearer YOUR_TOKEN",
        "Content-Type": "application/json"
    }
    
    response = requests.get("http://ems-web.test/api/events", headers=headers)
    events = response.json()
    print(events)
    return events'
                    ],
                    'create_event' => [
                        'description' => 'Create a new event',
                        'code' => 'import requests

def create_event(event_data):
    headers = {
        "Authorization": "Bearer YOUR_TOKEN",
        "Content-Type": "application/json"
    }
    
    response = requests.post(
        "http://ems-web.test/api/events", 
        headers=headers, 
        json=event_data
    )
    
    result = response.json()
    print(result)
    return result'
                    ]
                ]
            ]
        ]);
    }

    /**
     * SDK information
     */
    public function sdk(): JsonResponse
    {
        return response()->json([
            'success' => true,
            'title' => 'SDKs and Libraries',
            'official_sdks' => [
                'javascript' => [
                    'name' => 'Event Management JS SDK',
                    'npm_package' => '@ems/api-client',
                    'installation' => 'npm install @ems/api-client',
                    'usage' => 'import EMSClient from "@ems/api-client";\nconst client = new EMSClient("YOUR_TOKEN");'
                ],
                'php' => [
                    'name' => 'Event Management PHP SDK',
                    'composer_package' => 'ems/api-client',
                    'installation' => 'composer require ems/api-client',
                    'usage' => 'use EMS\\API\\Client;\n$client = new Client("YOUR_TOKEN");'
                ],
                'python' => [
                    'name' => 'Event Management Python SDK',
                    'pip_package' => 'ems-api-client',
                    'installation' => 'pip install ems-api-client',
                    'usage' => 'from ems_api import EMSClient\nclient = EMSClient("YOUR_TOKEN")'
                ]
            ],
            'third_party_tools' => [
                'postman' => 'Download our Postman collection for easy testing',
                'insomnia' => 'Import our OpenAPI specification into Insomnia',
                'swagger' => 'Interactive API documentation available'
            ]
        ]);
    }

    /**
     * Webhook documentation
     */
    public function webhooks(): JsonResponse
    {
        return response()->json([
            'success' => true,
            'title' => 'Webhooks Guide',
            'description' => 'Receive real-time notifications when events happen',
            'events' => [
                'event.created' => 'Triggered when a new event is created',
                'event.updated' => 'Triggered when an event is updated',
                'event.status_changed' => 'Triggered when event status changes',
                'event.deleted' => 'Triggered when an event is deleted',
                'participant.added' => 'Triggered when a participant is added',
                'participant.removed' => 'Triggered when a participant is removed',
                'finance.requested' => 'Triggered when finance request is created',
                'finance.approved' => 'Triggered when finance request is approved',
                'custodian.requested' => 'Triggered when custodian request is created',
                'custodian.approved' => 'Triggered when custodian request is approved'
            ],
            'setup' => [
                '1. Register your webhook URL',
                '2. Select the events you want to receive',
                '3. Verify your webhook endpoint',
                '4. Start receiving real-time data'
            ],
            'webhook_format' => [
                'method' => 'POST',
                'content_type' => 'application/json',
                'signature' => 'X-EMS-Signature header for verification',
                'example_payload' => [
                    'event' => 'event.created',
                    'data' => [
                        'id' => 123,
                        'title' => 'New Event',
                        'status' => 'pending_approvals'
                    ],
                    'timestamp' => '2024-02-14T11:53:00Z'
                ]
            ]
        ]);
    }

    /**
     * Support and help
     */
    public function support(): JsonResponse
    {
        return response()->json([
            'success' => true,
            'title' => 'API Support',
            'contact' => [
                'email' => 'api-support@ems-web.test',
                'documentation' => url('/API_DOCUMENTATION.md'),
                'status_page' => 'https://status.ems-web.test',
                'github_issues' => 'https://github.com/ems/api/issues'
            ],
            'resources' => [
                'api_reference' => 'Complete API documentation',
                'tutorials' => 'Step-by-step tutorials',
                'examples' => 'Code examples in multiple languages',
                'best_practices' => 'API usage guidelines',
                'changelog' => 'API updates and changes'
            ],
            'community' => [
                'discord' => 'Join our Discord community',
                'forum' => 'Developer forum',
                'blog' => 'API updates and announcements'
            ],
            'rate_limits' => [
                'standard' => '1000 requests per hour',
                'premium' => '10000 requests per hour',
                'enterprise' => 'Unlimited requests'
            ]
        ]);
    }

    /**
     * Register new application
     */
    public function register(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'app_name' => 'required|string|max:255',
            'description' => 'required|string|max:1000',
            'contact_email' => 'required|email',
            'website' => 'nullable|url',
            'callback_url' => 'nullable|url',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        // In a real implementation, you would store this in database
        $appData = [
            'app_id' => 'app_' . uniqid(),
            'app_secret' => 'secret_' . str_random(32),
            'name' => $request->app_name,
            'description' => $request->description,
            'contact_email' => $request->contact_email,
            'website' => $request->website,
            'callback_url' => $request->callback_url,
            'status' => 'active',
            'created_at' => now()
        ];

        return response()->json([
            'success' => true,
            'message' => 'Application registered successfully',
            'data' => $appData,
            'next_steps' => [
                'Save your app_id and app_secret securely',
                'Use these credentials to generate API tokens',
                'Read the documentation for implementation guidance'
            ]
        ], 201);
    }

    /**
     * Generate API token
     */
    public function generateToken(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'app_id' => 'required|string',
            'app_secret' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        // In a real implementation, you would validate credentials and create token
        $tokenData = [
            'access_token' => 'ems_' . str_random(64),
            'token_type' => 'Bearer',
            'expires_in' => 31536000, // 1 year in seconds
            'scope' => 'read write',
            'created_at' => now()
        ];

        return response()->json([
            'success' => true,
            'message' => 'Token generated successfully',
            'data' => $tokenData,
            'usage' => [
                'include_in_header' => 'Authorization: Bearer ' . $tokenData['access_token'],
                'expires_at' => now()->addYear()->toDateTimeString()
            ]
        ]);
    }
}
