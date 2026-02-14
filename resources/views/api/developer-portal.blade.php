<x-app-layout>
    <div class="min-h-screen bg-gray-50">
        <!-- Hero Section -->
        <div class="bg-gradient-to-r from-indigo-600 to-purple-600 text-white">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
                <div class="text-center">
                    <h1 class="text-4xl font-bold mb-4">Event Management API</h1>
                    <p class="text-xl mb-8">Connect your applications to our powerful event management system</p>
                    <div class="flex justify-center gap-4">
                        <a href="/api/integration/quick-start" class="bg-white text-indigo-600 px-6 py-3 rounded-lg font-semibold hover:bg-gray-100 transition">
                            Get Started
                        </a>
                        <a href="/API_DOCUMENTATION.md" class="border-2 border-white text-white px-6 py-3 rounded-lg font-semibold hover:bg-white hover:text-indigo-600 transition">
                            View Documentation
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Quick Stats -->
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 -mt-8">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
                <div class="bg-white rounded-lg shadow p-6">
                    <div class="flex items-center">
                        <div class="p-3 bg-indigo-100 rounded-lg">
                            <svg class="w-6 h-6 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm text-gray-600">Events</p>
                            <p class="text-2xl font-semibold">Full CRUD</p>
                        </div>
                    </div>
                </div>
                
                <div class="bg-white rounded-lg shadow p-6">
                    <div class="flex items-center">
                        <div class="p-3 bg-green-100 rounded-lg">
                            <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm text-gray-600">Finance</p>
                            <p class="text-2xl font-semibold">Budget API</p>
                        </div>
                    </div>
                </div>
                
                <div class="bg-white rounded-lg shadow p-6">
                    <div class="flex items-center">
                        <div class="p-3 bg-purple-100 rounded-lg">
                            <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                            </svg>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm text-gray-600">Logistics</p>
                            <p class="text-2xl font-semibold">Resources</p>
                        </div>
                    </div>
                </div>
                
                <div class="bg-white rounded-lg shadow p-6">
                    <div class="flex items-center">
                        <div class="p-3 bg-orange-100 rounded-lg">
                            <svg class="w-6 h-6 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                            </svg>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm text-gray-600">Real-time</p>
                            <p class="text-2xl font-semibold">Webhooks</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Getting Started -->
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-12">
                <!-- Quick Start Guide -->
                <div>
                    <h2 class="text-2xl font-bold text-gray-900 mb-6">Quick Start Guide</h2>
                    <div class="space-y-6">
                        <div class="flex items-start">
                            <div class="flex-shrink-0 w-8 h-8 bg-indigo-600 text-white rounded-full flex items-center justify-center font-semibold">1</div>
                            <div class="ml-4">
                                <h3 class="font-semibold text-gray-900">Register Your Application</h3>
                                <p class="text-gray-600 mt-1">Get your API credentials by registering your application</p>
                                <div class="mt-2">
                                    <button onclick="showRegisterModal()" class="text-indigo-600 hover:text-indigo-800 font-medium">Register Now →</button>
                                </div>
                            </div>
                        </div>
                        
                        <div class="flex items-start">
                            <div class="flex-shrink-0 w-8 h-8 bg-indigo-600 text-white rounded-full flex items-center justify-center font-semibold">2</div>
                            <div class="ml-4">
                                <h3 class="font-semibold text-gray-900">Generate API Token</h3>
                                <p class="text-gray-600 mt-1">Create a secure token for authenticating your requests</p>
                                <div class="mt-2">
                                    <button onclick="showTokenModal()" class="text-indigo-600 hover:text-indigo-800 font-medium">Generate Token →</button>
                                </div>
                            </div>
                        </div>
                        
                        <div class="flex items-start">
                            <div class="flex-shrink-0 w-8 h-8 bg-indigo-600 text-white rounded-full flex items-center justify-center font-semibold">3</div>
                            <div class="ml-4">
                                <h3 class="font-semibold text-gray-900">Make Your First Request</h3>
                                <p class="text-gray-600 mt-1">Test your connection by fetching events data</p>
                                <div class="mt-2">
                                    <a href="/api/integration/examples" class="text-indigo-600 hover:text-indigo-800 font-medium">View Examples →</a>
                                </div>
                            </div>
                        </div>
                        
                        <div class="flex items-start">
                            <div class="flex-shrink-0 w-8 h-8 bg-indigo-600 text-white rounded-full flex items-center justify-center font-semibold">4</div>
                            <div class="ml-4">
                                <h3 class="font-semibold text-gray-900">Build Your Integration</h4>
                                <p class="text-gray-600 mt-1">Use our SDKs and documentation to build your application</p>
                                <div class="mt-2">
                                    <a href="/api/integration/sdk" class="text-indigo-600 hover:text-indigo-800 font-medium">Browse SDKs →</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Code Example -->
                <div>
                    <h2 class="text-2xl font-bold text-gray-900 mb-6">Try It Now</h2>
                    <div class="bg-gray-900 rounded-lg p-6">
                        <div class="flex items-center justify-between mb-4">
                            <div class="flex space-x-2">
                                <button onclick="switchLanguage('javascript')" class="language-tab px-3 py-1 text-sm rounded bg-gray-700 text-white">JavaScript</button>
                                <button onclick="switchLanguage('php')" class="language-tab px-3 py-1 text-sm rounded text-gray-400">PHP</button>
                                <button onclick="switchLanguage('python')" class="language-tab px-3 py-1 text-sm rounded text-gray-400">Python</button>
                            </div>
                            <button onclick="copyCode()" class="text-gray-400 hover:text-white">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z" />
                                </svg>
                            </button>
                        </div>
                        <pre id="code-example" class="text-sm text-gray-300 overflow-x-auto"><code>// Fetch events using JavaScript
const fetchEvents = async () => {
  const response = await fetch("http://ems-web.test/api/events", {
    method: "GET",
    headers: {
      "Authorization": "Bearer YOUR_TOKEN_HERE",
      "Content-Type": "application/json"
    }
  });
  
  const data = await response.json();
  console.log(data);
  return data;
};

// Create a new event
const createEvent = async (eventData) => {
  const response = await fetch("http://ems-web.test/api/events", {
    method: "POST",
    headers: {
      "Authorization": "Bearer YOUR_TOKEN_HERE",
      "Content-Type": "application/json"
    },
    body: JSON.stringify({
      title: "My API Event",
      description: "Created via API",
      start_at: "2024-03-01T09:00:00Z",
      end_at: "2024-03-01T17:00:00Z",
      venue_id: 1,
      number_of_participants: 50
    })
  });
  
  return await response.json();
};</code></pre>
                    </div>
                    
                    <div class="mt-6 p-4 bg-blue-50 rounded-lg">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <svg class="w-5 h-5 text-blue-400" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
                                </svg>
                            </div>
                            <div class="ml-3">
                                <p class="text-sm text-blue-800">
                                    <strong>Pro Tip:</strong> Replace <code class="bg-blue-100 px-1 rounded">YOUR_TOKEN_HERE</code> with your actual API token from step 2.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- API Endpoints -->
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
            <h2 class="text-2xl font-bold text-gray-900 mb-6">Available Endpoints</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <div class="bg-white rounded-lg shadow p-6">
                    <div class="flex items-center mb-4">
                        <div class="p-2 bg-indigo-100 rounded-lg">
                            <svg class="w-6 h-6 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                        </div>
                        <h3 class="ml-3 font-semibold text-gray-900">Events</h3>
                    </div>
                    <ul class="space-y-2 text-sm text-gray-600">
                        <li><code class="bg-gray-100 px-1 rounded">GET /api/events</code> - List events</li>
                        <li><code class="bg-gray-100 px-1 rounded">POST /api/events</code> - Create event</li>
                        <li><code class="bg-gray-100 px-1 rounded">PUT /api/events/{id}</code> - Update event</li>
                        <li><code class="bg-gray-100 px-1 rounded">DELETE /api/events/{id}</code> - Delete event</li>
                    </ul>
                </div>

                <div class="bg-white rounded-lg shadow p-6">
                    <div class="flex items-center mb-4">
                        <div class="p-2 bg-green-100 rounded-lg">
                            <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <h3 class="ml-3 font-semibold text-gray-900">Finance</h3>
                    </div>
                    <ul class="space-y-2 text-sm text-gray-600">
                        <li><code class="bg-gray-100 px-1 rounded">GET /api/events/{id}/finance</code> - Get budget</li>
                        <li><code class="bg-gray-100 px-1 rounded">POST /api/events/{id}/finance</code> - Create budget</li>
                        <li><code class="bg-gray-100 px-1 rounded">PATCH /api/finance/{id}/status</code> - Approve</li>
                    </ul>
                </div>

                <div class="bg-white rounded-lg shadow p-6">
                    <div class="flex items-center mb-4">
                        <div class="p-2 bg-purple-100 rounded-lg">
                            <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                            </svg>
                        </div>
                        <h3 class="ml-3 font-semibold text-gray-900">Logistics</h3>
                    </div>
                    <ul class="space-y-2 text-sm text-gray-600">
                        <li><code class="bg-gray-100 px-1 rounded">GET /api/events/{id}/logistics</code> - List items</li>
                        <li><code class="bg-gray-100 px-1 rounded">POST /api/events/{id}/logistics</code> - Add item</li>
                        <li><code class="bg-gray-100 px-1 rounded">GET /api/logistics/resources</code> - Resources</li>
                    </ul>
                </div>
            </div>
        </div>

        <!-- Support Section -->
        <div class="bg-white border-t">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
                <div class="text-center">
                    <h2 class="text-2xl font-bold text-gray-900 mb-4">Need Help?</h2>
                    <p class="text-gray-600 mb-8">Our developer support team is here to help you succeed</p>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <div>
                            <div class="flex justify-center mb-4">
                                <div class="p-3 bg-indigo-100 rounded-full">
                                    <svg class="w-6 h-6 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                                    </svg>
                                </div>
                            </div>
                            <h3 class="font-semibold text-gray-900 mb-2">Documentation</h3>
                            <p class="text-gray-600 text-sm mb-4">Comprehensive guides and API reference</p>
                            <a href="/API_DOCUMENTATION.md" class="text-indigo-600 hover:text-indigo-800 font-medium text-sm">Read Docs →</a>
                        </div>
                        
                        <div>
                            <div class="flex justify-center mb-4">
                                <div class="p-3 bg-green-100 rounded-full">
                                    <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                                    </svg>
                                </div>
                            </div>
                            <h3 class="font-semibold text-gray-900 mb-2">Community</h3>
                            <p class="text-gray-600 text-sm mb-4">Join our developer community</p>
                            <a href="/api/integration/support" class="text-indigo-600 hover:text-indigo-800 font-medium text-sm">Join Community →</a>
                        </div>
                        
                        <div>
                            <div class="flex justify-center mb-4">
                                <div class="p-3 bg-purple-100 rounded-full">
                                    <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 5.636l-3.536 3.536m0 5.656l3.536 3.536M9.172 9.172L5.636 5.636m3.536 9.192l-3.536 3.536M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-5 0a4 4 0 11-8 0 4 4 0 018 0z" />
                                    </svg>
                                </div>
                            </div>
                            <h3 class="font-semibold text-gray-900 mb-2">Support</h3>
                            <p class="text-gray-600 text-sm mb-4">Get help from our support team</p>
                            <a href="/api/integration/support" class="text-indigo-600 hover:text-indigo-800 font-medium text-sm">Contact Support →</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modals -->
    <div id="registerModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden overflow-y-auto h-full w-full z-50">
        <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-lg bg-white">
            <div class="mt-3">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Register Your Application</h3>
                <form id="registerForm">
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Application Name</label>
                        <input type="text" name="app_name" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500">
                    </div>
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Description</label>
                        <textarea name="description" required rows="3" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500"></textarea>
                    </div>
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Contact Email</label>
                        <input type="email" name="contact_email" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500">
                    </div>
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Website (Optional)</label>
                        <input type="url" name="website" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500">
                    </div>
                    <div class="flex justify-end space-x-3">
                        <button type="button" onclick="closeRegisterModal()" class="px-4 py-2 bg-gray-200 text-gray-800 rounded-lg hover:bg-gray-300">Cancel</button>
                        <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700">Register</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div id="tokenModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden overflow-y-auto h-full w-full z-50">
        <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-lg bg-white">
            <div class="mt-3">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Generate API Token</h3>
                <form id="tokenForm">
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">App ID</label>
                        <input type="text" name="app_id" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500">
                    </div>
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">App Secret</label>
                        <input type="password" name="app_secret" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500">
                    </div>
                    <div class="flex justify-end space-x-3">
                        <button type="button" onclick="closeTokenModal()" class="px-4 py-2 bg-gray-200 text-gray-800 rounded-lg hover:bg-gray-300">Cancel</button>
                        <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700">Generate Token</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        // Code examples for different languages
        const codeExamples = {
            javascript: `// Fetch events using JavaScript
const fetchEvents = async () => {
  const response = await fetch("http://ems-web.test/api/events", {
    method: "GET",
    headers: {
      "Authorization": "Bearer YOUR_TOKEN_HERE",
      "Content-Type": "application/json"
    }
  });
  
  const data = await response.json();
  console.log(data);
  return data;
};

// Create a new event
const createEvent = async (eventData) => {
  const response = await fetch("http://ems-web.test/api/events", {
    method: "POST",
    headers: {
      "Authorization": "Bearer YOUR_TOKEN_HERE",
      "Content-Type": "application/json"
    },
    body: JSON.stringify({
      title: "My API Event",
      description: "Created via API",
      start_at: "2024-03-01T09:00:00Z",
      end_at: "2024-03-01T17:00:00Z",
      venue_id: 1,
      number_of_participants: 50
    })
  });
  
  return await response.json();
};`,
            php: `{{ '<?php' }}
// Fetch events using PHP and Guzzle
use GuzzleHttp\\Client;

$client = new Client();
$response = $client->get("http://ems-web.test/api/events", [
    "headers" => [
        "Authorization" => "Bearer YOUR_TOKEN_HERE",
        "Content-Type" => "application/json"
    ]
]);

$events = json_decode($response->getBody(), true);
print_r($events);

// Create a new event
$response = $client->post("http://ems-web.test/api/events", [
    "headers" => [
        "Authorization" => "Bearer YOUR_TOKEN_HERE",
        "Content-Type" => "application/json"
    ],
    "json" => [
        "title" => "My API Event",
        "description" => "Created via API",
        "start_at" => "2024-03-01T09:00:00Z",
        "end_at" => "2024-03-01T17:00:00Z",
        "venue_id" => 1,
        "number_of_participants" => 50
    ]
]);

$result = json_decode($response->getBody(), true);
print_r($result);`,
            python: `# Fetch events using Python
import requests

def fetch_events():
    headers = {
        "Authorization": "Bearer YOUR_TOKEN_HERE",
        "Content-Type": "application/json"
    }
    
    response = requests.get("http://ems-web.test/api/events", headers=headers)
    events = response.json()
    print(events)
    return events

# Create a new event
def create_event():
    headers = {
        "Authorization": "Bearer YOUR_TOKEN_HERE",
        "Content-Type": "application/json"
    }
    
    data = {
        "title": "My API Event",
        "description": "Created via API",
        "start_at": "2024-03-01T09:00:00Z",
        "end_at": "2024-03-01T17:00:00Z",
        "venue_id": 1,
        "number_of_participants": 50
    }
    
    response = requests.post(
        "http://ems-web.test/api/events", 
        headers=headers, 
        json=data
    )
    
    result = response.json()
    print(result)
    return result`
        };

        function switchLanguage(language) {
            document.getElementById('code-example').textContent = codeExamples[language];
            
            // Update tab styles
            document.querySelectorAll('.language-tab').forEach(tab => {
                tab.classList.remove('bg-gray-700', 'text-white');
                tab.classList.add('text-gray-400');
            });
            event.target.classList.remove('text-gray-400');
            event.target.classList.add('bg-gray-700', 'text-white');
        }

        function copyCode() {
            const code = document.getElementById('code-example').textContent;
            navigator.clipboard.writeText(code).then(() => {
                // Show success message
                const button = event.target.closest('button');
                const originalHTML = button.innerHTML;
                button.innerHTML = '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>';
                setTimeout(() => {
                    button.innerHTML = originalHTML;
                }, 2000);
            });
        }

        function showRegisterModal() {
            document.getElementById('registerModal').classList.remove('hidden');
        }

        function closeRegisterModal() {
            document.getElementById('registerModal').classList.add('hidden');
        }

        function showTokenModal() {
            document.getElementById('tokenModal').classList.remove('hidden');
        }

        function closeTokenModal() {
            document.getElementById('tokenModal').classList.add('hidden');
        }

        // Handle form submissions
        document.getElementById('registerForm').addEventListener('submit', async (e) => {
            e.preventDefault();
            const formData = new FormData(e.target);
            const data = Object.fromEntries(formData);
            
            try {
                const response = await fetch('/api/integration/register', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify(data)
                });
                
                const result = await response.json();
                
                if (result.success) {
                    alert('Application registered successfully! Save your credentials:\n\nApp ID: ' + result.data.app_id + '\nApp Secret: ' + result.data.app_secret);
                    closeRegisterModal();
                    e.target.reset();
                } else {
                    alert('Registration failed: ' + result.message);
                }
            } catch (error) {
                alert('Error: ' + error.message);
            }
        });

        document.getElementById('tokenForm').addEventListener('submit', async (e) => {
            e.preventDefault();
            const formData = new FormData(e.target);
            const data = Object.fromEntries(formData);
            
            try {
                const response = await fetch('/api/integration/token', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify(data)
                });
                
                const result = await response.json();
                
                if (result.success) {
                    alert('Token generated successfully!\n\nAccess Token: ' + result.data.access_token + '\n\nKeep this token secure!');
                    closeTokenModal();
                    e.target.reset();
                } else {
                    alert('Token generation failed: ' + result.message);
                }
            } catch (error) {
                alert('Error: ' + error.message);
            }
        });
    </script>
</x-app-layout>
